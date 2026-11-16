import { showToast, getSelectedStore, setSelectedStore } from './app.js?v=2';

const API = 'api/alerts.php?action=';

class AlertsPage {
    constructor() {
        this.alerts = [];
        this.filtered = [];
        this.currentFilter = 'all';
        this.storeId = null;
        this.init();
    }

    async init() {
        this.bind();
        try {
            await this.loadStores();
            this.storeId = getSelectedStore();
            const sel = document.getElementById('storeFilter');
            if (sel && this.storeId) sel.value = String(this.storeId);
            await this.loadAlerts();
        } catch (err) {
            console.error(err);
            showToast('Failed to load alerts', 'error');
        }
    }

    bind() {
        const storeFilter = document.getElementById('storeFilter');
        if (storeFilter) {
            storeFilter.addEventListener('change', (e) => {
                this.storeId = e.target.value ? parseInt(e.target.value) : null;
                setSelectedStore(this.storeId);
                this.loadAlerts();
            });
        }

        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.addEventListener('click', (e) => {
                document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
                e.currentTarget.classList.add('active');
                this.currentFilter = e.currentTarget.dataset.filter;
                this.applyFilter();
            });
        });

        document.getElementById('acknowledgeAllBtn')?.addEventListener('click', () => this.bulkAction('ACKNOWLEDGED'));
        document.getElementById('resolveAllBtn')?.addEventListener('click', () => this.bulkAction('RESOLVED'));
        document.getElementById('placeAllReordersBtn')?.addEventListener('click', () => {
            showToast('Re-order feature coming soon', 'info');
        });
    }

    async loadStores() {
        try {
            const res = await fetch('api/sales.php?action=stores');
            const json = await res.json();
            const sel = document.getElementById('storeFilter');
            if (sel && json.data) {
                sel.innerHTML = '<option value="">All Stores</option>' +
                    json.data.map(s => `<option value="${s.store_id}">${s.name}</option>`).join('');
            }
        } catch (err) {
            console.error(err);
        }
    }

    async loadAlerts() {
        try {
            let url = API + 'list';
            if (this.storeId) url += '&store_id=' + this.storeId;
            const res = await fetch(url);
            const json = await res.json();
            this.alerts = json.data || [];
            this.applyFilter();
        } catch (err) {
            console.error(err);
            showToast('Failed to load alerts', 'error');
        }
    }

    applyFilter() {
        if (this.currentFilter === 'all') {
            this.filtered = [...this.alerts];
        } else {
            this.filtered = this.alerts.filter(a => a.status.toLowerCase() === this.currentFilter);
        }
        this.render();
    }

    render() {
        document.getElementById('pendingCount').textContent =
            this.alerts.filter(a => a.status === 'PENDING').length;
        document.getElementById('criticalCount').textContent =
            this.alerts.filter(a => a.severity === 'CRITICAL').length;
        document.getElementById('resolvedCount').textContent =
            this.alerts.filter(a => a.status === 'RESOLVED').length;

        const tbody = document.getElementById('alertsTableBody');
        if (this.filtered.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:var(--text-secondary);padding:2rem">No alerts. All stock levels are healthy.</td></tr>';
            return;
        }

        tbody.innerHTML = this.filtered.map(a => {
            const sevClass = a.severity === 'CRITICAL' || a.severity === 'HIGH' ? 'status-low' : 'status-warning';
            const statusClass = 'status-' + a.status.toLowerCase();
            const editable = a.alert_id !== null && a.alert_id !== undefined;
            const actions = editable
                ? `<button class="btn btn-sm btn-outline" data-ack="${a.alert_id}" title="Acknowledge"><i class="fas fa-check"></i></button>
                   <button class="btn btn-sm btn-success" data-res="${a.alert_id}" title="Resolve"><i class="fas fa-check-double"></i></button>`
                : `<span class="status-badge status-pending" style="font-size:.7rem">auto-detected</span>`;
            return `
                <tr>
                    <td><strong>${a.product_name}</strong></td>
                    <td>${a.store_name}</td>
                    <td><strong style="color:var(--danger)">${a.current_quantity}</strong></td>
                    <td>${a.reorder_level}</td>
                    <td><span class="status-badge ${sevClass}">${a.severity}</span></td>
                    <td><span class="status-badge ${statusClass}">${a.status}</span></td>
                    <td><div style="display:flex;gap:0.4rem">${actions}</div></td>
                </tr>`;
        }).join('');

        tbody.querySelectorAll('[data-ack]').forEach(btn => {
            btn.addEventListener('click', () => this.updateAlert(parseInt(btn.dataset.ack), 'ACKNOWLEDGED'));
        });
        tbody.querySelectorAll('[data-res]').forEach(btn => {
            btn.addEventListener('click', () => this.updateAlert(parseInt(btn.dataset.res), 'RESOLVED'));
        });
    }

    async updateAlert(alertId, status) {
        try {
            const res = await fetch(API + 'update', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ alert_id: alertId, status })
            });
            const json = await res.json();
            if (!res.ok || json.status === 'error') {
                showToast(json.message || 'Update failed', 'error');
                return;
            }
            showToast('Alert ' + status.toLowerCase(), 'success');
            await this.loadAlerts();
        } catch (err) {
            console.error(err);
            showToast('Network error', 'error');
        }
    }

    async bulkAction(status) {
        const ids = this.alerts
            .filter(a => a.status === 'PENDING' && a.alert_id !== null && a.alert_id !== undefined)
            .map(a => a.alert_id);
        if (ids.length === 0) {
            showToast('No pending alerts to update', 'info');
            return;
        }
        let done = 0;
        for (const id of ids) {
            try {
                const res = await fetch(API + 'update', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ alert_id: id, status })
                });
                const json = await res.json();
                if (res.ok && json.status === 'success') done++;
            } catch (err) {
                console.error(err);
            }
        }
        showToast(`${done} alert(s) ${status.toLowerCase()}`, 'success');
        await this.loadAlerts();
    }
}

window.alertsPage = new AlertsPage();