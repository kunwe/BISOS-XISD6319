<?php
require_once __DIR__ . '/../auth.php';
if (!hasPermission('generate_reports')) redirect('../index.php');
$page_title = "Low Stock Report";
require_once __DIR__ . '/../header.php';

$result = $conn->query("SELECT p.product_id, p.product_name, s.current_quantity, p.reorder_level
                        FROM product p
                        JOIN stock s ON p.product_id = s.product_id
                        WHERE s.current_quantity <= p.reorder_level AND p.is_active = 1");
?>
<h3>Low Stock Report</h3>
<?php if ($result->num_rows == 0): ?>
    <div class="alert alert-success">All products are above reorder level.</div>
<?php else: ?>
    <table class="table table-bordered">
        <thead><tr><th>Product</th><th>Current Qty</th><th>Reorder Level</th><th>Recommended Order</th></tr></thead>
        <tbody>
        <?php 
        $total_reorder_cost = 0;
        while ($row = $result->fetch_assoc()):
            $recommended = max(0, $row['reorder_level'] * 2 - $row['current_quantity']); // arbitrary
            // get unit price
            $price = $conn->query("SELECT unit_price FROM product WHERE product_id = {$row['product_id']}")->fetch_assoc()['unit_price'];
            $total_reorder_cost += $recommended * $price;
        ?>
            <tr>
                <td><?php echo $row['product_name']; ?></td>
                <td><?php echo $row['current_quantity']; ?></td>
                <td><?php echo $row['reorder_level']; ?></td>
                <td><?php echo $recommended; ?> units</td>
            </tr>
        <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr><th colspan="3">Estimated Reorder Cost</th><th>R <?php echo number_format($total_reorder_cost, 2); ?></th></tr>
        </tfoot>
    </table>
<?php endif; ?>
<?php require_once __DIR__ . '/../footer.php'; ?>