<?php
require_once __DIR__ . '/../auth.php';
if (!hasPermission('manage_users')) redirect('../index.php');
$page_title = "Edit User";
require_once __DIR__ . '/../header.php';

$id = $_GET['id'] ?? 0;
if (!$id) redirect('manage_users.php');

$user = $conn->query("SELECT * FROM user WHERE user_id = $id")->fetch_assoc();
if (!$user) redirect('manage_users.php');

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $full_name = $_POST['full_name'];
    $role_id = $_POST['role_id'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $password = $_POST['password'];

    // Build query
    $sql = "UPDATE user SET username=?, email=?, full_name=?, role_id=?, is_active=?";
    $params = [$username, $email, $full_name, $role_id, $is_active];
    $types = "sssii";
    if (!empty($password)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql .= ", password_hash=?";
        $params[] = $hash;
        $types .= "s";
    }
    $sql .= " WHERE user_id=?";
    $params[] = $id;
    $types .= "i";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    if ($stmt->execute()) {
        $message = "User updated successfully!";
        $user = $conn->query("SELECT * FROM user WHERE user_id = $id")->fetch_assoc();
    } else {
        $message = "Error: " . $conn->error;
    }
}

$roles = $conn->query("SELECT role_id, role_name FROM role");
?>
<h3>Edit User</h3>
<?php if ($message) echo "<div class='alert alert-info'>$message</div>"; ?>
<form method="post" class="row g-3">
    <div class="col-md-6">
        <label>Username (Unique)</label>
        <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
    </div>
    <div class="col-md-6">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
    </div>
    <div class="col-md-6">
        <label>Full Name</label>
        <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
    </div>
    <div class="col-md-6">
        <label>Role</label>
        <select name="role_id" class="form-select" required>
            <?php while ($role = $roles->fetch_assoc()): ?>
                <option value="<?php echo $role['role_id']; ?>" <?php echo ($role['role_id'] == $user['role_id']) ? 'selected' : ''; ?>>
                    <?php echo $role['role_name']; ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label>New Password (leave blank to keep current)</label>
        <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current">
    </div>
    <div class="col-md-6">
        <label>Active</label>
        <div class="form-check">
            <input type="checkbox" name="is_active" class="form-check-input" <?php echo $user['is_active'] ? 'checked' : ''; ?>>
            <label class="form-check-label">Yes</label>
        </div>
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-warning">Update User</button>
        <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
    </div>
</form>
<?php require_once __DIR__ . '/../footer.php'; ?>