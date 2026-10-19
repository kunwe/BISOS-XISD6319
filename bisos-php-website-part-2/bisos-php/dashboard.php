<?php
require_once __DIR__ . '/config/config.php';
require_login();

$pageTitle  = 'Dashboard';
$activePage = 'dashboard';
$extraCss   = ['css/dashboard.css'];
$pageScript = 'js/dashboard.js';
include __DIR__ . '/includes/header.php';
?>
<main role="main" aria-label="Dashboard Content">
    <h1 class="sr-only">Dashboard Overview</h1>

    <section class="dashboard-summary" aria-label="Key metrics overview">
        <div class="summary-grid">
            <article class="summary-card card-store">
                <div class="card-icon"><i class="fas fa-store"></i></div>
                <div class="card-content">
                    <h2>Total Stores</h2>
                    <p class="card-value">3</p>
                    <span class="card-change positive">+1 this month</span>
                </div>
            </article>
            <article class="summary-card card-products">
                <div class="card-icon"><i class="fas fa-cube"></i></div>
                <div class="card-content">
                    <h2>Total Products</h2>
                    <p class="card-value">1,247</p>
                    <span class="card-change positive">+12 today</span>
                </div>
            </article>
            <article class="summary-card card-alerts">
                <div class="card-icon"><i class="fas fa-bell"></i></div>
                <div class="card-content">
                    <h2>Low Stock Alerts</h2>
                    <p class="card-value">8</p>
                    <span class="card-change negative">3 critical</span>
                </div>
            </article>
            <article class="summary-card card-sales">
                <div class="card-icon"><i class="fas fa-money-bill-wave"></i></div>
                <div class="card-content">
                    <h2>Today's Sales</h2>
                    <p class="card-value">R4,560</p>
                    <span class="card-change positive">+15% vs yesterday</span>
                </div>
            </article>
        </div>
    </section>

    <section class="dashboard-charts" aria-label="Sales and stock charts">
        <div class="chart-container">
            <h2>Monthly Revenue Trend</h2>
            <div id="barChart"></div>
        </div>
        <div class="chart-container">
            <h2>Stock by Category</h2>
            <div id="donutChart"></div>
        </div>
    </section>

    <div class="dashboard-bottom">
        <section class="recent-activity" aria-label="Recent system activity">
            <div class="section-header">
                <h2>Recent Activity</h2>
                <a href="#">View All</a>
            </div>
            <ul class="activity-list" id="activityList" role="list"></ul>
        </section>

        <section class="quick-actions" aria-label="Quick actions">
            <h2>Quick Actions</h2>
            <div class="action-grid">
                <a href="products.php" class="action-btn"><i class="fas fa-plus"></i> Add Product</a>
                <a href="low-stock-alerts.php" class="action-btn"><i class="fas fa-truck"></i> Place Reorder</a>
                <a href="sales-report.php" class="action-btn"><i class="fas fa-file-alt"></i> Generate Report</a>
                <a href="store-management.php" class="action-btn"><i class="fas fa-handshake"></i> Manage Stores</a>
            </div>
            <div style="margin-top:var(--spacing-md);padding:var(--spacing-md);background:var(--background);border-radius:var(--border-radius)">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem">
                    <span style="font-size:0.85rem;color:var(--text-secondary);font-weight:600">Monthly Revenue</span>
                    <button id="refreshBtn" class="btn btn-sm btn-outline"><i class="fas fa-sync-alt"></i></button>
                </div>
                <p style="font-size:1.6rem;font-weight:700;color:var(--success)">R124,800</p>
                <span style="font-size:0.75rem;color:var(--success)">+8.2% vs last month</span>
            </div>
        </section>
    </div>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>