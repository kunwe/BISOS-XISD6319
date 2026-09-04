<?php
require_once __DIR__ . '/../auth.php';
if (!hasPermission('view_stock')) redirect('../index.php');
$page_title = "View Stock";
require_once __DIR__ . '/../header.php';

$search = $_GET['search'] ?? '';
$sql = "SELECT p.product_id, p.product_name, p.SKU, s.current_quantity, s.minimum_quantity, s.maximum_quantity, s.location_code
        FROM product p
        JOIN stock s ON p.product_id = s.product_id
        WHERE p.is_active = 1 AND (p.product_name LIKE ? OR p.SKU LIKE ?)";
$stmt = $conn->prepare($sql);
$like = "%$search%";
$stmt->bind_param("ss", $like, $like);
$stmt->execute();
$result = $stmt->get_result();
?>
<h3>Stock Levels</h3>
<form method="get" class="row g-3 mb-4">
    <div class="col-auto">
        <input type="text" name="search" class="form-control" placeholder="Search by name or SKU" value="<?php echo htmlspecialchars($search); ?>">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-primary">Search</button>
    </div>
</form>
<table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>Product</th><th>SKU</th><th>Location</th><th>Current Qty</th><th>Min Qty</th><th>Max Qty</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['product_name']; ?></td>
            <td><?php echo $row['SKU']; ?></td>
            <td><?php echo $row['location_code']; ?></td>
            <td><?php echo $row['current_quantity']; ?></td>
            <td><?php echo $row['minimum_quantity']; ?></td>
            <td><?php echo $row['maximum_quantity']; ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php require_once __DIR__ . '/../footer.php'; ?>