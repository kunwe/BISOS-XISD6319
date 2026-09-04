<?php
require_once __DIR__ . '/auth.php';
$page_title = "Dashboard";
require_once __DIR__ . '/header.php';

// Fetch stats for dashboard
$total_products = $conn->query("SELECT COUNT(*) as cnt FROM product WHERE is_active = 1")->fetch_assoc()['cnt'];
$low_stock_count = $conn->query("SELECT COUNT(*) as cnt FROM product p JOIN stock s ON p.product_id = s.product_id WHERE s.current_quantity <= p.reorder_level AND p.is_active = 1")->fetch_assoc()['cnt'];
$pending_alerts = $conn->query("SELECT COUNT(*) as cnt FROM low_stock_alert WHERE status = 'PENDING'")->fetch_assoc()['cnt'];
$today_sales = $conn->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM sales_transaction WHERE DATE(transaction_date) = CURDATE()")->fetch_assoc()['total'];
?>

<!-- Dashboard Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-md-3 col-6">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <p class="stat-label">Total Products</p>
                <p class="stat-number"><?php echo $total_products; ?></p>
                <a href="admin/manage_products.php" class="stat-link">Manage <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
            <div class="stat-icon bg-blue"><i class="fas fa-cube"></i></div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <p class="stat-label">Low Stock Items</p>
                <p class="stat-number"><?php echo $low_stock_count; ?></p>
                <a href="manager/low_stock_report.php" class="stat-link">View <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
            <div class="stat-icon bg-orange"><i class="fas fa-exclamation-triangle"></i></div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <p class="stat-label">Pending Alerts</p>
                <p class="stat-number"><?php echo $pending_alerts; ?></p>
                <a href="stock_clerk/low_stock_alerts.php" class="stat-link">View <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
            <div class="stat-icon bg-red"><i class="fas fa-bell"></i></div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <p class="stat-label">Today's Sales</p>
                <p class="stat-number">R <?php echo number_format($today_sales, 2); ?></p>
                <a href="manager/daily_sales.php" class="stat-link">Report <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
            <div class="stat-icon bg-green"><i class="fas fa-rand"></i></div>
        </div>
    </div>
</div>

<!-- Quick Actions & Recent Activity -->
<div class="row g-4">
    <div class="col-md-8">
        <div class="table-container">
            <h5 class="mb-3"><i class="fas fa-clock me-2"></i>Recent Stock Updates</h5>
            <table class="table table-hover">
                <thead>
                    <tr><th>Product</th><th>Location</th><th>Current Qty</th><th>Last Updated</th><th>By</th></tr>
                </thead>
                <tbody>
                    <?php 
                    $recent = $conn->query("SELECT p.product_name, s.location_code, s.current_quantity, s.last_updated, s.updated_by FROM stock s JOIN product p ON s.product_id = p.product_id WHERE p.is_active = 1 ORDER BY s.last_updated DESC LIMIT 5");
                    if ($recent->num_rows > 0):
                        while ($row = $recent->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?php echo $row['product_name']; ?></td>
                        <td><?php echo $row['location_code']; ?></td>
                        <td><?php echo $row['current_quantity']; ?></td>
                        <td><?php echo $row['last_updated'] ?: 'Never'; ?></td>
                        <td><?php echo $row['updated_by'] ?: '—'; ?></td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="5" class="text-center text-muted">No recent updates.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-4">
        <div class="table-container">
            <h5 class="mb-3"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
            <div class="d-grid gap-2">
                <?php if (hasPermission('update_stock')): ?>
                <a href="stock_clerk/update_stock.php" class="btn btn-success"><i class="fas fa-plus-circle me-2"></i>Receive Stock</a>
                <?php endif; ?>
                <?php if (hasPermission('generate_reports')): ?>
                <a href="manager/daily_sales.php" class="btn btn-primary"><i class="fas fa-chart-pie me-2"></i>Generate Sales Report</a>
                <?php endif; ?>
                <?php if (hasPermission('manage_products')): ?>
                <a href="admin/add_product.php" class="btn btn-warning"><i class="fas fa-plus me-2"></i>Add New Product</a>
                <?php endif; ?>
                <?php if (hasPermission('manage_users')): ?>
                <a href="admin/add_user.php" class="btn btn-info text-white"><i class="fas fa-user-plus me-2"></i>Add New User</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>