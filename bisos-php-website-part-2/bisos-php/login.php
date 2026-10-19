<?php
require_once __DIR__ . '/config/config.php';
redirect_if_logged_in();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Find the user by email
    $stmt = $pdo->prepare("SELECT * FROM User WHERE email = ? AND is_active = 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Verify password against the hash in the database
    if ($user && password_verify($password, $user['password_hash'])) {
        // Update last login time
        $pdo->prepare("UPDATE User SET last_login = NOW() WHERE user_id = ?")->execute([$user['user_id']]);

        // Set session
        $_SESSION['user'] = [
            'id'       => $user['user_id'],
            'email'    => $user['email'],
            'name'     => $user['first_name'] . ' ' . $user['last_name'],
            'initials' => strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)),
            'role_id'  => $user['role_id']
        ];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid email or password.';
    }
}

$flash = get_flash();
$bodyClass = 'auth-page';
$showNav   = false;
$pageTitle = 'Login';
include __DIR__ . '/includes/header.php';
?>
<main class="auth-container" role="main">
    <div class="auth-box">
        <div class="auth-header">
            <div class="auth-logo-icon" aria-hidden="true">B</div>
            <h1>BISOS</h1>
            <p class="subtitle">Informal Supermarket Management System</p>
        </div>

        <form id="loginForm" class="auth-form" method="post" action="login.php" novalidate>
            <?php if ($flash): ?>
                <div class="auth-success" role="status"><i class="fas fa-check-circle"></i> <?= e($flash['message']) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="auth-error" role="alert"><i class="fas fa-exclamation-circle"></i> <?= e($error) ?></div>
            <?php endif; ?>

            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" placeholder="Enter your email" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <button type="button" class="toggle-password" aria-label="Show password"><i class="fas fa-eye"></i></button>
                </div>
            </div>

            <div class="form-options">
                <label class="remember-me"><input type="checkbox" name="remember"> Remember me</label>
                <a href="forgot-password.php" class="forgot-link">Forgot Password?</a>
            </div>

            <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-sign-in-alt"></i> Sign In</button>
        </form>

        <div class="divider"><span>OR</span></div>

        <button class="btn-sso" id="googleLogin" type="button"><i class="fab fa-google sso-icon-google"></i> Sign in with Google SSO</button>
        <button class="btn-sso" id="microsoftLogin" type="button"><i class="fab fa-microsoft sso-icon-microsoft"></i> Sign in with Microsoft</button>

        <p class="register-link">Don't have an account? <a href="register.php">Register here</a></p>
    </div>

    <aside class="demo-credentials" aria-label="Demo login credentials">
        <p><strong>Demo Credentials</strong></p>
        <p>Email: demo@bisos.co.za</p>
        <p>Password: Demo@123</p>
        <button id="demoLoginBtn" class="btn btn-outline" style="margin-top:0.75rem;width:100%;justify-content:center;color:white;border-color:rgba(255,255,255,0.5)">
            <i class="fas fa-play"></i> Use Demo Account
        </button>
    </aside>
</main>
<script type="module" src="js/auth.js"></script>
</body>
</html>