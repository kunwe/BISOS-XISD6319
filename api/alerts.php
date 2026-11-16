<?php
// api/alerts.php - Low stock alerts endpoints
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

function current_store_id(): ?int {
    $user = current_user();
    if ($user && !empty($user['store_id'])) {
        return (int)$user['store_id'];
    }
    return null;
}

try {
    switch ($action) {
        // ---- List alerts with product & store info ------------------------
        case 'list':
            $store_id = isset($_GET['store_id']) && $_GET['store_id'] !== ''
                ? (int)$_GET['store_id']
                : current_store_id();
            $status = strtoupper(trim($_GET['status'] ?? ''));

            $sql = "SELECT a.alert_id, a.product_id, p.name AS product_name, p.category,
                           st.name AS store_name, st.store_id,
                           a.current_quantity, a.reorder_level, a.severity, a.status,
                           a.created_at
                      FROM LowStockAlert a
                      JOIN Product p ON a.product_id = p.product_id
                      JOIN Store st ON p.store_id = st.store_id
                     WHERE 1=1";
            $params = [];
            if ($store_id) {
                $sql .= " AND p.store_id = ?";
                $params[] = $store_id;
            }
            if ($status !== '' && $status !== 'ALL') {
                $sql .= " AND a.status = ?";
                $params[] = $status;
            }
            $sql .= " ORDER BY FIELD(a.severity,'CRITICAL','HIGH','MEDIUM','LOW'), a.created_at DESC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $alerts = $stmt->fetchAll();

            // If no alerts in table, auto-generate from current stock vs reorder level.
            if (empty($alerts)) {
                $autoSql = "
                    SELECT p.product_id, p.name AS product_name, p.category,
                           st.name AS store_name, st.store_id,
                           s.quantity AS current_quantity,
                           GREATEST(p.reorder_level, s.minimum_level) AS reorder_level
                      FROM Product p
                      JOIN Stock s ON p.product_id = s.product_id
                      JOIN Store st ON p.store_id = st.store_id
                     WHERE s.quantity <= GREATEST(p.reorder_level, s.minimum_level)
                ";
                $autoParams = [];
                if ($store_id) {
                    $autoSql .= " AND p.store_id = ?";
                    $autoParams[] = $store_id;
                }
                $auto = $pdo->prepare($autoSql);
                $auto->execute($autoParams);
                $rows = $auto->fetchAll();
                $alerts = [];
                foreach ($rows as $r) {
                    $qty = (int)$r['current_quantity'];
                    $reorder = (int)$r['reorder_level'];
                    $severity = $qty <= 0 ? 'CRITICAL' : ($qty <= $reorder * 0.5 ? 'HIGH' : 'MEDIUM');
                    $alerts[] = [
                        'alert_id'         => null,
                        'product_id'       => $r['product_id'],
                        'product_name'     => $r['product_name'],
                        'category'         => $r['category'],
                        'store_name'       => $r['store_name'],
                        'store_id'         => $r['store_id'],
                        'current_quantity' => $qty,
                        'reorder_level'    => $reorder,
                        'severity'         => $severity,
                        'status'           => 'PENDING',
                        'created_at'       => date('Y-m-d H:i:s'),
                        'auto'             => true,
                    ];
                }
            }

            // Also include current low stock items even if some alerts exist,
            // so nothing is missed. Merge by product_id.
            $params2 = [];
            $auto2Sql = "
                SELECT p.product_id, p.name AS product_name, p.category,
                       st.name AS store_name, st.store_id,
                       s.quantity AS current_quantity,
                       GREATEST(p.reorder_level, s.minimum_level) AS reorder_level
                  FROM Product p
                  JOIN Stock s ON p.product_id = s.product_id
                  JOIN Store st ON p.store_id = st.store_id
                 WHERE s.quantity <= GREATEST(p.reorder_level, s.minimum_level)
            ";
            if ($store_id) {
                $auto2Sql .= " AND p.store_id = ?";
                $params2[] = $store_id;
            }
            $auto2 = $pdo->prepare($auto2Sql);
            $auto2->execute($params2);
            $seen = [];
            foreach ($alerts as $a) {
                $key = $a['product_id'] . '|' . ($a['store_id'] ?? '');
                $seen[$key] = true;
            }
            foreach ($auto2->fetchAll() as $r) {
                $key = $r['product_id'] . '|' . $r['store_id'];
                if (isset($seen[$key])) continue;
                $qty = (int)$r['current_quantity'];
                $reorder = (int)$r['reorder_level'];
                $severity = $qty <= 0 ? 'CRITICAL' : ($qty <= $reorder * 0.5 ? 'HIGH' : 'MEDIUM');
                $alerts[] = [
                    'alert_id'         => null,
                    'product_id'       => $r['product_id'],
                    'product_name'     => $r['product_name'],
                    'category'         => $r['category'],
                    'store_name'       => $r['store_name'],
                    'store_id'         => $r['store_id'],
                    'current_quantity' => $qty,
                    'reorder_level'    => $reorder,
                    'severity'         => $severity,
                    'status'           => 'PENDING',
                    'created_at'       => date('Y-m-d H:i:s'),
                    'auto'             => true,
                ];
            }

            respond([
                'status' => 'success',
                'data'   => $alerts,
                'count'  => count($alerts),
            ]);

        // ---- Acknowledge / resolve an alert -------------------------------
        case 'update':
            if ($method !== 'POST') {
                respond(['status' => 'error', 'message' => 'Method not allowed'], 405);
            }
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $alert_id = (int)($input['alert_id'] ?? 0);
            $new_status = strtoupper(trim($input['status'] ?? 'ACKNOWLEDGED'));
            if (!in_array($new_status, ['ACKNOWLEDGED', 'RESOLVED'], true)) {
                respond(['status' => 'error', 'message' => 'Invalid status'], 422);
            }
            if ($alert_id <= 0) {
                respond(['status' => 'error', 'message' => 'Invalid alert id (auto-generated alerts cannot be updated directly)'], 422);
            }
            $user_id = current_user()['user_id'] ?? null;
            if ($new_status === 'ACKNOWLEDGED') {
                $pdo->prepare("UPDATE LowStockAlert SET status='ACKNOWLEDGED', acknowledged_by=?, acknowledged_at=NOW() WHERE alert_id=?")->execute([$user_id, $alert_id]);
            } else {
                $pdo->prepare("UPDATE LowStockAlert SET status='RESOLVED', resolved_at=NOW() WHERE alert_id=?")->execute([$alert_id]);
            }
            respond(['status' => 'success', 'message' => 'Alert ' . strtolower($new_status)]);

        default:
            respond(['status' => 'error', 'message' => 'Invalid action'], 400);
    }
} catch (Exception $e) {
    respond(['status' => 'error', 'message' => $e->getMessage()], 500);
}