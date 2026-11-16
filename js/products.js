import { showToast, formatCurrency, getSelectedStore, setSelectedStore } from './app.js?v=2';

const API = 'api/products.php?action=';

class ProductsPage {
    constructor() {
        this.products = [];
        this.filtered = [];
        this.storeId = null;
        this.editingId = null;
        this.init();
    }

    async init() {
        this.bind();
        try {
            await this.loadStores();
            this.storeId = getSelectedStore();
            const sel = document.getElementById('storeFilter');
            if (sel && this.storeId) sel.value = String(this.storeId);
            await this.loadProducts();
        } catch (err) {
            console.error(err);
            showToast('Failed to load products', 'error');
        }
    }

    bind() {
        document.getElementById('addProductBtn').addEventListener('click', () => this.openAdd());
        document.getElementById('modalClose').addEventListener('click', () => this.closeModal());
        document.getElementById('modalCancel').addEventListener('click', () => this.closeModal());
        document.getElementById('productForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.saveProduct();
        });
        document.getElementById('searchInput').addEventListener('input', (e) => {
            this.applyFilters();
        });
        document.getElementById('categoryFilter').addEventListener('change', () => this.applyFilters());
        document.getElementById('storeFilter').addEventListener('change', (e) => {
            this.storeId = e.target.value ? parseInt(e.target.value) : null;
            setSelectedStore(this.storeId);
            this.loadProducts();
        });
        document.getElementById('exportProductsBtn').addEventListener('click', () => this.exportCsv());
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

