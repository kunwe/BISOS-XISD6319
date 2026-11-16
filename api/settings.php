<?php
// api/settings.php - Account & user management endpoints
require_once __DIR__ . '/../config/config.php';
require_login();

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$me = current_user();

function respond($payload, int $code = 200): void {
    http_response_code($code);
    echo json_encode($payload);
    exit;
}

try {
    switch ($action) {
        // ---- Current user's profile --------------------------------------
        case 'get_profile':
            $stmt = $pdo->prepare("
                SELECT u.user_id, u.store_id, u.role_id, u.username, u.email,
                       u.first_name, u.last_name, u.phone, u.is_active,
                       r.role_name, st.name AS store_name
                  FROM User u
                  LEFT JOIN Role r ON u.role_id = r.role_id
                  LEFT JOIN Store st ON u.store_id = st.store_id
                 WHERE u.user_id = ?
            ");
            $stmt->execute([$me['user_id']]);
            respond(['status' => 'success', 'data' => $stmt->fetch()]);

        // ---- Update current user's profile -------------------------------
        case 'update_profile':
            if ($method !== 'POST') {
                respond(['status' => 'error', 'message' => 'Method not allowed'], 405);
            }
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $first = trim($input['first_name'] ?? '');
            $last  = trim($input['last_name'] ?? '');
            $email = trim($input['email'] ?? '');
            $phone = trim($input['phone'] ?? '');
            $store_id = (int)($input['store_id'] ?? 0) ?: null;

            if ($first === '' || $email === '') {
                respond(['status' => 'error', 'message' => 'First name and email are required'], 422);
            }

            // Ensure email is not taken by another user.
            $chk = $pdo->prepare("SELECT user_id FROM User WHERE email = ? AND user_id <> ?");
            $chk->execute([$email, $me['user_id']]);
            if ($chk->fetch()) {
                respond(['status' => 'error', 'message' => 'Email is already in use'], 409);
            }

            $pdo->prepare("
                UPDATE User
                   SET first_name = ?, last_name = ?, email = ?, phone = ?, store_id = ?
                 WHERE user_id = ?
            ")->execute([$first, $last ?: null, $email, $phone ?: null, $store_id, $me['user_id']]);

            // Refresh session values.
            $_SESSION['user']['name'] = $first . ' ' . $last;
            $_SESSION['user']['email'] = $email;
            $_SESSION['user']['store_id'] = $store_id;

            respond(['status' => 'success', 'message' => 'Profile updated']);

        // ---- Change current user's password ------------------------------
        case 'change_password':
            if ($method !== 'POST') {
                respond(['status' => 'error', 'message' => 'Method not allowed'], 405);
            }
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $current = $_POST['current_password'] ?? $input['current_password'] ?? '';
            $new     = $_POST['new_password'] ?? $input['new_password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? $input['confirm_password'] ?? $new;

            if ($new !== $confirm) {
                respond(['status' => 'error', 'message' => 'New passwords do not match'], 422);
            }
            if (strlen($new) < 8) {
                respond(['status' => 'error', 'message' => 'Password must be at least 8 characters'], 422);
            }

            $stmt = $pdo->prepare("SELECT password_hash FROM User WHERE user_id = ?");
            $stmt->execute([$me['user_id']]);
            $hash = $stmt->fetchColumn();
            if (!$hash || !password_verify($current, $hash)) {
                respond(['status' => 'error', 'message' => 'Current password is incorrect'], 422);
            }

            $pdo->prepare("UPDATE User SET password_hash = ? WHERE user_id = ?")
                ->execute([password_hash($new, PASSWORD_BCRYPT), $me['user_id']]);

            respond(['status' => 'success', 'message' => 'Password updated']);

        // ---- List users --------------------------------------------------
        case 'list_users':
            $stmt = $pdo->query("
                SELECT u.user_id, u.first_name, u.last_name, u.email, u.phone,
                       u.is_active, u.store_id, r.role_name, st.name AS store_name
                  FROM User u
                  LEFT JOIN Role r ON u.role_id = r.role_id
                  LEFT JOIN Store st ON u.store_id = st.store_id
                 ORDER BY u.user_id ASC
            ");
            respond(['status' => 'success', 'data' => $stmt->fetchAll()]);

        // ---- List roles (for the user form) ------------------------------
        case 'roles':
            $stmt = $pdo->query("SELECT role_id, role_name FROM Role ORDER BY role_id ASC");
            respond(['status' => 'success', 'data' => $stmt->fetchAll()]);

        // ---- Create a user -----------------------------------------------
        case 'create_user':
            if ($method !== 'POST') {
                respond(['status' => 'error', 'message' => 'Method not allowed'], 405);
            }
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $first    = trim($input['first_name'] ?? '');
            $last     = trim($input['last_name'] ?? '');
            $email    = trim($input['email'] ?? '');
            $phone    = trim($input['phone'] ?? '');
            $password = $input['password'] ?? '';
            $role_id  = (int)($input['role_id'] ?? 2);
            $store_id = (int)($input['store_id'] ?? 0) ?: null;

            if ($first === '' || $email === '' || $password === '') {
                respond(['status' => 'error', 'message' => 'Name, email and password are required'], 422);
            }
            if (strlen($password) < 8) {
                respond(['status' => 'error', 'message' => 'Password must be at least 8 characters'], 422);
            }

            $chk = $pdo->prepare("SELECT user_id FROM User WHERE email = ?");
            $chk->execute([$email]);
            if ($chk->fetch()) {
                respond(['status' => 'error', 'message' => 'Email is already registered'], 409);
            }

            $stmt = $pdo->prepare("
                INSERT INTO User (store_id, role_id, username, email, password_hash, first_name, last_name, phone, is_active)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, TRUE)
            ");
            $stmt->execute([
                $store_id,
                $role_id,
                $email,
                $email,
                password_hash($password, PASSWORD_BCRYPT),
                $first,
                $last ?: null,
                $phone ?: null,
            ]);

            respond(['status' => 'success', 'message' => 'User created', 'user_id' => (int)$pdo->lastInsertId()], 201);

        // ---- Update a user -----------------------------------------------
        case 'update_user':
            if ($method !== 'POST') {
                respond(['status' => 'error', 'message' => 'Method not allowed'], 405);
            }
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $user_id  = (int)($input['user_id'] ?? 0);
            $first    = trim($input['first_name'] ?? '');
            $last     = trim($input['last_name'] ?? '');
            $email    = trim($input['email'] ?? '');
            $phone    = trim($input['phone'] ?? '');
            $role_id  = (int)($input['role_id'] ?? 2);
            $store_id = (int)($input['store_id'] ?? 0) ?: null;
            $is_active = !empty($input['is_active']) ? 1 : 0;
            $password = $input['password'] ?? '';

            if ($user_id <= 0) {
                respond(['status' => 'error', 'message' => 'Invalid user id'], 422);
            }
            if ($first === '' || $email === '') {
                respond(['status' => 'error', 'message' => 'Name and email are required'], 422);
            }

            $chk = $pdo->prepare("SELECT user_id FROM User WHERE email = ? AND user_id <> ?");
            $chk->execute([$email, $user_id]);
            if ($chk->fetch()) {
                respond(['status' => 'error', 'message' => 'Email is already in use'], 409);
            }

            if ($password !== '') {
                if (strlen($password) < 8) {
                    respond(['status' => 'error', 'message' => 'Password must be at least 8 characters'], 422);
                }
                $pdo->prepare("
                    UPDATE User
                       SET first_name = ?, last_name = ?, email = ?, phone = ?,
                           role_id = ?, store_id = ?, is_active = ?, password_hash = ?
                     WHERE user_id = ?
                ")->execute([
                    $first, $last ?: null, $email, $phone ?: null,
                    $role_id, $store_id, $is_active,
                    password_hash($password, PASSWORD_BCRYPT), $user_id
                ]);
            } else {
                $pdo->prepare("
                    UPDATE User
                       SET first_name = ?, last_name = ?, email = ?, phone = ?,
                           role_id = ?, store_id = ?, is_active = ?
                     WHERE user_id = ?
                ")->execute([
                    $first, $last ?: null, $email, $phone ?: null,
                    $role_id, $store_id, $is_active, $user_id
                ]);
            }

            respond(['status' => 'success', 'message' => 'User updated']);

        // ---- Delete a user -----------------------------------------------
        case 'delete_user':
            if ($method !== 'POST') {
                respond(['status' => 'error', 'message' => 'Method not allowed'], 405);
            }
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $user_id = (int)($input['user_id'] ?? 0);
            if ($user_id <= 0) {
                respond(['status' => 'error', 'message' => 'Invalid user id'], 422);
            }
            if ($user_id === (int)$me['user_id']) {
                respond(['status' => 'error', 'message' => 'You cannot delete your own account'], 422);
            }
            $pdo->prepare("DELETE FROM User WHERE user_id = ?")->execute([$user_id]);
            respond(['status' => 'success', 'message' => 'User removed']);

        default:
            respond(['status' => 'error', 'message' => 'Invalid action'], 400);
    }
} catch (Exception $e) {
    respond(['status' => 'error', 'message' => $e->getMessage()], 500);
}