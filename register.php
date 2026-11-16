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

    // Map the friendly role names to Role.role_id values.
    $roleMap = [
        'store_owner' => 1,
        'manager'     => 2,
        'clerk'       => 3,
        'cashier'     => 4,
    ];
    $roleKey = $_POST['role'] ?? 'manager';
    $role_id = $roleMap[$roleKey] ?? 2;

    // Store handling: either an existing store or a brand-new one.
    $storeMode  = $_POST['storeMode'] ?? 'existing';
    $newName    = trim($_POST['newStoreName'] ?? '');
    $newAddress = trim($_POST['newStoreAddress'] ?? '');
    $newPhone   = trim($_POST['newStorePhone'] ?? '');
    $newEmail   = trim($_POST['newStoreEmail'] ?? '');

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
            $pdo->beginTransaction();
            try {
                // Resolve the store id.
                if ($storeMode === 'new') {
                    if ($newName === '') {
                        throw new Exception('New store name is required.');
                    }
                    // Reuse an existing store with the same name if present.
                    $chk = $pdo->prepare("SELECT store_id FROM Store WHERE name = ?");
                    $chk->execute([$newName]);
                    $existing = $chk->fetchColumn();
                    if ($existing) {
                        $store_id = (int)$existing;
                    } else {
                        $ins = $pdo->prepare("
                            INSERT INTO Store (name, address, phone, email, manager_name, is_active)
                            VALUES (?, ?, ?, ?, ?, TRUE)
                        ");
                        $ins->execute([
                            $newName,
                            $newAddress ?: null,
                            $newPhone ?: null,
                            $newEmail ?: null,
                            $firstName . ' ' . $lastName,
                        ]);
                        $store_id = (int)$pdo->lastInsertId();
                    }
                } else {
                    $store_id = (int)($_POST['storeId'] ?? 1);
                    if ($store_id <= 0) {
                        throw new Exception('Please select a store.');
                    }
                    $chk = $pdo->prepare("SELECT store_id FROM Store WHERE store_id = ?");
                    $chk->execute([$store_id]);
                    if (!$chk->fetch()) {
                        throw new Exception('Selected store not found.');
                    }
                }

                // Hash the password securely (BCrypt)
                $hash = password_hash($password, PASSWORD_BCRYPT);

                // Insert into Database
                $stmt = $pdo->prepare("
                    INSERT INTO User (store_id, role_id, username, email, password_hash, first_name, last_name, phone)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $store_id,
                    $role_id,
                    $email,
                    $email,
                    $hash,
                    $firstName,
                    $lastName,
                    trim($_POST['phone'] ?? '') ?: null,
                ]);

                $pdo->commit();
                flash('success', 'Registration successful! Please sign in.');
                header('Location: login.php');
                exit;
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = $e->getMessage();
            }
        }
    }
}

// Load stores for the dropdown (only needed on GET / re-render).
$stores = [];
try {
    $stores = $pdo->query("SELECT store_id, name FROM Store WHERE is_active = TRUE ORDER BY name ASC")->fetchAll();
} catch (Exception $e) {
    $stores = [];
}

$bodyClass = 'auth-page';
$showNav   = false;
$pageTitle = 'Register';
include __DIR__ . '/includes/header.php';
?>
<main class="auth-container" role="main" style="max-width:560px">
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
                        <option value="manager">Manager</option>
                        <option value="store_owner">Store Owner</option>
                        <option value="clerk">Clerk</option>
                        <option value="cashier">Cashier</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="storeMode">Store</label>
                <div class="input-wrapper">
                    <i class="fas fa-store"></i>
                    <select id="storeMode" name="storeMode" style="padding-left:2.4rem;width:100%;border:2px solid #E0E0E0;border-radius:8px;padding-top:0.7rem;padding-bottom:0.7rem;font-size:0.95rem">
                        <option value="existing">Select an existing store</option>
                        <option value="new">+ Create a new store</option>
                    </select>
                </div>
            </div>

            <div class="form-group" id="existingStoreGroup">
                <label for="storeId">Choose Store</label>
                <div class="input-wrapper">
                    <i class="fas fa-warehouse"></i>
                    <select id="storeId" name="storeId" style="padding-left:2.4rem;width:100%;border:2px solid #E0E0E0;border-radius:8px;padding-top:0.7rem;padding-bottom:0.7rem;font-size:0.95rem">
                        <option value="">Select a store</option>
                        <?php foreach ($stores as $s): ?>
                            <option value="<?= (int)$s['store_id'] ?>"><?= e($s['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if (empty($stores)): ?>
                    <p style="font-size:0.8rem;color:var(--text-secondary);margin-top:0.3rem">No stores exist yet — choose "+ Create a new store".</p>
                <?php endif; ?>
            </div>

            <div id="newStoreFields" style="display:none">
                <div class="form-group">
                    <label for="newStoreName">New Store Name *</label>
                    <div class="input-wrapper"><i class="fas fa-store"></i><input type="text" id="newStoreName" name="newStoreName"></div>
                </div>
                <div class="form-group">
                    <label for="newStoreAddress">New Store Address</label>
                    <div class="input-wrapper"><i class="fas fa-map-marker-alt"></i><input type="text" id="newStoreAddress" name="newStoreAddress"></div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                    <div class="form-group">
                        <label for="newStorePhone">New Store Phone</label>
                        <div class="input-wrapper"><i class="fas fa-phone"></i><input type="tel" id="newStorePhone" name="newStorePhone"></div>
                    </div>
                    <div class="form-group">
                        <label for="newStoreEmail">New Store Email</label>
                        <div class="input-wrapper"><i class="fas fa-envelope"></i><input type="email" id="newStoreEmail" name="newStoreEmail"></div>
                    </div>
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
<script>
    // Toggle the store fields based on the selected mode.
    (function () {
        const mode = document.getElementById('storeMode');
        const existingGroup = document.getElementById('existingStoreGroup');
        const newFields = document.getElementById('newStoreFields');
        const storeId = document.getElementById('storeId');
        const newName = document.getElementById('newStoreName');

        function sync() {
            const isNew = mode.value === 'new';
            existingGroup.style.display = isNew ? 'none' : '';
            newFields.style.display = isNew ? '' : 'none';
            storeId.required = !isNew;
            newName.required = isNew;
        }
        mode.addEventListener('change', sync);
        sync();
    })();
</script>
</body>
</html>