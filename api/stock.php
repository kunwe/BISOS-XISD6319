<?php
// api/stock.php - Stock endpoints (list + create/update)
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

try {
    switch ($action) {
        // ---- List stock for a store ---------------------------------------
        case 'list':
            $store_id = isset($_GET['store_id']) && $_GET['store_id'] !== ''
                ? (int)$_GET['store_id']
                : current_store_id();
            $search   = trim($_GET['q'] ?? '');
            $category = trim($_GET['category'] ?? '');

            $sql = "SELECT p.product_id, p.name, p.category, p.reorder_level,
                           s.stock_id, s.quantity, s.minimum_level, s.maximum_level,
                           s.location_code, s.last_count_date
                      FROM Product p
                      JOIN Stock s ON p.product_id = s.product_id
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
            $rows = $stmt->fetchAll();

            // Annotate status.
            foreach ($rows as &$r) {
                $qty = (int)$r['quantity'];
                $min = (int)($r['minimum_level'] ?: $r['reorder_level'] ?: 0);
                if ($qty <= 0) {
                    $r['status'] = 'OUT';
                } elseif ($qty <= $min) {
                    $r['status'] = 'LOW';
                } else {
                    $r['status'] = 'OK';
                }
            }
            unset($r);
            respond(['status' => 'success', 'data' => $rows]);

        // ---- Create stock for an existing product -------------------------
        case 'create':
            if ($method !== 'POST') {
                respond(['status' => 'error', 'message' => 'Method not allowed'], 405);
            }
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

            $product_id = (int)($input['product_id'] ?? 0);
            $quantity   = (int)($input['quantity'] ?? 0);
            $min_level  = (int)($input['minimum_level'] ?? 0);
            $max_level  = (int)($input['maximum_level'] ?? 999);
            $location   = trim($input['location_code'] ?? '');

            if ($product_id <= 0) {
                respond(['status' => 'error', 'message' => 'Product is required'], 422);
            }

            // Verify product exists.
            $chk = $pdo->prepare("SELECT product_id FROM Product WHERE product_id = ?");
            $chk->execute([$product_id]);
            if (!$chk->fetch()) {
                respond(['status' => 'error', 'message' => 'Product not found'], 404);
            }

            // Upsert: insert or update existing stock row.
            $exists = $pdo->prepare("SELECT stock_id FROM Stock WHERE product_id = ?");
            $exists->execute([$product_id]);
            $existing = $exists->fetch();

            if ($existing) {
                $pdo->prepare("
                    UPDATE Stock
                       SET quantity = quantity + ?, minimum_level = ?, maximum_level = ?, location_code = ?
                     WHERE stock_id = ?
                ")->execute([$quantity, $min_level, $max_level, $location ?: null, $existing['stock_id']]);
                // Clear any pending low-stock alert if now above minimum.
                $pdo->prepare("
                    UPDATE LowStockAlert SET status = 'RESOLVED', resolved_at = NOW()
                     WHERE product_id = ? AND status = 'PENDING'
                ")->execute([$product_id]);
            } else {
                $pdo->prepare("
                    INSERT INTO Stock (product_id, quantity, minimum_level, maximum_level, location_code)
                    VALUES (?, ?, ?, ?, ?)
                ")->execute([$product_id, $quantity, $min_level, $max_level, $location ?: null]);
            }

            respond([
                'status'  => 'success',
                'message' => 'Stock updated',
            ], 201);

        // ---- Update an existing stock row (set values) --------------------
        case 'update':
            if ($method !== 'POST') {
                respond(['status' => 'error', 'message' => 'Method not allowed'], 405);
            }
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

            $stock_id   = (int)($input['stock_id'] ?? 0);
            $quantity   = (int)($input['quantity'] ?? 0);
            $min_level  = (int)($input['minimum_level'] ?? 0);
            $max_level  = (int)($input['maximum_level'] ?? 999);
            $location   = trim($input['location_code'] ?? '');

            if ($stock_id <= 0) {
                respond(['status' => 'error', 'message' => 'Invalid stock id'], 422);
            }

            $pdo->prepare("
                UPDATE Stock
                   SET quantity = ?, minimum_level = ?, maximum_level = ?, location_code = ?, last_count_date = CURDATE()
                 WHERE stock_id = ?
            ")->execute([$quantity, $min_level, $max_level, $location ?: null, $stock_id]);

            // Sync low-stock alert with new quantity.
            $prod = $pdo->prepare("SELECT product_id FROM Stock WHERE stock_id = ?");
            $prod->execute([$stock_id]);
            $pid = $prod->fetchColumn();

            if ($pid) {
                $reord = $pdo->prepare("SELECT reorder_level FROM Product WHERE product_id = ?");
                $reord->execute([$pid]);
                $reorder = (int)$reord->fetchColumn();

                if ($quantity <= $reorder) {
                    $severity = $quantity <= 0 ? 'CRITICAL' : ($quantity <= $reorder * 0.5 ? 'HIGH' : 'MEDIUM');
                    // Resolve old then insert new.
                    $pdo->prepare("UPDATE LowStockAlert SET status = 'RESOLVED', resolved_at = NOW() WHERE product_id = ? AND status = 'PENDING'")->execute([$pid]);
                    $pdo->prepare("INSERT INTO LowStockAlert (product_id, current_quantity, reorder_level, status, severity) VALUES (?, ?, ?, 'PENDING', ?)")->execute([$pid, $quantity, $reorder, $severity]);
                } else {
                    $pdo->prepare("UPDATE LowStockAlert SET status = 'RESOLVED', resolved_at = NOW() WHERE product_id = ? AND status = 'PENDING'")->execute([$pid]);
                }
            }

            respond(['status' => 'success', 'message' => 'Stock updated']);

        // ---- Delete a stock row (keep the product, remove its stock) -----
        case 'delete':
            if ($method !== 'POST') {
                respond(['status' => 'error', 'message' => 'Method not allowed'], 405);
            }
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $stock_id = (int)($input['stock_id'] ?? 0);
            if ($stock_id <= 0) {
                respond(['status' => 'error', 'message' => 'Invalid stock id'], 422);
            }

            // Resolve any pending low-stock alert for the owning product.
            $prod = $pdo->prepare("SELECT product_id FROM Stock WHERE stock_id = ?");
            $prod->execute([$stock_id]);
            $pid = $prod->fetchColumn();

            $pdo->prepare("DELETE FROM Stock WHERE stock_id = ?")->execute([$stock_id]);

            if ($pid) {
                $pdo->prepare("UPDATE LowStockAlert SET status='RESOLVED', resolved_at=NOW() WHERE product_id=? AND status='PENDING'")->execute([$pid]);
            }

            respond(['status' => 'success', 'message' => 'Stock removed']);

        default:
            respond(['status' => 'error', 'message' => 'Invalid action'], 400);
    }
} catch (Exception $e) {
    respond(['status' => 'error', 'message' => $e->getMessage()], 500);
}