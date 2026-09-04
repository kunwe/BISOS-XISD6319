<?php
require_once 'config.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $new_password = $_POST['password'];

    // Validate password strength (optional)
    if (strlen($new_password) < 8) {
        $message = "Password must be at least 8 characters.";
    } else {
        $hash = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE user SET password_hash = ? WHERE username = ?");
        $stmt->bind_param("ss", $hash, $username);
        if ($stmt->execute()) {
            $message = "Password for <strong>$username</strong> has been updated successfully. <a href='login.php'>Go to login</a>";
        } else {
            $message = "Error updating password: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="max-width:500px;">
    <h3>Reset Password</h3>
    <?php if ($message) echo "<div class='alert alert-info'>$message</div>"; ?>
    <form method="post">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" value="admin" required>
        </div>
        <div class="mb-3">
            <label>New Password</label>
            <input type="text" name="password" class="form-control" value="Password123!" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Password</button>
    </form>
    <p class="mt-3">After reset, try logging in with the chosen password.</p>
</div>
</body>
</html>