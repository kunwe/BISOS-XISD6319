<?php
require_once __DIR__ . '/../auth.php';
$page_title = "Inventory Valuation";
require_once __DIR__ . '/../header.php';


$sql = "SELECT c.category_name, COUNT(p.product_id) as num_products, SUM(s.current_quantity * p.unit_price) as cost_value,
        SUM(s.current_quantity * (p.unit_price * 1.5)) as retail_value -- assuming 50% markup for retail
        FROM product p
        JOIN stock s ON p.product_id = s.product_id
        JOIN category c ON p.category_id = c.category_id
        WHERE p.is_active = 1
        GROUP BY c.category_id";
$result = $conn->query($sql);
$total_cost = $total_retail = $total_units = 0;
?>
<h3>Inventory Valuation Report</h3>
<table class="table table-bordered">
    <thead><tr><th>Category</th><th># Products</th><th>Cost Value</th><th>Retail Value</th></tr></thead>
    <tbody>
    <?php while ($row = $result->fetch_assoc()): 
        $total_cost += $row['cost_value'];
        $total_retail += $row['retail_value'];
        // count total units
        $units = $conn->query("SELECT SUM(s.current_quantity) as total_units FROM stock s JOIN product p ON s.product_id = p.product_id WHERE p.category_id = (SELECT category_id FROM category WHERE category_name = '{$row['category_name']}')")->fetch_assoc()['total_units'];
        $total_units += $units;
    ?>
        <tr>
            <td><?php echo $row['category_name']; ?></td>
            <td><?php echo $row['num_products']; ?></td>
            <td>R <?php echo number_format($row['cost_value'], 2); ?></td>
            <td>R <?php echo number_format($row['retail_value'], 2); ?></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
    <tfoot>
        <tr><th colspan="2">Total</th><th>R <?php echo number_format($total_cost, 2); ?></th><th>R <?php echo number_format($total_retail, 2); ?></th></tr>
        <tr><th colspan="4">Projected Gross Profit: R <?php echo number_format($total_retail - $total_cost, 2); ?></th></tr>
        <tr><th colspan="4">Total Units in Stock: <?php echo $total_units; ?></th></tr>
    </tfoot>
</table>
<?php require_once __DIR__ . '/../footer.php'; ?>