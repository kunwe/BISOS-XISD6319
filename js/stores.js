import { showToast, formatCurrency } from './app.js?v=2';

const API = 'api/stores.php?action=';

class StoresPage {
    constructor() {
        this.stores = [];
        this.editingId = null;
        this.init();
    }

    async init() {
        this.bind();
        await this.loadStores();
    }

    bind() {
        document.getElementById('addStoreBtn').addEventListener('click', () => this.openAdd());
        document.getElementById('modalClose').addEventListener('click', () => this.closeModal());
        document.getElementById('modalCancel').addEventListener('click', () => this.closeModal());
        document.getElementById('storeForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.saveStore();
        });
    }

    async loadStores() {
        try {
            const res = await fetch(API + 'list');
            const json = await res.json();
            this.stores = json.data || [];
            this.render();
        } catch (err) {
            console.error(err);
            showToast('Failed to load stores', 'error');
            document.getElementById('storesGrid').innerHTML =
                '<p style="color:var(--danger)">Failed to load stores</p>';
        }
    }

    render() {
        const grid = document.getElementById('storesGrid');
        if (this.stores.length === 0) {
            grid.innerHTML = '<p style="color:var(--text-secondary)">No stores yet. Click "Add Store" to create one.</p>';
            return;
        }

        const colors = ['var(--primary)', 'var(--success)', 'var(--warning)', 'var(--primary-light)'];

        grid.innerHTML = this.stores.map((s, i) => {
            const active = s.is_active == 1;
            const statusBadge = active
                ? '<span class="status-badge" style="background:rgba(255,255,255,0.2);color:white">Active</span>'
                : '<span class="status-badge status-low" style="background:rgba(255,255,255,0.2)">Inactive</span>';
            const headerColor = colors[i % colors.length];
            const address = s.address || 'No address';
            const phone = s.phone || 'No phone';
            const email = s.email || 'No email';
            const manager = s.manager_name || 'Unassigned';

            return `
                <article class="store-card">
                    <div class="store-card-header" style="background:${headerColor}">
                        <h2><i class="fas fa-store"></i> ${s.name}</h2>
                        ${statusBadge}
                    </div>
                    <div class="store-card-body">
                        <div class="store-info-row"><i class="fas fa-map-marker-alt"></i><span>${address}</span></div>
                        <div class="store-info-row"><i class="fas fa-phone"></i><span>${phone}</span></div>
                        <div class="store-info-row"><i class="fas fa-envelope"></i><span>${email}</span></div>
                        <div class="store-info-row"><i class="fas fa-user-tie"></i><span>Manager: ${manager}</span></div>
                        <div class="store-stats">
                            <div class="store-stat"><p class="stat-value">${s.product_count}</p><p class="stat-label">Products</p></div>
                            <div class="store-stat"><p class="stat-value" style="color:var(--danger)">${s.alert_count}</p><p class="stat-label">Alerts</p></div>
                            <div class="store-stat"><p class="stat-value" style="color:var(--success)">${formatCurrency(s.today_sales)}</p><p class="stat-label">Today</p></div>
                        </div>
                    </div>
                    <div class="store-card-footer">
                        <a href="stocks.php" class="btn btn-sm btn-primary"><i class="fas fa-boxes"></i> View Stock</a>
                        <button class="btn btn-sm btn-outline" data-edit="${s.store_id}"><i class="fas fa-edit"></i> Edit</button>
                        <button class="btn btn-sm btn-danger" data-del="${s.store_id}"><i class="fas fa-trash"></i></button>
                    </div>
                </article>`;
        }).join('');

        grid.querySelectorAll('[data-edit]').forEach(btn => {
            btn.addEventListener('click', () => this.openEdit(parseInt(btn.dataset.edit)));
        });
        grid.querySelectorAll('[data-del]').forEach(btn => {
            btn.addEventListener('click', () => this.deleteStore(parseInt(btn.dataset.del)));
        });
    }

    openAdd() {
        this.editingId = null;
        document.getElementById('modalTitle').textContent = 'Add Store';
        document.getElementById('storeForm').reset();
        document.getElementById('fieldStoreId').value = '';
        document.getElementById('fieldActive').value = '1';
        document.getElementById('modalOverlay').classList.add('active');
    }

    openEdit(storeId) {
        const s = this.stores.find(x => parseInt(x.store_id) === parseInt(storeId));
        if (!s) return;
        this.editingId = parseInt(storeId);
        document.getElementById('modalTitle').textContent = 'Edit Store';
        document.getElementById('fieldStoreId').value = s.store_id;
        document.getElementById('fieldName').value = s.name || '';
        document.getElementById('fieldAddress').value = s.address || '';
        document.getElementById('fieldPhone').value = s.phone || '';
        document.getElementById('fieldEmail').value = s.email || '';
        document.getElementById('fieldManager').value = s.manager_name || '';
        document.getElementById('fieldActive').value = s.is_active == 1 ? '1' : '0';
        document.getElementById('modalOverlay').classList.add('active');
    }

    closeModal() {
        document.getElementById('modalOverlay').classList.remove('active');
        this.editingId = null;
    }

    async saveStore() {
        const payload = {
            name: document.getElementById('fieldName').value.trim(),
            address: document.getElementById('fieldAddress').value.trim(),
            phone: document.getElementById('fieldPhone').value.trim(),
            email: document.getElementById('fieldEmail').value.trim(),
            manager_name: document.getElementById('fieldManager').value.trim(),
            is_active: document.getElementById('fieldActive').value === '1'
        };

        if (!payload.name) {
            showToast('Store name is required', 'error');
            return;
        }

        try {
            if (this.editingId) {
                payload.store_id = this.editingId;
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
                showToast('Store updated', 'success');
            } else {
                const res = await fetch(API + 'create', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const json = await res.json();
                if (!res.ok || json.status === 'error') {
                    showToast(json.message || 'Create failed', 'error');
                    return;
                }
                showToast('Store created', 'success');
            }
            this.closeModal();
            await this.loadStores();
        } catch (err) {
            console.error(err);
            showToast('Network error', 'error');
        }
    }

    async deleteStore(storeId) {
        const s = this.stores.find(x => parseInt(x.store_id) === parseInt(storeId));
        const label = s ? s.name : 'this store';
        if (!confirm(`Delete "${label}"? This cannot be undone.`)) {
            return;
        }
        try {
            const res = await fetch(API + 'delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ store_id: storeId })
            });
            const json = await res.json();
            if (!res.ok || json.status === 'error') {
                showToast(json.message || 'Delete failed', 'error');
                return;
            }
            showToast('Store removed', 'success');
            await this.loadStores();
        } catch (err) {
            console.error(err);
            showToast('Network error', 'error');
        }
    }
}

window.storesPage = new StoresPage();