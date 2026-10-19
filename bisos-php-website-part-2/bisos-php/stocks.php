<?php
require_once __DIR__ . '/config/config.php';
require_login();

$pageTitle  = 'Stock Management';
$activePage = 'stocks';
$extraCss   = ['css/stocks.css'];
$pageScript = 'js/stocks.js';
include __DIR__ . '/includes/header.php';
?>
<main role="main" aria-label="Stock Management">
    <div class="page-header">
        <div>
            <h1>Stock Management</h1>
            <p>Store: Soweto Branch &mdash; Last updated: <span id="lastUpdated">Just now</span></p>
        </div>
        <div class="header-actions">
            <button class="btn btn-primary" id="addStockBtn"><i class="fas fa-plus"></i> Add New Stock</button>
            <button class="btn btn-outline" id="exportBtn"><i class="fas fa-download"></i> Export CSV</button>
        </div>
    </div>

    <div class="alert-summary" id="alertSummary"></div>

    <div class="table-wrapper">
        <div class="table-toolbar">
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="search" id="searchInput" placeholder="Search products...">
            </div>
            <div class="table-actions">
                <select id="categoryFilter">
                    <option value="">All Categories</option>
                    <option value="Dairy">Dairy</option>
                    <option value="Bakery">Bakery</option>
                    <option value="Pantry">Pantry</option>
                    <option value="Meat">Meat</option>
                    <option value="Grains">Grains</option>
                    <option value="Canned Goods">Canned Goods</option>
                </select>
                <select id="statusFilter">
                    <option value="">All Status</option>
                    <option value="low">Low Stock</option>
                    <option value="ok">OK</option>
                </select>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Stock Level</th>
                    <th>Reorder Level</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="stockTableBody">
                <tr><td colspan="7" style="text-align:center;padding:2rem;"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>
            </tbody>
        </table>

        <div class="pagination">
            <span id="paginationInfo">Showing 1–10 of 10 items</span>
            <div class="pagination-controls">
                <button class="page-btn active">1</button>
            </div>
        </div>
    </div>
</main>

<div class="modal-overlay" id="modalOverlay">
    <div class="modal">
        <div class="modal-header">
            <h2 id="modalTitle">Add New Stock Item</h2>
            <button class="modal-close" id="modalClose">&times;</button>
        </div>
        <form id="stockForm">
            <div class="form-grid">
                <div class="form-group full">
                    <label for="fieldName">Product Name *</label>
                    <input type="text" id="fieldName" required>
                </div>
                <div class="form-group">
                    <label for="fieldCategory">Category *</label>
                    <select id="fieldCategory" required>
                        <option value="">Select category</option>
                        <option value="Dairy">Dairy</option>
                        <option value="Bakery">Bakery</option>
                        <option value="Pantry">Pantry</option>
                        <option value="Meat">Meat</option>
                        <option value="Grains">Grains</option>
                        <option value="Canned Goods">Canned Goods</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="fieldLocation">Location Code</label>
                    <input type="text" id="fieldLocation">
                </div>
                <div class="form-group">
                    <label for="fieldQuantity">Current Quantity *</label>
                    <input type="number" id="fieldQuantity" min="0" required>
                </div>
                <div class="form-group">
                    <label for="fieldReorderLevel">Reorder Level *</label>
                    <input type="number" id="fieldReorderLevel" min="0" required>
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