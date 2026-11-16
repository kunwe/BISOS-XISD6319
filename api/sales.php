<?php
// api/sales.php - Sales (POS) endpoints for BISOS
// Uses config/config.php so require_login() and helpers are available.
require_once __DIR__ . '/../config/config.php';
require_login();

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

/**
 * Resolve the active store id from the session user, with optional override.
 */
function current_store_id(): int {
    $user = current_user();
    if ($user && !empty($user['store_id'])) {
        return (int)$user['store_id'];
    }
    // Fallback to the first store if the user has none assigned.
    return 1;
}

function respond($payload, int $code = 200): void {
    http_response_code($code);
    echo json_encode($payload);
    exit;
}

try {
    switch ($action) {
        // ---- Read: products with stock for a store --------------------------
        case 'products':
            $store_id = isset($_GET['store_id']) && $_GET['store_id'] !== ''
                ? (int)$_GET['store_id']
                : null;
            $search   = trim($_GET['q'] ?? '');
            $category = trim($_GET['category'] ?? '');

            $sql = "SELECT p.product_id, p.store_id, p.name, p.category, p.barcode,
                           p.cost_price, p.selling_price, p.reorder_level, p.is_perishable,
                           s.stock_id, COALESCE(s.quantity, 0) AS quantity,
                           s.minimum_level, s.maximum_level, s.location_code,
                           st.name AS store_name
                      FROM Product p
                      LEFT JOIN Stock s ON p.product_id = s.product_id
                      JOIN Store st ON p.store_id = st.store_id
                     WHERE 1=1";
            $params = [];
            if ($store_id) {
                $sql .= " AND p.store_id = ?";
                $params[] = $store_id;
            }
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
            $products = $stmt->fetchAll();

            // Annotate with status for convenience.
            foreach ($products as &$prod) {
                $qty = (int)$prod['quantity'];
                $reorder = (int)($prod['reorder_level'] ?: $prod['minimum_level'] ?: 0);
                if ($qty <= 0) {
                    $prod['status'] = 'OUT';
                } elseif ($qty <= $reorder) {
                    $prod['status'] = 'LOW';
                } else {
                    $prod['status'] = 'OK';
                }
            }
            unset($prod);

            respond(['status' => 'success', 'data' => $products]);

        // ---- Read: store list (for store filter / POS) ----------------------
        case 'stores':
            $stmt = $pdo->query("SELECT store_id, name FROM Store WHERE is_active = TRUE ORDER BY name ASC");
            respond(['status' => 'success', 'data' => $stmt->fetchAll()]);

        // ---- Write: record a sale + deduct stock ---------------------------
        case 'create':
            if ($method !== 'POST') {
                respond(['status' => 'error', 'message' => 'Method not allowed'], 405);
            }

            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

            $store_id    = isset($input['store_id']) ? (int)$input['store_id'] : current_store_id();
            $payment     = strtoupper(trim($input['payment_method'] ?? 'CASH'));
            $customer    = trim($input['customer_name'] ?? '');
            $processed   = current_user()['user_id'] ?? null;
            $items       = $input['items'] ?? [];

            if (!in_array($payment, ['CASH', 'CARD', 'MOBILE', 'CREDIT'], true)) {
                respond(['status' => 'error', 'message' => 'Invalid payment method'], 422);
            }
            if (empty($items) || !is_array($items)) {
                respond(['status' => 'error', 'message' => 'No items in cart'], 422);
            }

            // Load products to validate prices & stock, grouped by product.
            $product_ids = [];
            foreach ($items as $it) {
                $pid = (int)($it['product_id'] ?? 0);
                $qty = (int)($it['quantity'] ?? 0);
                if ($pid <= 0 || $qty <= 0) {
                    respond(['status' => 'error', 'message' => 'Invalid item line'], 422);
                }
                if (isset($product_ids[$pid])) {
                    $product_ids[$pid] += $qty;
                } else {
                    $product_ids[$pid] = $qty;
                }
            }

            // Fetch product info (price + stock) in one go.
            $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
            $pstmt = $pdo->prepare("
                SELECT p.product_id, p.name, p.selling_price, s.stock_id, s.quantity
                  FROM Product p
                  JOIN Stock s ON p.product_id = s.product_id
                 WHERE p.product_id IN ($placeholders) AND p.store_id = ?
                   FOR UPDATE
            ");
            $params = array_keys($product_ids);
            $params[] = $store_id;
            $pstmt->execute($params);
            $products = [];
            foreach ($pstmt->fetchAll() as $row) {
                $products[(int)$row['product_id']] = $row;
            }

            // Validate availability.
            foreach ($product_ids as $pid => $qty) {
                if (!isset($products[$pid])) {
                    respond(['status' => 'error', 'message' => 'Product not found in this store: ' . $pid], 409);
                }
                if ((int)$products[$pid]['quantity'] < $qty) {
                    respond([
                        'status'  => 'error',
                        'message' => 'Insufficient stock for ' . $products[$pid]['name'] .
                                     ' (available: ' . $products[$pid]['quantity'] . ')'
                    ], 409);
                }
            }

            // Compute totals.
            $total = 0.0;
            $lines = [];
            foreach ($product_ids as $pid => $qty) {
                $price   = (float)$products[$pid]['selling_price'];
                $lineTot = round($price * $qty, 2);
                $total  += $lineTot;
                $lines[] = [
                    'product_id' => $pid,
                    'quantity'   => $qty,
                    'unit_price' => $price,
                    'total'      => $lineTot,
                ];
            }
            $total = round($total, 2);

            // Begin transaction for the whole sale.
            $pdo->beginTransaction();
            try {
                // Generate a unique reference (e.g. INV-YYYYMMDD-XXXX)
                $ref = 'INV-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));

                $istmt = $pdo->prepare("
                    INSERT INTO SalesTransaction
                        (store_id, transaction_ref, payment_method, customer_name, total_amount, processed_by, status)
                    VALUES (?, ?, ?, ?, ?, ?, 'COMPLETED')
                ");
                $istmt->execute([$store_id, $ref, $payment, $customer ?: null, $total, $processed]);
                $transaction_id = (int)$pdo->lastInsertId();

                $sitem = $pdo->prepare("
                    INSERT INTO SalesItem (transaction_id, product_id, quantity, unit_price, total_price)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $ustock = $pdo->prepare("
                    UPDATE Stock SET quantity = quantity - ? WHERE stock_id = ?
                ");
                $chkstock = $pdo->prepare("SELECT quantity FROM Stock WHERE stock_id = ?");
                $reorder  = $pdo->prepare("SELECT reorder_level FROM Product WHERE product_id = ?");
                $alertins = $pdo->prepare("
                    INSERT INTO LowStockAlert (product_id, current_quantity, reorder_level, status, severity)
                    VALUES (?, ?, ?, 'PENDING', ?)
                ");
                $alertchk = $pdo->prepare("
                    SELECT alert_id FROM LowStockAlert
                     WHERE product_id = ? AND status = 'PENDING'
                ");

                foreach ($lines as $line) {
                    $sitem->execute([
                        $transaction_id,
                        $line['product_id'],
                        $line['quantity'],
                        $line['unit_price'],
                        $line['total'],
                    ]);

                    $stock = $products[$line['product_id']];
                    $ustock->execute([$line['quantity'], $stock['stock_id']]);

                    // Re-read remaining quantity.
                    $chkstock->execute([$stock['stock_id']]);
                    $remaining = (int)$chkstock->fetchColumn();

                    $reorder->execute([$line['product_id']]);
                    $reorderLevel = (int)$reorder->fetchColumn();

                    if ($remaining <= $reorderLevel) {
                        // Avoid duplicate open alerts for the same product.
                        $alertchk->execute([$line['product_id']]);
                        if (!$alertchk->fetch()) {
                            $severity = $remaining <= 0 ? 'CRITICAL' : ($remaining <= $reorderLevel * 0.5 ? 'HIGH' : 'MEDIUM');
                            $alertins->execute([$line['product_id'], $remaining, $reorderLevel, $severity]);
                        }
                    }
                }

                $pdo->commit();
            } catch (Exception $e) {
                $pdo->rollBack();
                respond(['status' => 'error', 'message' => 'Sale failed: ' . $e->getMessage()], 500);
            }

            respond([
                'status'         => 'success',
                'message'        => 'Sale recorded',
                'transaction_id' => $transaction_id,
                'reference'      => $ref,
                'total'          => $total,
            ], 201);

        // ---- Read: sales report data (for sales-report page) ----------------
        case 'report':
            $store_id = isset($_GET['store_id']) && $_GET['store_id'] !== ''
                ? (int)$_GET['store_id']
                : null;
            $from = $_GET['from'] ?? date('Y-m-01');
            $to   = $_GET['to']   ?? date('Y-m-t');
            $pay  = strtoupper(trim($_GET['payment'] ?? ''));

            $sql = "
                SELECT st.transaction_id, st.transaction_ref, st.transaction_date,
                       st.payment_method, st.total_amount, st.customer_name,
                       st.status, s.name AS store_name,
                       p.name AS product_name, si.quantity, si.unit_price, si.total_price
                  FROM SalesTransaction st
                  JOIN Store s ON st.store_id = s.store_id
                  JOIN SalesItem si ON st.transaction_id = si.transaction_id
                  JOIN Product p ON si.product_id = p.product_id
                 WHERE DATE(st.transaction_date) BETWEEN ? AND ?
                   AND st.status = 'COMPLETED'
            ";
            $params = [$from, $to];
            if ($store_id) {
                $sql .= " AND st.store_id = ?";
                $params[] = $store_id;
            }
            if ($pay !== '') {
                $sql .= " AND st.payment_method = ?";
                $params[] = $pay;
            }
            $sql .= " ORDER BY st.transaction_date DESC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll();

            respond([
                'status'     => 'success',
                'store_id'   => $store_id,
                'from'       => $from,
                'to'         => $to,
                'data'       => $rows,
                'summary'    => [
                    'total_sales'    => array_sum(array_column($rows, 'total_price')),
                    'transactions'   => count(array_unique(array_column($rows, 'transaction_id'))),
                ],
            ]);

        // ---- Read: sales summary (daily / weekly / monthly) ----------------
        case 'summary':
            $store_id = isset($_GET['store_id']) && $_GET['store_id'] !== ''
                ? (int)$_GET['store_id']
                : null;
            $period = strtolower(trim($_GET['period'] ?? 'daily')); // daily|weekly|monthly

            // Determine date range based on period.
            $now = new DateTime();
            $from = date('Y-m-d');
            $to   = date('Y-m-d');
            $label = 'Today';

            if ($period === 'weekly') {
                $from = date('Y-m-d', strtotime('monday this week'));
                $to   = date('Y-m-d', strtotime('sunday this week'));
                $label = 'This Week';
            } elseif ($period === 'monthly') {
                $from = date('Y-m-01');
                $to   = date('Y-m-t');
                $label = 'This Month';
            }

            // Aggregated totals + per-transaction list (all stores when no store_id).
            $sql = "
                SELECT st.transaction_id, st.transaction_ref, st.transaction_date,
                       st.payment_method, st.total_amount, st.discount_amount,
                       st.customer_name, st.status, s.name AS store_name
                  FROM SalesTransaction st
                  JOIN Store s ON st.store_id = s.store_id
                 WHERE DATE(st.transaction_date) BETWEEN ? AND ?
                   AND st.status = 'COMPLETED'
            ";
            $params = [$from, $to];
            if ($store_id) {
                $sql .= " AND st.store_id = ?";
                $params[] = $store_id;
            }
            $sql .= " ORDER BY st.transaction_date DESC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll();

            $totalSales = 0.0;
            $paymentTotals = ['CASH' => 0.0, 'CARD' => 0.0, 'MOBILE' => 0.0, 'CREDIT' => 0.0];
            foreach ($rows as $r) {
                $amt = (float)$r['total_amount'];
                $totalSales += $amt;
                $pm = $r['payment_method'];
                if (isset($paymentTotals[$pm])) {
                    $paymentTotals[$pm] += $amt;
                }
            }
            $txnCount = count($rows);
            $avg = $txnCount > 0 ? $totalSales / $txnCount : 0;

            respond([
                'status'   => 'success',
                'period'   => $period,
                'label'    => $label,
                'store_id' => $store_id,
                'from'     => $from,
                'to'       => $to,
                'summary'  => [
                    'total_sales'    => round($totalSales, 2),
                    'transactions'   => $txnCount,
                    'avg_transaction'=> round($avg, 2),
                    'by_payment'     => $paymentTotals,
                ],
                'transactions' => $rows,
            ]);

        default:
            respond(['status' => 'error', 'message' => 'Invalid action'], 400);
    }
} catch (Exception $e) {
    respond(['status' => 'error', 'message' => $e->getMessage()], 500);
}