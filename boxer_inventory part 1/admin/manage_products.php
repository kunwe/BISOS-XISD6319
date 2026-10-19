<?php
require_once __DIR__ . '/../auth.php';
if (!hasPermission('manage_products')) redirect('../index.php');
$page_title = "Manage Products";
require_once __DIR__ . '/../header.php';

$products = $conn->query("SELECT p.*, c.category_name FROM product p JOIN category c ON p.category_id = c.category_id");
?>
<h3>Products</h3>
<a href="add_product.php" class="btn btn-success mb-3">Add New Product</a>
<table class="table table-bordered">
    <thead><tr><th>ID</th><th>SKU</th><th>Name</th><th>Category</th><th>Price</th><th>Reorder Level</th><th>Active</th><th>Actions</th></tr></thead>
    <tbody>
    <?php while ($row = $products->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['product_id']; ?></td>
        <td><?php echo $row['SKU']; ?></td>
        <td><?php echo $row['product_name']; ?></td>
        <td><?php echo $row['category_name']; ?></td>
        <td>R <?php echo number_format($row['unit_price'], 2); ?></td>
        <td><?php echo $row['reorder_level']; ?></td>
        <td><?php echo $row['is_active'] ? 'Yes' : 'No'; ?></td>
        <td>
            <a href="edit_product.php?id=<?php echo $row['product_id']; ?>" class="btn btn-sm btn-warning">Edit</a>
            <a href="delete_product.php?id=<?php echo $row['product_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php require_once __DIR__ . '/../footer.php'; ?>