<?php
require_once 'config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Please fill in both fields.";
    } else {
        $stmt = $conn->prepare("SELECT u.*, r.role_name, r.permissions FROM user u JOIN role r ON u.role_id = r.role_id WHERE u.username = ? AND u.is_active = 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password_hash'])) {
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['full_name'] = $row['full_name'];
                $_SESSION['role'] = $row['role_name'];
                $_SESSION['permissions'] = $row['permissions'];

                $conn->query("UPDATE user SET last_login = NOW() WHERE user_id = {$row['user_id']}");
                header("Location: index.php");
                exit;
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "User not found or inactive.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Informal Market</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #1a1a2e 0%, #2d1b2e 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; padding: 20px; }
        .login-card { background: #fff; border-radius: 24px; padding: 40px 36px; width: 100%; max-width: 420px; box-shadow: 0 24px 48px rgba(0,0,0,0.3); }
        .login-card .brand { text-align: center; margin-bottom: 30px; }
        .login-card .brand h2 { color: #1a1a2e; font-weight: 700; }
        .login-card .brand h2 i { color: #e11d2f; }
        .login-card .brand p { color: #6b7a8f; font-size: 14px; }
        .login-card .form-control { border-radius: 12px; padding: 12px 16px; border-color: #dce0e6; }
        .login-card .form-control:focus { border-color: #e11d2f; box-shadow: 0 0 0 3px rgba(225, 29, 47, 0.12); }
        .login-card .btn-primary { background: #e11d2f; border-color: #e11d2f; padding: 12px; border-radius: 12px; font-weight: 600; width: 100%; }
        .login-card .btn-primary:hover { background: #b81222; border-color: #b81222; }
        .login-card .text-muted { font-size: 13px; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand">
            <h2><i class="fas fa-boxes"></i> Informal Market</h2>
            <p>Soweto Branch – Stock Optimization System</p>
        </div>
        <?php if ($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="post">
            <div class="mb-3">
                <label class="form-label fw-semibold">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Enter your username" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-sign-in-alt me-2"></i>Login</button>
        </form>
        <p class="text-muted mt-3 text-center">Default: <strong>admin</strong> / <strong>Password123!</strong></p>
    </div>
</body>
</html>