<?php
// api/products.php - Product catalogue endpoints (list + create)
require_once __DIR__ . '/../config/config.php';
require_login();

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

function respond($payload, int $code = 200): void {
    http_response_code($code);
    echo json_encode($payload);
    exit;
}

function current_store_id(): int {
    $user = current_user();
    if ($user && !empty($user['store_id'])) {
        return (int)$user['store_id'];
    }
    return 1;
}

/**
 * Generate a unique 13-digit EAN-style barcode that is not already used.
 */
function generate_barcode(PDO $pdo): string {
    do {
        // 13 digits: "200" prefix (in-store use) + 9 random digits + check digit.
        $body = '200' . str_pad((string)random_int(0, 999999999), 9, '0', STR_PAD_LEFT);
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $digit = (int)$body[$i];
            $sum += ($i % 2 === 0) ? $digit : $digit * 3;
        }
        $check = (10 - ($sum % 10)) % 10;
        $barcode = $body . $check;

        $chk = $pdo->prepare("SELECT product_id FROM Product WHERE barcode = ?");
        $chk->execute([$barcode]);
    } while ($chk->fetch());

    return $barcode;
}

try {
    switch ($action) {
        // ---- List products (with aggregated stock) -------------------------
        case 'list':
            $store_id = isset($_GET['store_id']) && $_GET['store_id'] !== ''
                ? (int)$_GET['store_id']
                : current_store_id();
            $search   = trim($_GET['q'] ?? '');
            $category = trim($_GET['category'] ?? '');

            $sql = "SELECT p.product_id, p.store_id, p.name, p.description, p.category, p.barcode,
                           p.cost_price, p.selling_price, p.reorder_level, p.is_perishable,
                           COALESCE(s.quantity, 0) AS quantity,
                           s.location_code
                      FROM Product p
                      LEFT JOIN Stock s ON p.product_id = s.product_id
                     WHERE p.store_id = ?";
            $params = [$store_id];
            if ($search !== '') {
                $sql .= " AND (p.name LIKE ? OR p.barcode LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }
            if ($category !== '') {
                $sql .= " AND p.category = ?";
                $params[] = $category;
            }
            $sql .= " ORDER BY p.name ASC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            respond(['status' => 'success', 'data' => $stmt->fetchAll()]);

        // ---- Create a product (optionally with initial stock) --------------
        case 'create':
            if ($method !== 'POST') {
                respond(['status' => 'error', 'message' => 'Method not allowed'], 405);
            }
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

            $store_id     = isset($input['store_id']) && $input['store_id'] !== ''
                ? (int)$input['store_id']
                : current_store_id();
            $name         = trim($input['name'] ?? '');
            $description  = trim($input['description'] ?? '');
            $category     = trim($input['category'] ?? '');
            $barcode      = trim($input['barcode'] ?? '');
            $cost_price   = (float)($input['cost_price'] ?? 0);
            $selling_price= (float)($input['selling_price'] ?? 0);
            $reorder_level= (int)($input['reorder_level'] ?? 0);
            $is_perishable= !empty($input['is_perishable']) ? 1 : 0;
            $quantity     = (int)($input['quantity'] ?? 0);
            $location     = trim($input['location_code'] ?? '');

            if ($name === '') {
                respond(['status' => 'error', 'message' => 'Product name is required'], 422);
            }

            // Verify store exists.
            $chk = $pdo->prepare("SELECT store_id FROM Store WHERE store_id = ?");
            $chk->execute([$store_id]);
            if (!$chk->fetch()) {
                respond(['status' => 'error', 'message' => 'Invalid store'], 422);
            }

            // Reject duplicate product names within the same store.
            $dup = $pdo->prepare("SELECT product_id FROM Product WHERE store_id = ? AND LOWER(name) = LOWER(?)");
            $dup->execute([$store_id, $name]);
            if ($dup->fetch()) {
                respond([
                    'status'  => 'error',
                    'message' => 'A product named "' . $name . '" already exists in this store',
                    'code'    => 'DUPLICATE_NAME',
                ], 409);
            }

            // Auto-generate a unique barcode when none is supplied.
            $generated = false;
            if ($barcode === '') {
                $barcode = generate_barcode($pdo);
                $generated = true;
            } else {
                // Ensure a supplied barcode is unique.
                $bc = $pdo->prepare("SELECT product_id FROM Product WHERE barcode = ?");
                $bc->execute([$barcode]);
                if ($bc->fetch()) {
                    respond([
                        'status'  => 'error',
                        'message' => 'Barcode "' . $barcode . '" is already in use',
                        'code'    => 'DUPLICATE_BARCODE',
                    ], 409);
                }
            }

            $pdo->beginTransaction();
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO Product
                        (store_id, name, description, category, barcode, cost_price, selling_price, reorder_level, is_perishable)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $store_id, $name, $description ?: null, $category ?: null, $barcode,
                    $cost_price, $selling_price, $reorder_level, $is_perishable
                ]);
                $product_id = (int)$pdo->lastInsertId();

                // Create stock row.
                $sstmt = $pdo->prepare("
                    INSERT INTO Stock (product_id, quantity, minimum_level, maximum_level, location_code)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $sstmt->execute([
                    $product_id,
                    $quantity,
                    $reorder_level,
                    max($reorder_level * 3, $quantity, 999),
                    $location ?: null
                ]);

                // If already at/below reorder level, raise an alert.
                if ($quantity <= $reorder_level) {
                    $severity = $quantity <= 0 ? 'CRITICAL' : ($quantity <= $reorder_level * 0.5 ? 'HIGH' : 'MEDIUM');
                    $pdo->prepare("
                        INSERT INTO LowStockAlert (product_id, current_quantity, reorder_level, status, severity)
                        VALUES (?, ?, ?, 'PENDING', ?)
                    ")->execute([$product_id, $quantity, $reorder_level, $severity]);
                }

                $pdo->commit();
            } catch (Exception $e) {
                $pdo->rollBack();
                respond(['status' => 'error', 'message' => 'Create failed: ' . $e->getMessage()], 500);
            }

            respond([
                'status'     => 'success',
                'message'    => 'Product added',
                'product_id' => $product_id,
                'barcode'    => $barcode,
                'generated'  => $generated,
            ], 201);

        // ---- Update an existing product (and its stock reorder level) -----
        case 'update':
            if ($method !== 'POST') {
                respond(['status' => 'error', 'message' => 'Method not allowed'], 405);
            }
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

            $product_id   = (int)($input['product_id'] ?? 0);
            $name         = trim($input['name'] ?? '');
            $description  = trim($input['description'] ?? '');
            $category     = trim($input['category'] ?? '');
            $barcode      = trim($input['barcode'] ?? '');
            $cost_price   = (float)($input['cost_price'] ?? 0);
            $selling_price= (float)($input['selling_price'] ?? 0);
            $reorder_level= (int)($input['reorder_level'] ?? 0);
            $is_perishable= !empty($input['is_perishable']) ? 1 : 0;

            if ($product_id <= 0) {
                respond(['status' => 'error', 'message' => 'Invalid product id'], 422);
            }
            if ($name === '') {
                respond(['status' => 'error', 'message' => 'Product name is required'], 422);
            }

            $chk = $pdo->prepare("SELECT product_id, store_id, barcode FROM Product WHERE product_id = ?");
            $chk->execute([$product_id]);
            $existing = $chk->fetch();
            if (!$existing) {
                respond(['status' => 'error', 'message' => 'Product not found'], 404);
            }
            $store_id = (int)$existing['store_id'];

            // Reject duplicate names (ignore this product itself).
            $dup = $pdo->prepare("SELECT product_id FROM Product WHERE store_id = ? AND LOWER(name) = LOWER(?) AND product_id <> ?");
            $dup->execute([$store_id, $name, $product_id]);
            if ($dup->fetch()) {
                respond([
                    'status'  => 'error',
                    'message' => 'A product named "' . $name . '" already exists in this store',
                    'code'    => 'DUPLICATE_NAME',
                ], 409);
            }

            // Keep the existing barcode if the field was left blank.
            if ($barcode === '') {
                $barcode = $existing['barcode'] ?: generate_barcode($pdo);
            } else {
                $bc = $pdo->prepare("SELECT product_id FROM Product WHERE barcode = ? AND product_id <> ?");
                $bc->execute([$barcode, $product_id]);
                if ($bc->fetch()) {
                    respond([
                        'status'  => 'error',
                        'message' => 'Barcode "' . $barcode . '" is already in use',
                        'code'    => 'DUPLICATE_BARCODE',
                    ], 409);
                }
            }

            $pdo->prepare("
                UPDATE Product
                   SET name = ?, description = ?, category = ?, barcode = ?,
                       cost_price = ?, selling_price = ?, reorder_level = ?, is_perishable = ?
                 WHERE product_id = ?
            ")->execute([
                $name, $description ?: null, $category ?: null, $barcode,
                $cost_price, $selling_price, $reorder_level, $is_perishable, $product_id
            ]);

            // Keep stock.minimum_level in sync with the product reorder level,
            // and re-evaluate any low-stock alert for this product.
            $pdo->prepare("UPDATE Stock SET minimum_level = ? WHERE product_id = ?")->execute([$reorder_level, $product_id]);

            $srow = $pdo->prepare("SELECT quantity FROM Stock WHERE product_id = ?");
            $srow->execute([$product_id]);
            $qty = (int)$srow->fetchColumn();
            if ($qty <= $reorder_level) {
                $severity = $qty <= 0 ? 'CRITICAL' : ($qty <= $reorder_level * 0.5 ? 'HIGH' : 'MEDIUM');
                $pdo->prepare("UPDATE LowStockAlert SET status='RESOLVED', resolved_at=NOW() WHERE product_id=? AND status='PENDING'")->execute([$product_id]);
                $pdo->prepare("INSERT INTO LowStockAlert (product_id, current_quantity, reorder_level, status, severity) VALUES (?, ?, ?, 'PENDING', ?)")->execute([$product_id, $qty, $reorder_level, $severity]);
            } else {
                $pdo->prepare("UPDATE LowStockAlert SET status='RESOLVED', resolved_at=NOW() WHERE product_id=? AND status='PENDING'")->execute([$product_id]);
            }

            respond(['status' => 'success', 'message' => 'Product updated']);

        // ---- Delete a product (and its stock, sales items, alerts) --------
        case 'delete':
            if ($method !== 'POST') {
                respond(['status' => 'error', 'message' => 'Method not allowed'], 405);
            }
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $product_id = (int)($input['product_id'] ?? 0);
            if ($product_id <= 0) {
                respond(['status' => 'error', 'message' => 'Invalid product id'], 422);
            }

            $chk = $pdo->prepare("SELECT product_id FROM Product WHERE product_id = ?");
            $chk->execute([$product_id]);
            if (!$chk->fetch()) {
                respond(['status' => 'error', 'message' => 'Product not found'], 404);
            }

            $pdo->beginTransaction();
            try {
                $pdo->prepare("DELETE FROM SalesItem WHERE product_id = ?")->execute([$product_id]);
                $pdo->prepare("DELETE FROM LowStockAlert WHERE product_id = ?")->execute([$product_id]);
                $pdo->prepare("DELETE FROM Stock WHERE product_id = ?")->execute([$product_id]);
                $pdo->prepare("DELETE FROM Product WHERE product_id = ?")->execute([$product_id]);
                $pdo->commit();
            } catch (Exception $e) {
                $pdo->rollBack();
                respond(['status' => 'error', 'message' => 'Delete failed: ' . $e->getMessage()], 500);
            }

            respond(['status' => 'success', 'message' => 'Product removed']);

        default:
            respond(['status' => 'error', 'message' => 'Invalid action'], 400);
    }
} catch (Exception $e) {
    respond(['status' => 'error', 'message' => $e->getMessage()], 500);
}