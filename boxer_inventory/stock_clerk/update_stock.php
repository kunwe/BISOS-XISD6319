<?php
require_once __DIR__ . '/../auth.php';
if (!hasPermission('update_stock')) redirect('../index.php');
$page_title = "Update Stock";
require_once __DIR__ . '/../header.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $quantity = intval($_POST['quantity']);
    $type = $_POST['type']; // 'inward' or 'adjustment'
    $reason = $_POST['reason'] ?? '';

    // Update stock quantity
    $sql = "UPDATE stock SET current_quantity = current_quantity + ? WHERE product_id = ?";
    if ($type == 'adjustment') {
        // For adjustment, we might subtract (if lost/damaged) - we'll let user enter negative?
        // Simpler: allow both positive (inward) and negative (adjustment) quantities.
        // But we'll treat as addition; for loss, user enters negative.
        $sql = "UPDATE stock SET current_quantity = current_quantity + ? WHERE product_id = ?";
    }
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $quantity, $product_id);
    if ($stmt->execute()) {
        // Log last_updated, updated_by
        $update = $conn->query("UPDATE stock SET last_updated = NOW(), updated_by = '{$_SESSION['username']}' WHERE product_id = $product_id");
        $message = "Stock updated successfully.";
        // Check low stock after update
        generateLowStockAlerts();
    } else {
        $message = "Error updating stock.";
    }
}

// Get products for dropdown
$products = $conn->query("SELECT product_id, product_name FROM product WHERE is_active = 1");
?>
<h3>Update Stock</h3>
<?php if ($message) echo "<div class='alert alert-info'>$message</div>"; ?>
<form method="post" class="row g-3">
    <div class="col-md-4">
        <label>Product</label>
        <select name="product_id" class="form-select" required>
            <option value="">Select</option>
            <?php while ($p = $products->fetch_assoc()): ?>
                <option value="<?php echo $p['product_id']; ?>"><?php echo $p['product_name']; ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="col-md-3">
        <label>Quantity (positive for inward, negative for loss/damage)</label>
        <input type="number" name="quantity" class="form-control" required>
    </div>
    <div class="col-md-3">
        <label>Type</label>
        <select name="type" class="form-select">
            <option value="inward">Inward (delivery)</option>
            <option value="adjustment">Adjustment (loss/damage)</option>
        </select>
    </div>
    <div class="col-md-2">
        <label>Reason (optional)</label>
        <input type="text" name="reason" class="form-control">
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-success">Update</button>
    </div>
</form>
<?php require_once __DIR__ . '/../footer.php'; ?>