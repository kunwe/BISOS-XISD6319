<?php
require_once __DIR__ . '/../auth.php';
if (!hasPermission('generate_reports')) redirect('../index.php');
$page_title = "Daily Sales Report";
require_once __DIR__ . '/../header.php';

$date = $_GET['date'] ?? date('Y-m-d');
$sales = $conn->query("SELECT p.product_name, SUM(s.quantity_sold) as total_qty, s.unit_price_at_sale, SUM(s.subtotal) as total_subtotal
                        FROM sales_transaction s
                        JOIN product p ON s.product_id = p.product_id
                        WHERE DATE(s.transaction_date) = '$date'
                        GROUP BY s.product_id");
?>
<h3>Daily Sales Report - <?php echo $date; ?></h3>
<form method="get" class="row g-3 mb-4">
    <div class="col-auto">
        <input type="date" name="date" value="<?php echo $date; ?>" class="form-control">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-primary">Generate</button>
    </div>
</form>
<table class="table table-bordered">
    <thead><tr><th>Product</th><th>Quantity Sold</th><th>Unit Price</th><th>Subtotal</th></tr></thead>
    <tbody>
    <?php 
    $grand_total = 0;
    while ($row = $sales->fetch_assoc()): 
        $grand_total += $row['total_subtotal'];
    ?>
        <tr>
            <td><?php echo $row['product_name']; ?></td>
            <td><?php echo $row['total_qty']; ?></td>
            <td>R <?php echo number_format($row['unit_price_at_sale'], 2); ?></td>
            <td>R <?php echo number_format($row['total_subtotal'], 2); ?></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
    <tfoot>
        <tr><th colspan="3">Total Sales (excl. VAT)</th><th>R <?php echo number_format($grand_total, 2); ?></th></tr>
        <tr><th colspan="3">VAT (15%)</th><th>R <?php echo number_format($grand_total * 0.15, 2); ?></th></tr>
        <tr><th colspan="3">Total (incl. VAT)</th><th>R <?php echo number_format($grand_total * 1.15, 2); ?></th></tr>
    </tfoot>
</table>
<?php require_once __DIR__ . '/../footer.php'; ?>