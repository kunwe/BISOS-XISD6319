import { showToast, formatCurrency } from './app.js';

const MOCK_SALES = [
    { id: 'A-4521', date: '2026-03-01', product: 'Milk 2L', qty: 12, price: 24.99, total: 299.88, payment: 'Cash', store: 'Soweto' },
    { id: 'A-4522', date: '2026-03-01', product: 'Bread 700g', qty: 8, price: 18.50, total: 148.00, payment: 'Card', store: 'Soweto' },
    { id: 'A-4523', date: '2026-03-02', product: 'Sugar 1kg', qty: 5, price: 32.00, total: 160.00, payment: 'Cash', store: 'Soweto' },
    { id: 'A-4524', date: '2026-03-02', product: 'Cooking Oil', qty: 3, price: 58.99, total: 176.97, payment: 'Card', store: 'Soweto' }
];

class SalesReport {
    constructor() {
        this.sales = [...MOCK_SALES];
        this.filtered = [...this.sales];
        this.render();
    }

    get totalSales() { return this.filtered.reduce((sum, s) => sum + s.total, 0); }

    render() {
        document.getElementById('totalSales').textContent = formatCurrency(this.totalSales);
        document.getElementById('totalTransactions').textContent = this.filtered.length;
        document.getElementById('avgTransaction').textContent = formatCurrency(this.totalSales / (this.filtered.length || 1));
        
        const cash = this.filtered.filter(s => s.payment === 'Cash').reduce((sum, s) => sum + s.total, 0);
        document.getElementById('cashSales').textContent = formatCurrency(cash);

        document.getElementById('salesTableBody').innerHTML = this.filtered.map(s => `
            <tr>
                <td><code>${s.id}</code></td>
                <td>${s.date}</td>
                <td>${s.product}</td>
                <td>${s.qty}</td>
                <td>${formatCurrency(s.price)}</td>
                <td><strong>${formatCurrency(s.total)}</strong></td>
                <td><span class="status-badge status-ok">${s.payment}</span></td>
                <td>${s.store}</td>
            </tr>
        `).join('');
        
        document.getElementById('tableTotalSales').textContent = formatCurrency(this.totalSales);
    }
}

new SalesReport();