<?php
require_once __DIR__ . '/config/config.php';
redirect_if_logged_in();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName  = trim($_POST['lastName']  ?? '');
    $email     = trim($_POST['email']     ?? '');
    $password  = $_POST['password']       ?? '';
    $confirm   = $_POST['confirmPassword'] ?? '';
    $role_id   = 2; // Default to Manager (Store Owner is 1, Clerk 3, Cashier 4)

    if ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT user_id FROM User WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email is already registered.';
        } else {
            // Hash the password securely (BCrypt)
            $hash = password_hash($password, PASSWORD_BCRYPT);
            
            // Insert into Database
            $stmt = $pdo->prepare("INSERT INTO User (store_id, role_id, username, email, password_hash, first_name, last_name) VALUES (1, ?, ?, ?, ?, ?, ?)");
            try {
                $stmt->execute([$role_id, $email, $email, $hash, $firstName, $lastName]);
                flash('success', 'Registration successful! Please sign in.');
                header('Location: login.php');
                exit;
            } catch (Exception $e) {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}

$bodyClass = 'auth-page';
$showNav   = false;
$pageTitle = 'Register';
include __DIR__ . '/includes/header.php';
?>
<main class="auth-container" role="main" style="max-width:520px">
    <div class="auth-box">
        <div class="auth-header">
            <div class="auth-logo-icon">B</div>
            <h1>Create Account</h1>
            <p class="subtitle">Join BISOS to manage your store</p>
        </div>

        <form id="registerForm" class="auth-form" method="post" action="register.php" novalidate>
            <?php if ($error): ?>
                <div class="auth-error" role="alert"><i class="fas fa-exclamation-circle"></i> <?= e($error) ?></div>
            <?php endif; ?>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                <div class="form-group">
                    <label for="firstName">First Name</label>
                    <div class="input-wrapper"><i class="fas fa-user"></i><input type="text" id="firstName" name="firstName" required></div>
                </div>
                <div class="form-group">
                    <label for="lastName">Last Name</label>
                    <div class="input-wrapper"><i class="fas fa-user"></i><input type="text" id="lastName" name="lastName" required></div>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-wrapper"><i class="fas fa-envelope"></i><input type="email" id="email" name="email" required></div>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <div class="input-wrapper"><i class="fas fa-phone"></i><input type="tel" id="phone" name="phone"></div>
            </div>

            <div class="form-group">
                <label for="role">Role</label>
                <div class="input-wrapper">
                    <i class="fas fa-user-tag"></i>
                    <select id="role" name="role" required style="padding-left:2.4rem;width:100%;border:2px solid #E0E0E0;border-radius:8px;padding-top:0.7rem;padding-bottom:0.7rem;font-size:0.95rem">
                        <option value="">Select your role</option>
                        <option value="store_owner">Store Owner</option>
                        <option value="manager">Manager</option>
                        <option value="clerk">Clerk</option>
                        <option value="cashier">Cashier</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Min. 8 characters" required>
                    <button type="button" class="toggle-password"><i class="fas fa-eye"></i></button>
                </div>
            </div>

            <div class="form-group">
                <label for="confirmPassword">Confirm Password</label>
                <div class="input-wrapper"><i class="fas fa-lock"></i><input type="password" id="confirmPassword" name="confirmPassword" required></div>
            </div>

            <div style="margin-bottom:1.25rem;font-size:0.875rem">
                <label style="display:flex;align-items:flex-start;gap:0.5rem;cursor:pointer">
                    <input type="checkbox" name="terms" required style="margin-top:3px">
                    I agree to the <a href="#" style="color:var(--primary-light)">Terms of Service</a>
                </label>
            </div>

            <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-user-plus"></i> Create Account</button>
        </form>
        <p class="register-link">Already have an account? <a href="login.php">Sign in</a></p>
    </div>
</main>
<script type="module" src="js/auth.js"></script>
</body>
</html>