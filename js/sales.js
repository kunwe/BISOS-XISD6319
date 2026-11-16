import { showToast, formatCurrency, getSelectedStore, setSelectedStore } from './app.js?v=2';

const API = 'api/sales.php?action=';

class SalesPage {
    constructor() {
        this.products = [];
        this.filtered = [];
        this.cart = []; // { product_id, name, price, quantity, stock }
        this.storeId = null;
        this.period = 'daily';
        this.init();
    }

    async init() {
        this.bind();
        this.renderCart();
        try {
            await this.loadStores();
            this.storeId = getSelectedStore();
            const sel = document.getElementById('storeSelect');
            if (sel && this.storeId) sel.value = String(this.storeId);
            await this.loadProducts();
            await this.loadSummary();
        } catch (err) {
            console.error(err);
            showToast('Failed to load sales page', 'error');
        }
    }

    bind() {
        document.getElementById('searchInput').addEventListener('input', (e) => {
            const q = e.target.value.toLowerCase().trim();
            this.filtered = this.products.filter(p =>
                !q || p.name.toLowerCase().includes(q) || (p.barcode || '').includes(q)
            );
            this.renderProducts();
        });

        document.getElementById('categoryFilter').addEventListener('change', (e) => {
            const cat = e.target.value;
            const q = document.getElementById('searchInput').value.toLowerCase().trim();
            this.filtered = this.products.filter(p => {
                const matchesCat = !cat || p.category === cat;
                const matchesQ = !q || p.name.toLowerCase().includes(q) || (p.barcode || '').includes(q);
                return matchesCat && matchesQ;
            });
            this.renderProducts();
        });

        document.getElementById('storeSelect').addEventListener('change', async (e) => {
            this.storeId = e.target.value ? parseInt(e.target.value) : null;
            setSelectedStore(this.storeId);
            this.cart = [];
            await this.loadProducts();
            this.renderCart();
            await this.loadSummary();
        });

        document.getElementById('clearCartBtn').addEventListener('click', () => {
            this.cart = [];
            this.renderCart();
        });

        document.getElementById('discount').addEventListener('input', () => this.updateTotals());

        document.getElementById('checkoutBtn').addEventListener('click', () => this.checkout());

        document.getElementById('newSaleBtn').addEventListener('click', () => this.closeReceipt());
        document.getElementById('receiptClose').addEventListener('click', () => this.closeReceipt());

        document.querySelectorAll('.period-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
                e.currentTarget.classList.add('active');
                this.period = e.currentTarget.dataset.period;
                this.loadSummary();
            });
        });
    }

    async loadStores() {
        try {
            const res = await fetch(API + 'stores');
            const json = await res.json();
            const sel = document.getElementById('storeSelect');
            sel.innerHTML = '<option value="">All Stores</option>' +
                (json.data || []).map(s =>
                    `<option value="${s.store_id}">${s.name}</option>`
                ).join('');
            // Keep "All Stores" selected so every product is shown by default.
            this.storeId = null;
        } catch (err) {
            console.error(err);
            showToast('Failed to load stores', 'error');
        }
    }

    async loadProducts() {
        try {
            let url = API + 'products';
            if (this.storeId) url += '&store_id=' + this.storeId;
            const res = await fetch(url);
            const json = await res.json();
            this.products = json.data || [];
            this.filtered = [...this.products];
            this.populateCategories();
            this.renderProducts();
        } catch (err) {
            console.error(err);
            showToast('Failed to load products', 'error');
        }
    }

    populateCategories() {
        const cats = [...new Set(this.products.map(p => p.category).filter(Boolean))].sort();
        const sel = document.getElementById('categoryFilter');
        const current = sel.value;
        sel.innerHTML = '<option value="">All Categories</option>' +
            cats.map(c => `<option value="${c}">${c}</option>`).join('');
        if (cats.includes(current)) sel.value = current;
    }

    renderProducts() {
        const grid = document.getElementById('productGrid');
        if (this.filtered.length === 0) {
            grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;color:var(--text-secondary);padding:2rem">No products found.</div>';
            return;
        }
        grid.innerHTML = this.filtered.map(p => {
            const qty = parseInt(p.quantity);
            const disabled = qty <= 0 ? 'disabled' : '';
            return `
                <div class="product-card ${disabled}" data-id="${p.product_id}">
                    <div style="display:flex;align-items:center">
                        <span class="pc-name">${p.name}</span>
                        <span class="pc-status ${p.status}">${p.status}</span>
                    </div>
                    <span class="pc-price">${formatCurrency(p.selling_price)}</span>
                    <span class="pc-stock">${qty} in stock</span>
                </div>`;
        }).join('');

        grid.querySelectorAll('.product-card:not(.disabled)').forEach(card => {
            card.addEventListener('click', () => this.addToCart(parseInt(card.dataset.id)));
        });
    }

    addToCart(productId) {
        const product = this.products.find(p => parseInt(p.product_id) === parseInt(productId));
        if (!product) return;

        const existing = this.cart.find(c => parseInt(c.product_id) === parseInt(productId));
        const inCart = existing ? existing.quantity : 0;

        if (inCart >= parseInt(product.quantity)) {
            showToast(`Cannot add more ${product.name} — only ${product.quantity} in stock`, 'error');
            return;
        }

        if (existing) {
            existing.quantity += 1;
        } else {
            this.cart.push({
                product_id: product.product_id,
                name: product.name,
                price: parseFloat(product.selling_price),
                quantity: 1,
                stock: parseInt(product.quantity),
                store_id: product.store_id,
                store_name: product.store_name
            });
        }
        this.renderCart();
    }

    changeQty(productId, delta) {
        const item = this.cart.find(c => parseInt(c.product_id) === parseInt(productId));
        if (!item) return;
        item.quantity += delta;
        if (item.quantity <= 0) {
            this.cart = this.cart.filter(c => parseInt(c.product_id) !== parseInt(productId));
        } else if (item.quantity > item.stock) {
            item.quantity = item.stock;
            showToast(`Only ${item.stock} available`, 'error');
        }
        this.renderCart();
    }

    removeItem(productId) {
        this.cart = this.cart.filter(c => parseInt(c.product_id) !== parseInt(productId));
        this.renderCart();
    }

    updateTotals() {
        const subtotal = this.cart.reduce((s, c) => s + c.price * c.quantity, 0);
        const discount = parseFloat(document.getElementById('discount').value) || 0;
        const total = Math.max(0, subtotal - discount);
        document.getElementById('subtotal').textContent = formatCurrency(subtotal);
        document.getElementById('grandTotal').textContent = formatCurrency(total);
    }

    renderCart() {
        const itemsEl = document.getElementById('cartItems');
        const countEl = document.getElementById('cartCount');

        if (this.cart.length === 0) {
            itemsEl.innerHTML = '<div class="cart-empty">Cart is empty. Click a product to add it.</div>';
        } else {
            itemsEl.innerHTML = this.cart.map(c => `
                <div class="cart-item">
                    <span class="ci-name">${c.name}</span>
                    <span class="ci-qty">
                        <button data-act="dec" data-id="${c.product_id}">−</button>
                        <span>${c.quantity}</span>
                        <button data-act="inc" data-id="${c.product_id}">+</button>
                    </span>
                    <span class="ci-total">${formatCurrency(c.price * c.quantity)}</span>
                    <button class="btn btn-sm btn-danger" data-act="remove" data-id="${c.product_id}" style="padding:.2rem .5rem">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `).join('');

            itemsEl.querySelectorAll('button[data-act]').forEach(btn => {
                const id = parseInt(btn.dataset.id);
                const act = btn.dataset.act;
                if (act === 'inc') btn.addEventListener('click', () => this.changeQty(id, +1));
                else if (act === 'dec') btn.addEventListener('click', () => this.changeQty(id, -1));
                else if (act === 'remove') btn.addEventListener('click', () => this.removeItem(id));
            });
        }

        const count = this.cart.reduce((s, c) => s + c.quantity, 0);
        countEl.textContent = `${count} item${count !== 1 ? 's' : ''}`;
        this.updateTotals();
    }

    async checkout() {
        if (this.cart.length === 0) {
            showToast('Cart is empty', 'error');
            return;
        }

        const btn = document.getElementById('checkoutBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

        const discount = parseFloat(document.getElementById('discount').value) || 0;
        const subtotal = this.cart.reduce((s, c) => s + c.price * c.quantity, 0);
        const total = Math.max(0, subtotal - discount);

        // Group cart items by store so a sale is recorded against the right store.
        const groups = {};
        this.cart.forEach(c => {
            const sid = c.store_id || this.storeId || 1;
            if (!groups[sid]) groups[sid] = [];
            groups[sid].push({ product_id: c.product_id, quantity: c.quantity });
        });

        const payment = document.getElementById('paymentMethod').value;
        const customer = document.getElementById('customerName').value.trim();

        try {
            const results = [];
            for (const [sid, items] of Object.entries(groups)) {
                const payload = {
                    store_id: parseInt(sid),
                    payment_method: payment,
                    customer_name: customer,
                    discount_amount: discount,
                    items
                };
                const res = await fetch(API + 'create', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const json = await res.json();
                if (!res.ok || json.status === 'error') {
                    showToast(json.message || 'Sale failed', 'error');
                    return;
                }
                results.push(json);
            }

            // All stores processed successfully.
            const lastRef = results[results.length - 1].reference;
            this.showReceipt({ reference: lastRef, total: total }, total);

            // Refresh products to show updated stock.
            this.cart = [];
            await this.loadProducts();
            this.renderCart();
            // Refresh the sales summary so the totals update immediately.
            await this.loadSummary();
        } catch (err) {
            console.error(err);
            showToast('Network error during checkout', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle"></i> Complete Sale';
        }
    }

    async loadSummary() {
        try {
            let url = `${API}summary&period=${this.period}`;
            if (this.storeId) url += `&store_id=${this.storeId}`;
            const res = await fetch(url);
            const json = await res.json();

            const s = json.summary || {};
            const set = (id, val) => {
                const el = document.getElementById(id);
                if (el) el.textContent = val;
            };

            set('sumTotalSales', formatCurrency(s.total_sales ?? 0));
            set('sumTransactions', s.transactions ?? 0);
            set('sumAvg', formatCurrency(s.avg_transaction ?? 0));
            set('sumCash', formatCurrency((s.by_payment && s.by_payment.CASH) || 0));
            set('sumCard', formatCurrency((s.by_payment && s.by_payment.CARD) || 0));
            set('sumMobile', formatCurrency((s.by_payment && s.by_payment.MOBILE) || 0));
            set('sumPeriodSub', json.label || this.period);
            set('summaryPeriodLabel', json.label ? `— ${json.label}` : '');

            const tbody = document.getElementById('salesTxBody');
            if (!tbody) return;
            const txs = json.transactions || [];
            if (txs.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:var(--text-secondary);padding:1.5rem">No sales in this period.</td></tr>';
            } else {
                tbody.innerHTML = txs.map(tx => `
                    <tr>
                        <td><code>${tx.transaction_ref}</code></td>
                        <td>${tx.transaction_date}</td>
                        <td><span class="status-badge status-ok">${tx.payment_method}</span></td>
                        <td>${tx.customer_name || 'Walk-in'}</td>
                        <td><strong>${formatCurrency(tx.total_amount)}</strong></td>
                    </tr>
                `).join('');
            }
        } catch (err) {
            console.error(err);
        }
    }

    showReceipt(json, total) {
        const body = document.getElementById('receiptBody');
        body.innerHTML = `
            <div class="status-badge status-completed" style="font-size:.8rem;margin-bottom:1rem">
                <i class="fas fa-check-circle"></i> Completed
            </div>
            <p style="font-size:.9rem;color:var(--text-secondary)">Reference</p>
            <p style="font-size:1.1rem;font-weight:700;margin-bottom:1rem">${json.reference}</p>
            <p style="font-size:.9rem;color:var(--text-secondary)">Total Paid</p>
            <p style="font-size:1.8rem;font-weight:700;color:var(--success);margin-bottom:1rem">${formatCurrency(total)}</p>
            <p style="font-size:.8rem;color:var(--text-secondary)">Stock, report &amp; dashboard updated.</p>
        `;
        document.getElementById('receiptOverlay').classList.add('active');
    }

    closeReceipt() {
        document.getElementById('receiptOverlay').classList.remove('active');
    }
}

window.salesPage = new SalesPage();