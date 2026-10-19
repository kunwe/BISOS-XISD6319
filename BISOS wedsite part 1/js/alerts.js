// alerts.js - Low Stock Alerts Logic

import { renderNav, renderFooter, showToast, requireAuth } from './app.js';

const MOCK_ALERTS = [
    { id: 1, product: 'Milk 2L', store: 'Soweto', current: 15, reorderLevel: 20, severity: 'HIGH', status: 'PENDING', created: '2026-03-26T08:00:00' },
    { id: 2, product: 'Sugar 1kg', store: 'Soweto', current: 8, reorderLevel: 15, severity: 'HIGH', status: 'PENDING', created: '2026-03-26T07:30:00' },
    { id: 3, product: 'Chicken 1kg', store: 'Soweto', current: 5, reorderLevel: 10, severity: 'CRITICAL', status: 'PENDING', created: '2026-03-26T07:00:00' },
    { id: 4, product: 'Eggs 30-pack', store: 'Soweto', current: 12, reorderLevel: 15, severity: 'MEDIUM', status: 'PENDING', created: '2026-03-25T16:00:00' },
    { id: 5, product: 'Bread 700g', store: 'Soweto', current: 9, reorderLevel: 10, severity: 'MEDIUM', status: 'ACKNOWLEDGED', created: '2026-03-25T14:00:00' },
    { id: 6, product: 'Maize Meal 5kg', store: 'Soweto', current: 3, reorderLevel: 10, severity: 'CRITICAL', status: 'RESOLVED', created: '2026-03-24T10:00:00' },
];

class AlertsPage {
    constructor() {
        requireAuth();
        renderNav('alerts');
        renderFooter();
        this.alerts = [...MOCK_ALERTS];
        this.activeFilter = 'all';
        this.render();
        this.setupEventListeners();
    }

    get filtered() {
        if (this.activeFilter === 'all') return this.alerts;
        return this.alerts.filter(a => a.status.toLowerCase() === this.activeFilter);
    }

    getSeverityClass(severity) {
        return { CRITICAL: 'status-low', HIGH: 'status-low', MEDIUM: 'status-warning', LOW: 'status-ok' }[severity] || 'status-ok';
    }

    getStatusClass(status) {
        return { PENDING: 'status-pending', ACKNOWLEDGED: 'status-acknowledged', RESOLVED: 'status-resolved' }[status] || '';
    }

    render() {
        const pending = this.alerts.filter(a => a.status === 'PENDING').length;
        const critical = this.alerts.filter(a => a.severity === 'CRITICAL' && a.status === 'PENDING').length;
        const resolved = this.alerts.filter(a => a.status === 'RESOLVED').length;

        document.getElementById('pendingCount').textContent = pending;
        document.getElementById('criticalCount').textContent = critical;
        document.getElementById('resolvedCount').textContent = resolved;

        // Update nav badge
        document.querySelectorAll('.badge').forEach(b => b.textContent = pending);

        const tbody = document.getElementById('alertsTableBody');
        const data = this.filtered;

        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--text-secondary)">No alerts found</td></tr>`;
            return;
        }

        tbody.innerHTML = data.map(a => `
            <tr>
                <td><strong>${a.product}</strong></td>
                <td>${a.store}</td>
                <td><strong style="color:var(--danger)">${a.current}</strong></td>
                <td>${a.reorderLevel}</td>
                <td><span class="status-badge ${this.getSeverityClass(a.severity)}">${a.severity}</span></td>
                <td><span class="status-badge ${this.getStatusClass(a.status)}">${a.status}</span></td>
                <td>
                    <div style="display:flex;gap:0.4rem;flex-wrap:wrap">
                        ${a.status === 'PENDING' ? `
                            <button class="btn btn-sm btn-warning" onclick="alertsPage.acknowledge(${a.id})">
                                <i class="fas fa-check"></i> Acknowledge
                            </button>
                            <button class="btn btn-sm btn-primary" onclick="alertsPage.placeReorder(${a.id})">
                                <i class="fas fa-truck"></i> Reorder
                            </button>
                        ` : ''}
                        ${a.status === 'ACKNOWLEDGED' ? `
                            <button class="btn btn-sm btn-success" onclick="alertsPage.resolve(${a.id})">
                                <i class="fas fa-check-double"></i> Resolve
                            </button>
                        ` : ''}
                        ${a.status === 'RESOLVED' ? `<span style="color:var(--text-secondary);font-size:0.8rem">Resolved</span>` : ''}
                    </div>
                </td>
            </tr>
        `).join('');
    }

    setupEventListeners() {
        document.querySelectorAll('.filter-tab').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                this.activeFilter = btn.dataset.filter;
                this.render();
            });
        });

        document.getElementById('acknowledgeAllBtn').addEventListener('click', () => {
            this.alerts.filter(a => a.status === 'PENDING').forEach(a => a.status = 'ACKNOWLEDGED');
            this.render();
            showToast('All pending alerts acknowledged', 'success');
        });

        document.getElementById('resolveAllBtn').addEventListener('click', () => {
            this.alerts.filter(a => a.status === 'ACKNOWLEDGED').forEach(a => a.status = 'RESOLVED');
            this.render();
            showToast('All acknowledged alerts resolved', 'success');
        });

        document.getElementById('placeAllReordersBtn').addEventListener('click', () => {
            const pending = this.alerts.filter(a => a.status === 'PENDING');
            showToast(`Reorder placed for ${pending.length} items`, 'success');
        });
    }

    acknowledge(id) {
        const alert = this.alerts.find(a => a.id === id);
        if (alert) { alert.status = 'ACKNOWLEDGED'; this.render(); showToast(`${alert.product} alert acknowledged`, 'success'); }
    }

    resolve(id) {
        const alert = this.alerts.find(a => a.id === id);
        if (alert) { alert.status = 'RESOLVED'; this.render(); showToast(`${alert.product} alert resolved`, 'success'); }
    }

    placeReorder(id) {
        const alert = this.alerts.find(a => a.id === id);
        if (alert) { showToast(`Reorder placed for ${alert.product}`, 'success'); }
    }
}

const alertsPage = new AlertsPage();
window.alertsPage = alertsPage;
