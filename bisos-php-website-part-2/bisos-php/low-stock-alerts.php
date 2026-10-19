<?php
require_once __DIR__ . '/config/config.php';
require_login();

$pageTitle  = 'Low Stock Alerts';
$activePage = 'alerts';
$pageScript = 'js/alerts.js';
$inlineStyle = '
    .alerts-kpi { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:var(--spacing-md); margin-bottom:var(--spacing-lg); }
    .kpi-card { background:var(--card-bg); padding:var(--spacing-md); border-radius:var(--border-radius); box-shadow:var(--shadow); text-align:center; border-top:3px solid transparent; }
    .kpi-card.danger { border-top-color:var(--danger); }
    .kpi-card.warning { border-top-color:var(--warning); }
    .kpi-card.success { border-top-color:var(--success); }
    .kpi-card .kpi-value { font-size:2rem; font-weight:700; }
    .kpi-card.danger .kpi-value { color:var(--danger); }
    .kpi-card.warning .kpi-value { color:var(--warning); }
    .kpi-card.success .kpi-value { color:var(--success); }
    .kpi-card .kpi-label { font-size:0.8rem; color:var(--text-secondary); font-weight:600; text-transform:uppercase; }
    .bulk-actions { display:flex; gap:var(--spacing-sm); margin-bottom:var(--spacing-md); flex-wrap:wrap; align-items:center; }
    .bulk-actions span { font-size:0.875rem; color:var(--text-secondary); margin-right:0.5rem; }
    .filter-tabs { display:flex; gap:0.3rem; margin-bottom:var(--spacing-md); }
    .filter-tab { padding:0.45rem 1rem; border:2px solid #E0E0E0; border-radius:20px; background:white; font-size:0.85rem; font-weight:500; cursor:pointer; font-family:var(--font-family); color:var(--text-secondary); }
    .filter-tab.active { background:var(--primary); color:white; border-color:var(--primary); }
    .alert-icon { font-size:1.5rem; margin-right:0.5rem; }
';
include __DIR__ . '/includes/header.php';
?>
<main role="main">
    <div class="page-header">
        <div>
            <h1><span class="alert-icon">⚠️</span> Low Stock Alerts</h1>
            <p>Monitor and manage inventory alerts across all stores</p>
        </div>
    </div>

    <div class="alerts-kpi">
        <div class="kpi-card danger"><p class="kpi-value" id="pendingCount">0</p><p class="kpi-label">Pending Alerts</p></div>
        <div class="kpi-card warning"><p class="kpi-value" id="criticalCount">0</p><p class="kpi-label">Critical Items</p></div>
        <div class="kpi-card success"><p class="kpi-value" id="resolvedCount">0</p><p class="kpi-label">Resolved Today</p></div>
    </div>

    <div class="filter-tabs">
        <button class="filter-tab active" data-filter="all">All Alerts</button>
        <button class="filter-tab" data-filter="pending">Pending</button>
        <button class="filter-tab" data-filter="acknowledged">Acknowledged</button>
        <button class="filter-tab" data-filter="resolved">Resolved</button>
    </div>

    <div class="bulk-actions">
        <span>Bulk Actions:</span>
        <button class="btn btn-warning" id="acknowledgeAllBtn"><i class="fas fa-check"></i> Acknowledge All</button>
        <button class="btn btn-success" id="resolveAllBtn"><i class="fas fa-check-double"></i> Resolve All</button>
        <button class="btn btn-primary" id="placeAllReordersBtn"><i class="fas fa-truck"></i> Place All Reorders</button>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Product</th><th>Store</th><th>Current Stock</th><th>Reorder Level</th><th>Severity</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody id="alertsTableBody"></tbody>
        </table>
    </div>

    <div style="margin-top:var(--spacing-md);text-align:center">
        <a href="#" class="btn btn-outline"><i class="fas fa-history"></i> View Full Alert History</a>
    </div>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>