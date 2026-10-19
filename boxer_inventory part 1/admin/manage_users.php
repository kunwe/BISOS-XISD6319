<?php
require_once __DIR__ . '/../auth.php';
if (!hasPermission('manage_users')) redirect('../index.php');
$page_title = "Manage Users";
require_once __DIR__ . '/../header.php';

$users = $conn->query("SELECT u.*, r.role_name FROM user u JOIN role r ON u.role_id = r.role_id");
?>
<h3>Users</h3>
<a href="add_user.php" class="btn btn-success mb-3">Add New User</a>
<table class="table table-bordered">
    <thead><tr><th>ID</th><th>Username</th><th>Email</th><th>Full Name</th><th>Role</th><th>Last Login</th><th>Active</th><th>Actions</th></tr></thead>
    <tbody>
    <?php while ($row = $users->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['user_id']; ?></td>
        <td><?php echo htmlspecialchars($row['username']); ?></td>
        <td><?php echo htmlspecialchars($row['email']); ?></td>
        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
        <td><?php echo $row['role_name']; ?></td>
        <td><?php echo $row['last_login'] ?: 'Never'; ?></td>
        <td><?php echo $row['is_active'] ? 'Yes' : 'No'; ?></td>
        <td>
            <a href="edit_user.php?id=<?php echo $row['user_id']; ?>" class="btn btn-sm btn-warning">Edit</a>
            <a href="delete_user.php?id=<?php echo $row['user_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php require_once __DIR__ . '/../footer.php'; ?>