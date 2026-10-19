<?php
require_once __DIR__ . '/../auth.php';
if (!hasPermission('view_alerts')) redirect('../index.php');
$page_title = "Low Stock Alerts";
require_once __DIR__ . '/../header.php';

// Acknowledge alert
if (isset($_GET['acknowledge'])) {
    $alert_id = $_GET['acknowledge'];
    $conn->query("UPDATE low_stock_alert SET status = 'RESOLVED', resolved_by = '{$_SESSION['username']}' WHERE alert_id = $alert_id");
    redirect('low_stock_alerts.php');
}

$alerts = $conn->query("SELECT a.*, p.product_name FROM low_stock_alert a JOIN product p ON a.product_id = p.product_id WHERE a.status = 'PENDING' ORDER BY a.alert_date DESC");
?>
<h3>Low Stock Alerts</h3>
<?php if ($alerts->num_rows == 0): ?>
    <div class="alert alert-success">No pending low stock alerts.</div>
<?php else: ?>
    <table class="table table-bordered">
        <thead><tr><th>Product</th><th>Alert Date</th><th>Current Qty</th><th>Threshold</th><th>Action</th></tr></thead>
        <tbody>
        <?php while ($row = $alerts->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['product_name']; ?></td>
            <td><?php echo $row['alert_date']; ?></td>
            <td><?php echo $row['current_quantity']; ?></td>
            <td><?php echo $row['threshold_value']; ?></td>
            <td><a href="?acknowledge=<?php echo $row['alert_id']; ?>" class="btn btn-sm btn-primary">Acknowledge</a></td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
<?php endif; ?>
<?php require_once __DIR__ . '/../footer.php'; ?>