    async loadProducts() {
        try {
            // If a store is chosen, query that store; else gather all stores.
            if (this.storeId) {
                const res = await fetch(`${API}list&store_id=${this.storeId}`);
                const json = await res.json();
                this.products = (json.data || []).map(p => ({ ...p, store_id: this.storeId }));
            } else {
                const storesRes = await fetch('api/sales.php?action=stores');
                const storesJson = await storesRes.json();
                const ids = (storesJson.data || []).map(s => s.store_id);
                const results = await Promise.all(ids.map(async (sid) => {
                    const res = await fetch(`${API}list&store_id=${sid}`);
                    const json = await res.json();
                    return (json.data || []).map(p => ({ ...p, store_id: sid }));
                }));
                this.products = [].concat(...results);
            }
            this.populateCategories();
            this.applyFilters();
        } catch (err) {
            console.error(err);
            showToast('Failed to load products', 'error');
            document.getElementById('productsTableBody').innerHTML =
                '<tr><td colspan="9" style="text-align:center;color:var(--danger)">Failed to load products</td></tr>';
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

    applyFilters() {
        const q = (document.getElementById('searchInput').value || '').toLowerCase().trim();
        const cat = document.getElementById('categoryFilter').value;
        this.filtered = this.products.filter(p => {
            const matchQ = !q || p.name.toLowerCase().includes(q) || (p.barcode || '').includes(q);
            const matchCat = !cat || p.category === cat;
            return matchQ && matchCat;
        });
        this.render();
    }

    render() {
        const tbody = document.getElementById('productsTableBody');
        if (this.filtered.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;color:var(--text-secondary);padding:2rem">No products found. Click "Add Product" to create one.</td></tr>';
        } else {
            tbody.innerHTML = this.filtered.map(p => {
                const margin = parseFloat(p.selling_price) > 0
                    ? ((parseFloat(p.selling_price) - parseFloat(p.cost_price)) / parseFloat(p.selling_price) * 100).toFixed(1)
                    : '0.0';
                const qty = parseInt(p.quantity || 0);
                const reorder = parseInt(p.reorder_level || 0);
                const stockBadge = qty <= 0
                    ? '<span class="status-badge status-low">OUT</span>'
                    : (qty <= reorder
                        ? `<span class="status-badge status-warning">${qty} (low)</span>`
                        : `<span class="status-badge status-ok">${qty}</span>`);
                return `
                    <tr>
                        <td><strong>${p.name}</strong></td>
                        <td>${p.category || '-'}</td>
                        <td><code>${p.barcode || '-'}</code></td>
                        <td>${formatCurrency(p.cost_price)}</td>
                        <td>${formatCurrency(p.selling_price)}</td>
                        <td style="color:var(--success)">${margin}%</td>
                        <td>${stockBadge}</td>
                        <td>${p.is_perishable ? '<span class="status-badge status-warning">Yes</span>' : '<span class="status-badge status-ok">No</span>'}</td>
                        <td>
                            <div style="display:flex;gap:0.4rem">
                                <button class="btn btn-sm btn-outline" data-edit="${p.product_id}" title="Edit"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-danger" data-del="${p.product_id}" title="Delete"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>`;
            }).join('');

            tbody.querySelectorAll('[data-edit]').forEach(btn => {
                btn.addEventListener('click', () => this.openEdit(parseInt(btn.dataset.edit)));
            });
            tbody.querySelectorAll('[data-del]').forEach(btn => {
                btn.addEventListener('click', () => this.deleteProduct(parseInt(btn.dataset.del)));
            });
        }
        document.getElementById('paginationInfo').textContent =
            `Showing ${this.filtered.length} of ${this.products.length} products`;
    }

    openAdd() {
        this.editingId = null;
        document.getElementById('modalTitle').textContent = 'Add New Product';
        document.getElementById('productForm').reset();
        document.getElementById('fieldReorder').value = 10;
        document.getElementById('fieldQuantity').value = 0;
        document.getElementById('openingStockGroup').style.display = '';
        document.getElementById('fieldQuantity').required = true;
        document.getElementById('modalOverlay').classList.add('active');
    }

    openEdit(productId) {
        const p = this.products.find(x => parseInt(x.product_id) === parseInt(productId));
        if (!p) return;
        this.editingId = productId;
        document.getElementById('modalTitle').textContent = 'Edit Product';
        document.getElementById('fieldName').value = p.name || '';
        document.getElementById('fieldCategory').value = p.category || '';
        document.getElementById('fieldBarcode').value = p.barcode || '';
        document.getElementById('fieldCost').value = p.cost_price || 0;
        document.getElementById('fieldSelling').value = p.selling_price || 0;
        document.getElementById('fieldReorder').value = p.reorder_level || 0;
        document.getElementById('fieldQuantity').value = p.quantity || 0;
        document.getElementById('fieldLocation').value = p.location_code || '';
        document.getElementById('fieldPerishable').value = p.is_perishable ? '1' : '0';
        // Hide opening-stock field on edit (stock is managed on the Stocks page).
        document.getElementById('openingStockGroup').style.display = 'none';
        document.getElementById('fieldQuantity').required = false;
        document.getElementById('modalOverlay').classList.add('active');
    }

    closeModal() {
        document.getElementById('modalOverlay').classList.remove('active');
        this.editingId = null;
    }

    async saveProduct() {
        const payload = {
            store_id: this.storeId || null,
            name: document.getElementById('fieldName').value.trim(),
            category: document.getElementById('fieldCategory').value,
            barcode: document.getElementById('fieldBarcode').value.trim(),
            cost_price: parseFloat(document.getElementById('fieldCost').value) || 0,
            selling_price: parseFloat(document.getElementById('fieldSelling').value) || 0,
            reorder_level: parseInt(document.getElementById('fieldReorder').value) || 0,
            quantity: parseInt(document.getElementById('fieldQuantity').value) || 0,
            location_code: document.getElementById('fieldLocation').value.trim(),
            is_perishable: document.getElementById('fieldPerishable').value === '1'
        };

        if (!payload.name) { showToast('Product name is required', 'error'); return; }

        const btn = document.getElementById('saveBtn');
        btn.disabled = true;
        try {
            if (this.editingId) {
                payload.product_id = this.editingId;
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
                showToast('Product updated', 'success');
            } else {
                const res = await fetch(API + 'create', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const json = await res.json();
                if (!res.ok || json.status === 'error') {
                    showToast(json.message || 'Failed to add product', 'error');
                    return;
                }
                if (json.generated) {
                    showToast(`Product added — barcode ${json.barcode} generated`, 'success');
                } else {
                    showToast('Product added', 'success');
                }
            }
            this.closeModal();
            await this.loadProducts();
        } catch (err) {
            console.error(err);
            showToast('Network error', 'error');
        } finally {
            btn.disabled = false;
        }
    }

    async deleteProduct(productId) {
        const p = this.products.find(x => parseInt(x.product_id) === parseInt(productId));
        const label = p ? p.name : 'this product';
        if (!confirm(`Delete "${label}"? This removes the product, its stock, and sales history. This cannot be undone.`)) {
            return;
        }
        try {
            const res = await fetch(API + 'delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId })
            });
            const json = await res.json();
            if (!res.ok || json.status === 'error') {
                showToast(json.message || 'Delete failed', 'error');
                return;
            }
            showToast('Product removed', 'success');
            await this.loadProducts();
        } catch (err) {
            console.error(err);
            showToast('Network error', 'error');
        }
    }

    exportCsv() {
        const header = ['Name', 'Category', 'Barcode', 'Cost', 'Selling', 'Stock'];
        const rows = this.filtered.map(p => [p.name, p.category, p.barcode, p.cost_price, p.selling_price, p.quantity]);
        const csv = [header, ...rows].map(r => r.map(c => `"${c}"`).join(',')).join('\n');
        const blob = new Blob([csv], { type: 'text/csv' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'products.csv';
        a.click();
        showToast('Exported', 'success');
    }
}

window.productsPage = new ProductsPage();