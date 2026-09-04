<?php
require_once __DIR__ . '/../auth.php';
if (!hasPermission('manage_users')) redirect('../index.php');
$page_title = "Add User";
require_once __DIR__ . '/../header.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $full_name = $_POST['full_name'];
    $role_id = $_POST['role_id'];
    $password = $_POST['password'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Hash password
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO user (username, email, full_name, role_id, password_hash, is_active) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssisi", $username, $email, $full_name, $role_id, $hash, $is_active);
    if ($stmt->execute()) {
        $message = "User added successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

$roles = $conn->query("SELECT role_id, role_name FROM role");
?>
<h3>Add New User</h3>
<?php if ($message) echo "<div class='alert alert-info'>$message</div>"; ?>
<form method="post" class="row g-3">
    <div class="col-md-6">
        <label>Username (Unique)</label>
        <input type="text" name="username" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label>Full Name</label>
        <input type="text" name="full_name" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label>Role</label>
        <select name="role_id" class="form-select" required>
            <option value="">Select</option>
            <?php while ($role = $roles->fetch_assoc()): ?>
                <option value="<?php echo $role['role_id']; ?>"><?php echo $role['role_name']; ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label>Password (min 8 chars, at least 1 uppercase, 1 number, 1 special)</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label>Active</label>
        <div class="form-check">
            <input type="checkbox" name="is_active" class="form-check-input" checked> 
            <label class="form-check-label">Yes</label>
        </div>
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-success">Add User</button>
        <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
    </div>
</form>
<?php require_once __DIR__ . '/../footer.php'; ?>