<?php
// api/stores.php - Store management endpoints (list / create / update / delete)
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

try {
    switch ($action) {
        // ---- List stores (with product / alert / today's sales stats) ------
        case 'list':
            $sql = "
                SELECT st.store_id, st.name, st.address, st.phone, st.email,
                       st.manager_name, st.is_active, st.created_at,
                       (SELECT COUNT(*) FROM Product p WHERE p.store_id = st.store_id) AS product_count,
                       (SELECT COUNT(*) FROM Product p
                          JOIN Stock s ON p.product_id = s.product_id
                         WHERE p.store_id = st.store_id
                           AND s.quantity <= GREATEST(p.reorder_level, s.minimum_level)) AS alert_count,
                       (SELECT COALESCE(SUM(total_amount),0) FROM SalesTransaction
                         WHERE store_id = st.store_id
                           AND DATE(transaction_date) = CURDATE()
                           AND status = 'COMPLETED') AS today_sales
                  FROM Store st
                 ORDER BY st.name ASC
            ";
            $stmt = $pdo->query($sql);
            respond(['status' => 'success', 'data' => $stmt->fetchAll()]);

        // ---- Create a store ----------------------------------------------
        case 'create':
            if ($method !== 'POST') {
                respond(['status' => 'error', 'message' => 'Method not allowed'], 405);
            }
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

            $name         = trim($input['name'] ?? '');
            $address      = trim($input['address'] ?? '');
            $phone        = trim($input['phone'] ?? '');
            $email        = trim($input['email'] ?? '');
            $manager_name = trim($input['manager_name'] ?? '');

            if ($name === '') {
                respond(['status' => 'error', 'message' => 'Store name is required'], 422);
            }

            // Guard against duplicate store names.
            $chk = $pdo->prepare("SELECT store_id FROM Store WHERE name = ?");
            $chk->execute([$name]);
            if ($chk->fetch()) {
                respond(['status' => 'error', 'message' => 'A store with that name already exists'], 409);
            }

            $stmt = $pdo->prepare("
                INSERT INTO Store (name, address, phone, email, manager_name, is_active)
                VALUES (?, ?, ?, ?, ?, TRUE)
            ");
            $stmt->execute([
                $name,
                $address ?: null,
                $phone ?: null,
                $email ?: null,
                $manager_name ?: null,
            ]);
            $store_id = (int)$pdo->lastInsertId();

            respond([
                'status'   => 'success',
                'message'  => 'Store created',
                'store_id' => $store_id,
            ], 201);

        // ---- Update a store ----------------------------------------------
        case 'update':
            if ($method !== 'POST') {
                respond(['status' => 'error', 'message' => 'Method not allowed'], 405);
            }
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

            $store_id     = (int)($input['store_id'] ?? 0);
            $name         = trim($input['name'] ?? '');
            $address      = trim($input['address'] ?? '');
            $phone        = trim($input['phone'] ?? '');
            $email        = trim($input['email'] ?? '');
            $manager_name = trim($input['manager_name'] ?? '');
            $is_active    = !empty($input['is_active']) ? 1 : 0;

            if ($store_id <= 0) {
                respond(['status' => 'error', 'message' => 'Invalid store id'], 422);
            }
            if ($name === '') {
                respond(['status' => 'error', 'message' => 'Store name is required'], 422);
            }

            $chk = $pdo->prepare("SELECT store_id FROM Store WHERE store_id = ?");
            $chk->execute([$store_id]);
            if (!$chk->fetch()) {
                respond(['status' => 'error', 'message' => 'Store not found'], 404);
            }

            $pdo->prepare("
                UPDATE Store
                   SET name = ?, address = ?, phone = ?, email = ?, manager_name = ?, is_active = ?
                 WHERE store_id = ?
            ")->execute([
                $name,
                $address ?: null,
                $phone ?: null,
                $email ?: null,
                $manager_name ?: null,
                $is_active,
                $store_id,
            ]);

            respond(['status' => 'success', 'message' => 'Store updated']);

        // ---- Delete a store (only if it has no products/users) -----------
        case 'delete':
            if ($method !== 'POST') {
                respond(['status' => 'error', 'message' => 'Method not allowed'], 405);
            }
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $store_id = (int)($input['store_id'] ?? 0);
            if ($store_id <= 0) {
                respond(['status' => 'error', 'message' => 'Invalid store id'], 422);
            }

            $chk = $pdo->prepare("SELECT store_id FROM Store WHERE store_id = ?");
            $chk->execute([$store_id]);
            if (!$chk->fetch()) {
                respond(['status' => 'error', 'message' => 'Store not found'], 404);
            }

            // Block delete if the store still has products or users.
            $pc = $pdo->prepare("SELECT COUNT(*) FROM Product WHERE store_id = ?");
            $pc->execute([$store_id]);
            if ((int)$pc->fetchColumn() > 0) {
                respond(['status' => 'error', 'message' => 'Cannot delete: store still has products. Remove them first.'], 409);
            }
            $uc = $pdo->prepare("SELECT COUNT(*) FROM User WHERE store_id = ?");
            $uc->execute([$store_id]);
            if ((int)$uc->fetchColumn() > 0) {
                respond(['status' => 'error', 'message' => 'Cannot delete: store still has users assigned.'], 409);
            }

            $pdo->prepare("DELETE FROM Store WHERE store_id = ?")->execute([$store_id]);
            respond(['status' => 'success', 'message' => 'Store removed']);

        default:
            respond(['status' => 'error', 'message' => 'Invalid action'], 400);
    }
} catch (Exception $e) {
    respond(['status' => 'error', 'message' => $e->getMessage()], 500);
}