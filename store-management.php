<?php
require_once __DIR__ . '/config/config.php';
require_login();

$pageTitle   = 'Store Management';
$activePage  = 'stores';
$extraCss    = ['css/stocks.css'];
$pageScript  = 'js/stores.js';
$inlineStyle = '
    .stores-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:var(--spacing-lg); margin-bottom:var(--spacing-lg); }
    .store-card { background:var(--card-bg); border-radius:var(--border-radius); box-shadow:var(--shadow); overflow:hidden; transition:transform 0.3s,box-shadow 0.3s; }
    .store-card:hover { transform:translateY(-4px); box-shadow:0 6px 24px rgba(0,0,0,0.12); }
    .store-card-header { padding:var(--spacing-md) var(--spacing-lg); background:var(--primary); color:white; display:flex; justify-content:space-between; align-items:center; }
    .store-card-header h2 { font-size:1.1rem; }
    .store-card-body { padding:var(--spacing-lg); }
    .store-info-row { display:flex; align-items:center; gap:0.75rem; margin-bottom:0.75rem; font-size:0.875rem; }
    .store-info-row i { color:var(--primary-light); width:16px; text-align:center; }
    .store-stats { display:grid; grid-template-columns:1fr 1fr 1fr; gap:var(--spacing-sm); margin:var(--spacing-md) 0; padding:var(--spacing-md); background:var(--background); border-radius:var(--border-radius); }
    .store-stat { text-align:center; }
    .store-stat .stat-value { font-size:1.2rem; font-weight:700; color:var(--primary); }
    .store-stat .stat-label { font-size:0.7rem; color:var(--text-secondary); text-transform:uppercase; letter-spacing:0.04em; }
    .store-card-footer { padding:var(--spacing-md) var(--spacing-lg); border-top:1px solid var(--background); display:flex; gap:var(--spacing-sm); }
';
include __DIR__ . '/includes/header.php';
?>
<main role="main">
    <div class="page-header">
        <div>
            <h1>Store Management</h1>
            <p>Manage all your store locations and their details</p>
        </div>
        <div class="header-actions">
            <button class="btn btn-primary" id="addStoreBtn"><i class="fas fa-plus"></i> Add Store</button>
        </div>
    </div>

    <div class="stores-grid" id="storesGrid">
        <p style="color:var(--text-secondary)"><i class="fas fa-spinner fa-spin"></i> Loading stores...</p>
    </div>
</main>

<div class="modal-overlay" id="modalOverlay">
    <div class="modal">
        <div class="modal-header">
            <h2 id="modalTitle">Add Store</h2>
            <button class="modal-close" id="modalClose">&times;</button>
        </div>
        <form id="storeForm">
            <input type="hidden" id="fieldStoreId">
            <div class="form-grid">
                <div class="form-group full">
                    <label for="fieldName">Store Name *</label>
                    <input type="text" id="fieldName" required>
                </div>
                <div class="form-group full">
                    <label for="fieldAddress">Address</label>
                    <input type="text" id="fieldAddress">
                </div>
                <div class="form-group">
                    <label for="fieldPhone">Phone</label>
                    <input type="text" id="fieldPhone">
                </div>
                <div class="form-group">
                    <label for="fieldEmail">Email</label>
                    <input type="email" id="fieldEmail">
                </div>
                <div class="form-group full">
                    <label for="fieldManager">Manager Name</label>
                    <input type="text" id="fieldManager">
                </div>
                <div class="form-group full">
                    <label for="fieldActive">Status</label>
                    <select id="fieldActive">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" id="modalCancel">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
            </div>
        </form>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>