<?php
require_once __DIR__ . '/config/config.php';
require_login();

$pageTitle   = 'Sales';
$activePage  = 'sales';
$extraCss    = ['css/sales.css'];
$pageScript  = 'js/sales.js';
$inlineStyle = '
    .pos-layout { display:grid; grid-template-columns:1fr 380px; gap:var(--spacing-lg); align-items:start; }
    .pos-catalog { background:var(--card-bg); border-radius:var(--border-radius); box-shadow:var(--shadow); padding:var(--spacing-lg); }
    .pos-catalog .table-toolbar { padding:0 0 var(--spacing-md); border-bottom:none; }
    .pos-toolbar { display:flex; gap:var(--spacing-sm); flex-wrap:wrap; align-items:center; }
    .pos-toolbar .search-bar { flex:1; max-width:none; }
    .product-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(150px,1fr)); gap:var(--spacing-md); margin-top:var(--spacing-md); max-height:60vh; overflow-y:auto; padding-right:4px; }
    .product-card { background:var(--background); border:2px solid transparent; border-radius:var(--border-radius); padding:var(--spacing-md); cursor:pointer; transition:all .2s; display:flex; flex-direction:column; gap:.3rem; }
    .product-card:hover { border-color:var(--primary-light); transform:translateY(-2px); }
    .product-card.disabled { opacity:.45; cursor:not-allowed; transform:none; }
    .product-card .pc-name { font-weight:600; font-size:.9rem; }
    .product-card .pc-price { font-weight:700; color:var(--success); font-size:1.05rem; }
    .product-card .pc-stock { font-size:.75rem; color:var(--text-secondary); }
    .product-card .pc-status { font-size:.7rem; font-weight:700; margin-left:auto; }
    .cart-panel { background:var(--card-bg); border-radius:var(--border-radius); box-shadow:var(--shadow); padding:var(--spacing-lg); position:sticky; top:90px; }
    .cart-items { max-height:40vh; overflow-y:auto; margin-bottom:var(--spacing-md); }
    .cart-item { display:flex; align-items:center; gap:var(--spacing-sm); padding:.5rem 0; border-bottom:1px solid var(--background); }
    .cart-item .ci-name { flex:1; font-size:.85rem; font-weight:500; }
    .cart-item .ci-qty { display:flex; align-items:center; gap:.3rem; }
    .cart-item .ci-qty button { width:26px; height:26px; border-radius:6px; border:1px solid #E0E0E0; background:white; cursor:pointer; }
    .cart-item .ci-total { font-weight:700; min-width:70px; text-align:right; }
    .cart-empty { text-align:center; color:var(--text-secondary); padding:var(--spacing-lg) 0; font-size:.9rem; }
    .cart-summary { border-top:2px solid var(--background); padding-top:var(--spacing-md); margin-bottom:var(--spacing-md); }
    .cart-summary .row { display:flex; justify-content:space-between; margin-bottom:.35rem; font-size:.9rem; }
    .cart-summary .grand { font-size:1.3rem; font-weight:700; border-top:1px dashed #E0E0E0; padding-top:.5rem; margin-top:.5rem; }
    .sales-summary { margin-top:var(--spacing-lg); background:var(--card-bg); border-radius:var(--border-radius); box-shadow:var(--shadow); padding:var(--spacing-lg); }
    .sales-summary .section-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:var(--spacing-md); flex-wrap:wrap; gap:var(--spacing-sm); }
    .sales-summary .section-header h2 { font-size:1rem; font-weight:600; }
    .period-tabs { display:flex; gap:0.3rem; background:var(--background); padding:0.3rem; border-radius:var(--border-radius); width:fit-content; }
    .period-btn { padding:0.45rem 1.1rem; border:none; background:transparent; border-radius:6px; font-size:0.85rem; font-weight:500; cursor:pointer; font-family:var(--font-family); color:var(--text-secondary); }
    .period-btn.active { background:white; color:var(--primary); box-shadow:0 1px 4px rgba(0,0,0,0.1); }
    .sales-kpi-row { display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:var(--spacing-md); margin-bottom:var(--spacing-md); }
    .sales-kpi { background:var(--background); padding:var(--spacing-md); border-radius:var(--border-radius); text-align:center; }
    .sales-kpi .label { font-size:0.7rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-secondary); font-weight:600; }
    .sales-kpi .value { font-size:1.3rem; font-weight:700; margin:0.25rem 0; }
    .sales-kpi .sub { font-size:0.7rem; color:var(--text-secondary); }
    .sales-tx-table { max-height:300px; overflow-y:auto; }
    @media (max-width: 1024px) { .pos-layout { grid-template-columns:1fr; } .cart-panel { position:static; } }
