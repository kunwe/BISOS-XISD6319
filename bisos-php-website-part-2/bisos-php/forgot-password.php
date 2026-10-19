<?php
require_once __DIR__ . '/config/config.php';
redirect_if_logged_in();
$sent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') $sent = true;

$bodyClass = 'auth-page';
$showNav   = false;
$pageTitle = 'Forgot Password';
include __DIR__ . '/includes/header.php';
?>
<main class="auth-container" role="main">
    <div class="auth-box">
        <div class="auth-header">
            <div class="auth-logo-icon">B</div>
            <h1>Forgot Password</h1>
            <p class="subtitle">Enter your email to receive a reset link</p>
        </div>
        <form class="auth-form" method="post">
            <?php if ($sent): ?>
                <div class="auth-success"><i class="fas fa-check-circle"></i> If the email exists, a reset link has been sent.</div>
            <?php endif; ?>
            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-wrapper"><i class="fas fa-envelope"></i><input type="email" id="email" name="email" required></div>
            </div>
            <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-paper-plane"></i> Send Reset Link</button>
        </form>
        <p class="register-link"><a href="login.php">Back to sign in</a></p>
    </div>
</main>
</body>
</html>