<?php
require_once __DIR__ . '/../auth.php';
if (!hasPermission('set_thresholds')) redirect('../index.php');
$page_title = "Set Thresholds";
require_once __DIR__ . '/../header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $new_threshold = $_POST['reorder_level'];
    $conn->query("UPDATE product SET reorder_level = $new_threshold WHERE product_id = $product_id");
    // Update low stock alerts accordingly
    generateLowStockAlerts();
    $message = "Threshold updated.";
}

$products = $conn->query("SELECT p.product_id, p.product_name, p.reorder_level, s.current_quantity FROM product p JOIN stock s ON p.product_id = s.product_id WHERE p.is_active = 1");
?>
<h3>Set Reorder Thresholds</h3>
<?php if (isset($message)) echo "<div class='alert alert-success'>$message</div>"; ?>
<table class="table table-bordered">
    <thead><tr><th>Product</th><th>Current Qty</th><th>Current Threshold</th><th>Action</th></tr></thead>
    <tbody>
    <?php while ($row = $products->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['product_name']; ?></td>
        <td><?php echo $row['current_quantity']; ?></td>
        <td><?php echo $row['reorder_level']; ?></td>
        <td>
            <form method="post" class="row g-2">
                <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                <div class="col-auto">
                    <input type="number" name="reorder_level" value="<?php echo $row['reorder_level']; ?>" class="form-control form-control-sm" style="width:80px;">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary">Update</button>
                </div>
            </form>
        </td>
    </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php require_once __DIR__ . '/../footer.php'; ?>