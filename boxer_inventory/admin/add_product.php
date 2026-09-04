<?php
require_once __DIR__ . '/../auth.php';
$page_title = "Add New Product";
require_once __DIR__ . '/../header.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sku = $_POST['sku'];
    $barcode = $_POST['barcode'];
    $name = $_POST['product_name'];
    $category_id = $_POST['category_id'];
    $price = $_POST['unit_price'];
    $reorder = $_POST['reorder_level'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $stmt = $conn->prepare("INSERT INTO product (SKU, barcode, product_name, category_id, unit_price, reorder_level, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiddi", $sku, $barcode, $name, $category_id, $price, $reorder, $is_active);
    if ($stmt->execute()) {
        $product_id = $conn->insert_id;
        // Create stock record with default quantities
        $conn->query("INSERT INTO stock (product_id, location_code, current_quantity, minimum_quantity, maximum_quantity) VALUES ($product_id, 'NEW-LOC', 0, 5, 100)");
        $message = "Product added successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

$categories = $conn->query("SELECT category_id, category_name FROM category");
?>
<h3>Add New Product</h3>
<?php if ($message) echo "<div class='alert alert-info'>$message</div>"; ?>
<form method="post" class="row g-3">
    <div class="col-md-6">
        <label>SKU (Unique)</label>
        <input type="text" name="sku" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label>Barcode</label>
        <input type="text" name="barcode" class="form-control">
    </div>
    <div class="col-md-6">
        <label>Product Name</label>
        <input type="text" name="product_name" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label>Category</label>
        <select name="category_id" class="form-select" required>
            <option value="">Select</option>
            <?php while ($cat = $categories->fetch_assoc()): ?>
                <option value="<?php echo $cat['category_id']; ?>"><?php echo $cat['category_name']; ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="col-md-4">
        <label>Unit Price (R)</label>
        <input type="number" step="0.01" name="unit_price" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label>Reorder Level</label>
        <input type="number" name="reorder_level" class="form-control" value="10">
    </div>
    <div class="col-md-4">
        <label>Active</label>
        <div class="form-check">
            <input type="checkbox" name="is_active" class="form-check-input" checked> 
            <label class="form-check-label">Yes</label>
        </div>
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-success">Add Product</button>
        <a href="manage_products.php" class="btn btn-secondary">Cancel</a>
    </div>
</form>
<?php require_once __DIR__ . '/../footer.php'; ?>