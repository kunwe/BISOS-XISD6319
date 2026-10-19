<?php
require_once __DIR__ . '/config/config.php';
require_login();

$pageTitle  = 'Products';
$activePage = 'products';
$extraCss   = ['css/stocks.css'];
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
                <select id="categoryFilter">
                    <option value="">All Categories</option>
                    <option value="Dairy">Dairy</option>
                    <option value="Bakery">Bakery</option>
                    <option value="Pantry">Pantry</option>
                    <option value="Meat">Meat</option>
                    <option value="Grains">Grains</option>
                    <option value="Canned Goods">Canned Goods</option>
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
                    <th>Perishable</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="productsTableBody">
                <tr><td>Milk 2L</td><td>Dairy</td><td><code>6001234567890</code></td><td>R18.50</td><td>R24.99</td><td style="color:var(--success)">35.1%</td><td><span class="status-badge status-warning">Yes</span></td><td><div style="display:flex;gap:0.4rem"><button class="btn btn-sm btn-outline"><i class="fas fa-edit"></i></button><button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></div></td></tr>
                <tr><td>Bread 700g</td><td>Bakery</td><td><code>6001234567891</code></td><td>R12.00</td><td>R18.50</td><td style="color:var(--success)">54.2%</td><td><span class="status-badge status-warning">Yes</span></td><td><div style="display:flex;gap:0.4rem"><button class="btn btn-sm btn-outline"><i class="fas fa-edit"></i></button><button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></div></td></tr>
                <tr><td>Sugar 1kg</td><td>Pantry</td><td><code>6001234567892</code></td><td>R22.00</td><td>R32.00</td><td style="color:var(--success)">45.5%</td><td><span class="status-badge status-ok">No</span></td><td><div style="display:flex;gap:0.4rem"><button class="btn btn-sm btn-outline"><i class="fas fa-edit"></i></button><button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></div></td></tr>
                <tr><td>Cooking Oil 750ml</td><td>Pantry</td><td><code>6001234567893</code></td><td>R45.00</td><td>R58.99</td><td style="color:var(--success)">31.1%</td><td><span class="status-badge status-ok">No</span></td><td><div style="display:flex;gap:0.4rem"><button class="btn btn-sm btn-outline"><i class="fas fa-edit"></i></button><button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></div></td></tr>
                <tr><td>Chicken 1kg</td><td>Meat</td><td><code>6001234567894</code></td><td>R55.00</td><td>R72.99</td><td style="color:var(--success)">32.7%</td><td><span class="status-badge status-warning">Yes</span></td><td><div style="display:flex;gap:0.4rem"><button class="btn btn-sm btn-outline"><i class="fas fa-edit"></i></button><button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></div></td></tr>
                <tr><td>Rice 5kg</td><td>Grains</td><td><code>6001234567895</code></td><td>R65.00</td><td>R85.00</td><td style="color:var(--success)">30.8%</td><td><span class="status-badge status-ok">No</span></td><td><div style="display:flex;gap:0.4rem"><button class="btn btn-sm btn-outline"><i class="fas fa-edit"></i></button><button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></div></td></tr>
                <tr><td>Eggs 30-pack</td><td>Dairy</td><td><code>6001234567899</code></td><td>R42.00</td><td>R56.50</td><td style="color:var(--success)">34.5%</td><td><span class="status-badge status-warning">Yes</span></td><td><div style="display:flex;gap:0.4rem"><button class="btn btn-sm btn-outline"><i class="fas fa-edit"></i></button><button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></div></td></tr>
                <tr><td>Maize Meal 5kg</td><td>Grains</td><td><code>6001234567898</code></td><td>R35.00</td><td>R48.99</td><td style="color:var(--success)">40.0%</td><td><span class="status-badge status-ok">No</span></td><td><div style="display:flex;gap:0.4rem"><button class="btn btn-sm btn-outline"><i class="fas fa-edit"></i></button><button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></div></td></tr>
                <tr><td>Pasta 500g</td><td>Pantry</td><td><code>6001234567896</code></td><td>R12.00</td><td>R16.99</td><td style="color:var(--success)">41.6%</td><td><span class="status-badge status-ok">No</span></td><td><div style="display:flex;gap:0.4rem"><button class="btn btn-sm btn-outline"><i class="fas fa-edit"></i></button><button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></div></td></tr>
                <tr><td>Canned Beans 410g</td><td>Canned Goods</td><td><code>6001234567897</code></td><td>R8.50</td><td>R12.99</td><td style="color:var(--success)">52.8%</td><td><span class="status-badge status-ok">No</span></td><td><div style="display:flex;gap:0.4rem"><button class="btn btn-sm btn-outline"><i class="fas fa-edit"></i></button><button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></div></td></tr>
            </tbody>
        </table>
        <div class="pagination">
            <span>Showing 1–10 of 10 products</span>
            <div class="pagination-controls"><button class="page-btn active">1</button></div>
        </div>
    </div>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>