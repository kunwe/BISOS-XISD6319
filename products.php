<?php
require_once __DIR__ . '/config/config.php';
require_login();

$pageTitle   = 'Products';
$activePage  = 'products';
$extraCss    = ['css/stocks.css'];
$pageScript  = 'js/products.js';
include __DIR__ . '/includes/header.php';
?>
<main role="main">
    <div class="page-header">
        <div>
            <h1>Product Management</h1>
            <p>Manage your product catalogue across all stores</p>
        </div>
        <div class="header-actions">
            <button class="btn btn-primary" id="addProductBtn"><i class="fas fa-plus"></i> Add Product</button>
            <button class="btn btn-outline" id="exportProductsBtn"><i class="fas fa-download"></i> Export</button>
        </div>
    </div>

    <div class="table-wrapper">
        <div class="table-toolbar">
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="search" id="searchInput" placeholder="Search products, barcodes...">
            </div>
            <div class="table-actions">
                <select id="storeFilter">
                    <option value="">All Stores</option>
                </select>
                <select id="categoryFilter">
                    <option value="">All Categories</option>
                </select>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Barcode</th>
                    <th>Cost Price</th>
                    <th>Selling Price</th>
                    <th>Margin</th>
                    <th>Stock</th>
                    <th>Perishable</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="productsTableBody">
                <tr><td colspan="9" style="text-align:center;padding:2rem"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>
            </tbody>
        </table>
        <div class="pagination">
            <span id="paginationInfo">Loading...</span>
        </div>
    </div>
</main>

<div class="modal-overlay" id="modalOverlay">
    <div class="modal">
        <div class="modal-header">
            <h2 id="modalTitle">Add New Product</h2>
            <button class="modal-close" id="modalClose">&times;</button>
        </div>
        <form id="productForm">
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
                        <option value="Produce">Produce</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="fieldBarcode">Barcode <span style="font-weight:400;color:var(--text-secondary)">(leave blank to auto-generate)</span></label>
                    <input type="text" id="fieldBarcode" placeholder="Auto-generated if empty">
                </div>
                <div class="form-group">
                    <label for="fieldCost">Cost Price (R) *</label>
                    <input type="number" id="fieldCost" min="0" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="fieldSelling">Selling Price (R) *</label>
                    <input type="number" id="fieldSelling" min="0" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="fieldReorder">Reorder Level *</label>
                    <input type="number" id="fieldReorder" min="0" value="10" required>
                </div>
                <div class="form-group" id="openingStockGroup">
                    <label for="fieldQuantity">Opening Stock Qty *</label>
                    <input type="number" id="fieldQuantity" min="0" value="0" required>
                </div>
                <div class="form-group">
                    <label for="fieldLocation">Location Code</label>
                    <input type="text" id="fieldLocation" placeholder="e.g. A-01">
                </div>
                <div class="form-group">
                    <label for="fieldPerishable">Perishable</label>
                    <select id="fieldPerishable">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" id="modalCancel">Cancel</button>
                <button type="submit" class="btn btn-primary" id="saveBtn"><i class="fas fa-save"></i> Save</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>