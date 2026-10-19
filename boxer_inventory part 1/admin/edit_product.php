<?php
require_once __DIR__ . '/../auth.php';
if (!hasPermission('manage_products')) redirect('../index.php');
$page_title = "Edit Product";
require_once __DIR__ . '/../header.php';

$id = $_GET['id'] ?? 0;
if (!$id) redirect('manage_products.php');

$product = $conn->query("SELECT * FROM product WHERE product_id = $id")->fetch_assoc();
if (!$product) redirect('manage_products.php');

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sku = $_POST['sku'];
    $barcode = $_POST['barcode'];
    $name = $_POST['product_name'];
    $category_id = $_POST['category_id'];
    $price = $_POST['unit_price'];
    $reorder = $_POST['reorder_level'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE product SET SKU=?, barcode=?, product_name=?, category_id=?, unit_price=?, reorder_level=?, is_active=? WHERE product_id=?");
    $stmt->bind_param("sssiddii", $sku, $barcode, $name, $category_id, $price, $reorder, $is_active, $id);
    if ($stmt->execute()) {
        $message = "Product updated successfully!";
        // Refresh product data
        $product = $conn->query("SELECT * FROM product WHERE product_id = $id")->fetch_assoc();
    } else {
        $message = "Error: " . $conn->error;
    }
}

$categories = $conn->query("SELECT category_id, category_name FROM category");
?>
<h3>Edit Product</h3>
<?php if ($message) echo "<div class='alert alert-info'>$message</div>"; ?>
<form method="post" class="row g-3">
    <div class="col-md-6">
        <label>SKU (Unique)</label>
        <input type="text" name="sku" class="form-control" value="<?php echo htmlspecialchars($product['SKU']); ?>" required>
    </div>
    <div class="col-md-6">
        <label>Barcode</label>
        <input type="text" name="barcode" class="form-control" value="<?php echo htmlspecialchars($product['barcode']); ?>">
    </div>
    <div class="col-md-6">
        <label>Product Name</label>
        <input type="text" name="product_name" class="form-control" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
    </div>
    <div class="col-md-6">
        <label>Category</label>
        <select name="category_id" class="form-select" required>
            <?php while ($cat = $categories->fetch_assoc()): ?>
                <option value="<?php echo $cat['category_id']; ?>" <?php echo ($cat['category_id'] == $product['category_id']) ? 'selected' : ''; ?>>
                    <?php echo $cat['category_name']; ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="col-md-4">
        <label>Unit Price (R)</label>
        <input type="number" step="0.01" name="unit_price" class="form-control" value="<?php echo $product['unit_price']; ?>" required>
    </div>
    <div class="col-md-4">
        <label>Reorder Level</label>
        <input type="number" name="reorder_level" class="form-control" value="<?php echo $product['reorder_level']; ?>">
    </div>
    <div class="col-md-4">
        <label>Active</label>
        <div class="form-check">
            <input type="checkbox" name="is_active" class="form-check-input" <?php echo $product['is_active'] ? 'checked' : ''; ?>>
            <label class="form-check-label">Yes</label>
        </div>
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-warning">Update Product</button>
        <a href="manage_products.php" class="btn btn-secondary">Cancel</a>
    </div>
</form>
<?php require_once __DIR__ . '/../footer.php'; ?>