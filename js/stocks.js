import { showToast, formatCurrency, getSelectedStore, setSelectedStore } from './app.js?v=2';

const API = 'api/stock.php?action=';

class StocksPage {
    constructor() {
        this.stocks = [];
        this.filtered = [];
        this.storeId = null;
        this.editingStockId = null; // for update mode
        this.init();
    }

    async init() {
        this.bind();
        try {
            await this.loadStores();
            this.storeId = getSelectedStore();
            const sel = document.getElementById('storeFilter');
            if (sel && this.storeId) {
                sel.value = String(this.storeId);
                const nameEl = document.getElementById('storeName');
                if (nameEl && sel.options[sel.selectedIndex]) {
                    nameEl.textContent = sel.options[sel.selectedIndex].text;
                }
            }
            await this.loadData();
        } catch (err) {
            console.error(err);
            showToast('Failed to load stock', 'error');
        }
    }

    bind() {
        document.getElementById('searchInput').addEventListener('input', (e) => {
            this.applyFilters();
        });
        document.getElementById('categoryFilter').addEventListener('change', () => this.applyFilters());
        document.getElementById('statusFilter').addEventListener('change', () => this.applyFilters());
        document.getElementById('storeFilter').addEventListener('change', (e) => {
            this.storeId = e.target.value ? parseInt(e.target.value) : null;
            setSelectedStore(this.storeId);
            const name = e.target.options[e.target.selectedIndex]?.text || 'All Stores';
            const nameEl = document.getElementById('storeName');
            if (nameEl) nameEl.textContent = name;
            this.loadData();
        });
        document.getElementById('addStockBtn').addEventListener('click', () => this.openAdd());
        document.getElementById('modalClose').addEventListener('click', () => this.closeModal());
        document.getElementById('modalCancel').addEventListener('click', () => this.closeModal());
        document.getElementById('stockForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.saveStock();
        });
        document.getElementById('exportBtn').addEventListener('click', () => this.exportCsv());
    }

    async loadStores() {
        try {
            const res = await fetch('api/sales.php?action=stores');
            const json = await res.json();
            const sel = document.getElementById('storeFilter');
            const current = sel.value;
            sel.innerHTML = '<option value="">All Stores</option>' +
                (json.data || []).map(s => `<option value="${s.store_id}">${s.name}</option>`).join('');
            if ([...sel.options].some(o => o.value === current)) sel.value = current;
            this.storeId = sel.value ? parseInt(sel.value) : null;
        } catch (err) {
            console.error(err);
        }
    }

    async loadData() {
        try {
            let url = `${API}list`;
            if (this.storeId) url += `&store_id=${this.storeId}`;
            const res = await fetch(url);
            const json = await res.json();
            this.stocks = (json.data || []).map(s => ({
                ...s,
                quantity: parseInt(s.quantity),
                reorder_level: parseInt(s.reorder_level),
                minimum_level: parseInt(s.minimum_level),
                maximum_level: parseInt(s.maximum_level),
            }));
            this.populateCategories();
            this.applyFilters();
        } catch (err) {
            console.error(err);
            showToast('Failed to load stocks', 'error');
        }
    }

    populateCategories() {
        const cats = [...new Set(this.stocks.map(s => s.category).filter(Boolean))].sort();
        const sel = document.getElementById('categoryFilter');
        const current = sel.value;
        sel.innerHTML = '<option value="">All Categories</option>' +
            cats.map(c => `<option value="${c}">${c}</option>`).join('');
        if (cats.includes(current)) sel.value = current;
    }

    applyFilters() {
        const q = (document.getElementById('searchInput').value || '').toLowerCase().trim();
        const cat = document.getElementById('categoryFilter').value;
        const status = document.getElementById('statusFilter').value;

        this.filtered = this.stocks.filter(s => {
            const matchQ = !q || s.name.toLowerCase().includes(q);
            const matchCat = !cat || s.category === cat;
            let matchStatus = true;
            if (status === 'LOW') matchStatus = s.quantity > 0 && s.quantity <= (s.minimum_level || s.reorder_level);
            else if (status === 'OK') matchStatus = s.quantity > (s.minimum_level || s.reorder_level);
            else if (status === 'OUT') matchStatus = s.quantity <= 0;
            return matchQ && matchCat && matchStatus;
        });
        this.render();
    }

    getStatus(stock) {
        const qty = stock.quantity;
        const min = stock.minimum_level || stock.reorder_level;
        if (qty <= 0) return { label: 'OUT', cls: 'status-low' };
        if (qty <= min) return { label: 'LOW', cls: 'status-low' };
        return { label: 'OK', cls: 'status-ok' };
    }

    render() {
        const low = this.stocks.filter(s => s.quantity > 0 && s.quantity <= (s.minimum_level || s.reorder_level)).length;
        const out = this.stocks.filter(s => s.quantity <= 0).length;
        document.getElementById('alertSummary').innerHTML = `
            <div class="alert-chip critical"><i class="fas fa-exclamation-circle"></i> ${out} Out of Stock</div>
            <div class="alert-chip warning"><i class="fas fa-exclamation-triangle"></i> ${low} Low</div>
            <div class="alert-chip ok"><i class="fas fa-check-circle"></i> ${this.stocks.length - low - out} OK</div>
        `;

        const tbody = document.getElementById('stockTableBody');
        if (this.filtered.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:var(--text-secondary);padding:2rem">No stock items found.</td></tr>';
        } else {
            tbody.innerHTML = this.filtered.map(s => {
                const status = this.getStatus(s);
                const max = s.maximum_level || Math.max(s.quantity, 1);
                const pct = Math.min(100, Math.round((s.quantity / max) * 100));
                const barClass = pct < 50 ? 'low' : 'ok';
                return `
                    <tr>
                        <td><strong>${s.name}</strong></td>
                        <td>${s.category || '-'}</td>
                        <td>
                            <div class="stock-level-bar">
                                <div class="stock-bar-track"><div class="stock-bar-fill ${barClass}" style="width:${pct}%"></div></div>
                                <span class="stock-qty">${s.quantity}</span>
                            </div>
                        </td>
                        <td>${s.minimum_level || s.reorder_level}</td>
                        <td>${s.location_code || '-'}</td>
                        <td><span class="status-badge ${status.cls}">${status.label}</span></td>
                        <td>
                            <div style="display:flex;gap:0.4rem">
                                <button class="btn btn-sm btn-outline" data-edit="${s.stock_id}" title="Update"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-danger" data-del="${s.stock_id}" title="Remove"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>`;
            }).join('');

            tbody.querySelectorAll('[data-edit]').forEach(btn => {
                btn.addEventListener('click', () => this.openEdit(parseInt(btn.dataset.edit)));
            });
            tbody.querySelectorAll('[data-del]').forEach(btn => {
                btn.addEventListener('click', () => this.deleteStock(parseInt(btn.dataset.del)));
            });
        }
        document.getElementById('paginationInfo').textContent =
            `Showing ${this.filtered.length} of ${this.stocks.length} items`;
    }

    async openAdd() {
        this.editingStockId = null;
        document.getElementById('modalTitle').textContent = 'Add Stock';
        document.getElementById('stockForm').reset();
        document.getElementById('fieldQuantity').value = 0;
        document.getElementById('fieldReorderLevel').value = 0;
        document.getElementById('fieldMax').value = 999;
        // Load products for the dropdown.
        await this.loadProductOptions(null);
        document.getElementById('modalOverlay').classList.add('active');
    }

    async openEdit(stockId) {
        const stock = this.stocks.find(s => parseInt(s.stock_id) === parseInt(stockId));
        if (!stock) return;
        this.editingStockId = stockId;
        document.getElementById('modalTitle').textContent = 'Update Stock — ' + stock.name;
        document.getElementById('fieldQuantity').value = stock.quantity;
        document.getElementById('fieldReorderLevel').value = stock.minimum_level || stock.reorder_level;
        document.getElementById('fieldMax').value = stock.maximum_level;
        document.getElementById('fieldLocation').value = stock.location_code || '';
        // For edit, we show the product name only (read-only concept); populate options then select.
        await this.loadProductOptions(stock.product_id);
        document.getElementById('fieldProduct').value = stock.product_id;
        document.getElementById('fieldProduct').disabled = true;
        document.getElementById('modalOverlay').classList.add('active');
    }

    async loadProductOptions(selectedId) {
        try {
            let url = 'api/products.php?action=list';
            if (this.storeId) url += `&store_id=${this.storeId}`;
            const res = await fetch(url);
            const json = await res.json();
            const sel = document.getElementById('fieldProduct');
            sel.innerHTML = '<option value="">Select product</option>' +
                (json.data || []).map(p => `<option value="${p.product_id}">${p.name} (${p.category || '—'})</option>`).join('');
            if (selectedId) sel.value = selectedId;
            if (!this.editingStockId) sel.disabled = false;
        } catch (err) {
            console.error(err);
        }
    }

    closeModal() {
        document.getElementById('modalOverlay').classList.remove('active');
        const prodSel = document.getElementById('fieldProduct');
        if (prodSel) prodSel.disabled = false;
    }

    async saveStock() {
        const payload = {
            quantity: parseInt(document.getElementById('fieldQuantity').value) || 0,
            minimum_level: parseInt(document.getElementById('fieldReorderLevel').value) || 0,
            maximum_level: parseInt(document.getElementById('fieldMax').value) || 999,
            location_code: document.getElementById('fieldLocation').value.trim()
        };

        try {
            if (this.editingStockId) {
                // Update existing stock row.
                payload.stock_id = this.editingStockId;
                const res = await fetch(API + 'update', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const json = await res.json();
                if (!res.ok || json.status === 'error') {
                    showToast(json.message || 'Update failed', 'error');
                    return;
                }
                showToast('Stock updated', 'success');
            } else {
                // Add stock to an existing product.
                const productId = parseInt(document.getElementById('fieldProduct').value);
                if (!productId) {
                    showToast('Select a product', 'error');
                    return;
                }
                payload.product_id = productId;
                const res = await fetch(API + 'create', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const json = await res.json();
                if (!res.ok || json.status === 'error') {
                    showToast(json.message || 'Add failed', 'error');
                    return;
                }
                showToast('Stock added', 'success');
            }
            this.closeModal();
            await this.loadData();
        } catch (err) {
            console.error(err);
            showToast('Network error', 'error');
        }
    }

    async deleteStock(stockId) {
        const stock = this.stocks.find(s => parseInt(s.stock_id) === parseInt(stockId));
        const label = stock ? stock.name : 'this stock';
        if (!confirm(`Remove stock record for "${label}"? The product will remain in the catalogue.`)) {
            return;
        }
        try {
            const res = await fetch(API + 'delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ stock_id: stockId })
            });
            const json = await res.json();
            if (!res.ok || json.status === 'error') {
                showToast(json.message || 'Delete failed', 'error');
                return;
            }
            showToast('Stock removed', 'success');
            await this.loadData();
        } catch (err) {
            console.error(err);
            showToast('Network error', 'error');
        }
    }

    exportCsv() {
        const header = ['Product', 'Category', 'Quantity', 'Reorder Level', 'Location'];
        const rows = this.filtered.map(s => [s.name, s.category, s.quantity, s.minimum_level || s.reorder_level, s.location_code]);
        const csv = [header, ...rows].map(r => r.map(c => `"${c}"`).join(',')).join('\n');
        const blob = new Blob([csv], { type: 'text/csv' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'stocks.csv';
        a.click();
        showToast('Exported', 'success');
    }
}

window.stocksPage = new StocksPage();