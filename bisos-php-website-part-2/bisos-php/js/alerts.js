import { showToast } from './app.js';

const MOCK_ALERTS = [
    { id: 1, product: 'Milk 2L', store: 'Soweto', current: 15, reorderLevel: 20, severity: 'HIGH', status: 'PENDING' },
    { id: 2, product: 'Sugar 1kg', store: 'Soweto', current: 8, reorderLevel: 15, severity: 'HIGH', status: 'PENDING' },
    { id: 3, product: 'Chicken 1kg', store: 'Soweto', current: 5, reorderLevel: 10, severity: 'CRITICAL', status: 'PENDING' }
];

class AlertsPage {
    constructor() {
        this.alerts = [...MOCK_ALERTS];
        this.render();
        document.getElementById('acknowledgeAllBtn').addEventListener('click', () => {
            this.alerts.filter(a => a.status === 'PENDING').forEach(a => a.status = 'ACKNOWLEDGED');
            this.render();
            showToast('All pending alerts acknowledged', 'success');
        });
    }

    render() {
        document.getElementById('pendingCount').textContent = this.alerts.filter(a => a.status === 'PENDING').length;
        document.getElementById('criticalCount').textContent = this.alerts.filter(a => a.severity === 'CRITICAL').length;
        
        document.getElementById('alertsTableBody').innerHTML = this.alerts.map(a => `
            <tr>
                <td><strong>${a.product}</strong></td>
                <td>${a.store}</td>
                <td><strong style="color:var(--danger)">${a.current}</strong></td>
                <td>${a.reorderLevel}</td>
                <td><span class="status-badge status-low">${a.severity}</span></td>
                <td><span class="status-badge status-${a.status.toLowerCase()}">${a.status}</span></td>
                <td><button class="btn btn-sm btn-primary">Reorder</button></td>
            </tr>
        `).join('');
    }
}

new AlertsPage();