';
include __DIR__ . '/includes/header.php';
?>
<main role="main" aria-label="Point of Sale">
    <div class="page-header">
        <div>
            <h1>Sales &mdash; Point of Sale</h1>
            <p>Sell products and automatically update stock, reports and dashboard.</p>
        </div>
        <div class="header-actions">
            <select id="storeSelect" aria-label="Select store">
                <option value="">All Stores</option>
            </select>
            <button class="btn btn-outline" id="clearCartBtn"><i class="fas fa-trash"></i> Clear</button>
        </div>
    </div>

    <div class="pos-layout">
        <section class="pos-catalog" aria-label="Products">
            <div class="pos-toolbar">
                <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="search" id="searchInput" placeholder="Search products or scan barcode...">
                </div>
                <select id="categoryFilter">
                    <option value="">All Categories</option>
                </select>
            </div>
            <div class="product-grid" id="productGrid">
                <div style="padding:2rem;text-align:center;color:var(--text-secondary)"><i class="fas fa-spinner fa-spin"></i> Loading...</div>
            </div>
        </section>

        <aside class="cart-panel" aria-label="Cart">
            <div class="modal-header" style="padding:0;margin-bottom:var(--spacing-md)">
                <h2><i class="fas fa-shopping-cart"></i> Cart</h2>
                <span class="status-badge status-ok" id="cartCount">0 items</span>
            </div>
            <div class="cart-items" id="cartItems">
                <div class="cart-empty">Cart is empty. Click a product to add it.</div>
            </div>
            <div class="cart-summary">
                <div class="row"><span>Subtotal</span><span id="subtotal">R0.00</span></div>
                <div class="row"><span>Discount</span>
                    <input type="number" id="discount" min="0" step="0.01" value="0"
                           style="width:90px;text-align:right;border:1px solid #E0E0E0;border-radius:6px;padding:.2rem .4rem">
                </div>
                <div class="row grand"><span>Total</span><span id="grandTotal">R0.00</span></div>
            </div>
            <div class="form-group">
                <label for="paymentMethod">Payment Method</label>
                <select id="paymentMethod">
                    <option value="CASH">Cash</option>
                    <option value="CARD">Card</option>
                    <option value="MOBILE">Mobile</option>
                    <option value="CREDIT">Credit</option>
                </select>
            </div>
            <div class="form-group">
                <label for="customerName">Customer (optional)</label>
                <input type="text" id="customerName" placeholder="Walk-in customer">
            </div>
            <button class="btn btn-success btn-block" id="checkoutBtn">
                <i class="fas fa-check-circle"></i> Complete Sale
            </button>
        </aside>
    </div>

    <section class="sales-summary" aria-label="Sales summary">
        <div class="section-header">
            <h2><i class="fas fa-chart-line"></i> Sales Summary <span id="summaryPeriodLabel" style="font-size:0.8rem;color:var(--text-secondary);font-weight:400"></span></h2>
            <div class="period-tabs">
                <button class="period-btn active" data-period="daily">Daily</button>
                <button class="period-btn" data-period="weekly">Weekly</button>
                <button class="period-btn" data-period="monthly">Monthly</button>
            </div>
        </div>
        <div class="sales-kpi-row">
            <div class="sales-kpi"><p class="label">Total Sales</p><p class="value" id="sumTotalSales" style="color:var(--success)">R0.00</p><p class="sub" id="sumPeriodSub">Today</p></div>
            <div class="sales-kpi"><p class="label">Transactions</p><p class="value" id="sumTransactions">0</p><p class="sub">This period</p></div>
            <div class="sales-kpi"><p class="label">Avg. Transaction</p><p class="value" id="sumAvg">R0.00</p><p class="sub">Per sale</p></div>
            <div class="sales-kpi"><p class="label">Cash</p><p class="value" id="sumCash" style="color:var(--success)">R0.00</p></div>
            <div class="sales-kpi"><p class="label">Card</p><p class="value" id="sumCard" style="color:var(--primary-light)">R0.00</p></div>
            <div class="sales-kpi"><p class="label">Mobile</p><p class="value" id="sumMobile" style="color:var(--warning)">R0.00</p></div>
        </div>
        <div class="table-wrapper sales-tx-table">
            <table>
                <thead>
                    <tr><th>Reference</th><th>Date</th><th>Payment</th><th>Customer</th><th>Amount</th></tr>
                </thead>
                <tbody id="salesTxBody">
                    <tr><td colspan="5" style="text-align:center;color:var(--text-secondary);padding:1.5rem">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </section>
</main>

<div class="modal-overlay" id="receiptOverlay">
    <div class="modal" style="max-width:420px">
        <div class="modal-header">
            <h2><i class="fas fa-receipt"></i> Sale Complete</h2>
            <button class="modal-close" id="receiptClose">&times;</button>
        </div>
        <div id="receiptBody" style="text-align:center;padding:var(--spacing-md) 0"></div>
        <div class="modal-footer">
            <button class="btn btn-outline" id="newSaleBtn"><i class="fas fa-plus"></i> New Sale</button>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>