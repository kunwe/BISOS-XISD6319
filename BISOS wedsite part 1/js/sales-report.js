// sales-report.js - Sales Report Logic

import { renderNav, renderFooter, showToast, formatCurrency, requireAuth } from './app.js';

const MOCK_SALES = [
    { id: 'A-4521', date: '2026-03-01', product: 'Milk 2L', qty: 12, price: 24.99, total: 299.88, payment: 'Cash', store: 'Soweto' },
    { id: 'A-4522', date: '2026-03-01', product: 'Bread 700g', qty: 8, price: 18.50, total: 148.00, payment: 'Card', store: 'Soweto' },
    { id: 'A-4523', date: '2026-03-02', product: 'Sugar 1kg', qty: 5, price: 32.00, total: 160.00, payment: 'Cash', store: 'Soweto' },
    { id: 'A-4524', date: '2026-03-02', product: 'Cooking Oil 750ml', qty: 3, price: 58.99, total: 176.97, payment: 'Card', store: 'Soweto' },
    { id: 'A-4525', date: '2026-03-03', product: 'Chicken 1kg', qty: 4, price: 72.99, total: 291.96, payment: 'Mobile', store: 'Soweto' },
    { id: 'A-4526', date: '2026-03-03', product: 'Rice 5kg', qty: 2, price: 85.00, total: 170.00, payment: 'Cash', store: 'Soweto' },
    { id: 'A-4527', date: '2026-03-04', product: 'Eggs 30-pack', qty: 6, price: 56.50, total: 339.00, payment: 'Card', store: 'Soweto' },
    { id: 'A-4528', date: '2026-03-05', product: 'Maize Meal 5kg', qty: 3, price: 48.99, total: 146.97, payment: 'Cash', store: 'Soweto' },
    { id: 'A-4529', date: '2026-03-05', product: 'Pasta 500g', qty: 10, price: 16.99, total: 169.90, payment: 'Card', store: 'Soweto' },
    { id: 'A-4530', date: '2026-03-06', product: 'Canned Beans 410g', qty: 15, price: 12.99, total: 194.85, payment: 'Cash', store: 'Soweto' },
];

class SalesReport {
    constructor() {
        requireAuth();
        renderNav('sales-report');
        renderFooter();
        this.sales = [...MOCK_SALES];
        this.filtered = [...this.sales];
        this.activeTab = 'monthly';
        this.render();
        this.setupEventListeners();
    }

    get totalSales() { return this.filtered.reduce((sum, s) => sum + s.total, 0); }
    get totalTransactions() { return this.filtered.length; }
    get avgTransaction() { return this.totalTransactions ? this.totalSales / this.totalTransactions : 0; }

    render() {
        this.renderSummaryCards();
        this.renderTable();
    }

    renderSummaryCards() {
        document.getElementById('totalSales').textContent = formatCurrency(this.totalSales);
        document.getElementById('totalTransactions').textContent = this.totalTransactions;
        document.getElementById('avgTransaction').textContent = formatCurrency(this.avgTransaction);

        const cash = this.filtered.filter(s => s.payment === 'Cash').reduce((sum, s) => sum + s.total, 0);
        const card = this.filtered.filter(s => s.payment === 'Card').reduce((sum, s) => sum + s.total, 0);
        const mobile = this.filtered.filter(s => s.payment === 'Mobile').reduce((sum, s) => sum + s.total, 0);
        document.getElementById('cashSales').textContent = formatCurrency(cash);
        document.getElementById('cardSales').textContent = formatCurrency(card);
        document.getElementById('mobileSales').textContent = formatCurrency(mobile);
    }

    renderTable() {
        const tbody = document.getElementById('salesTableBody');
        tbody.innerHTML = this.filtered.map(s => `
            <tr>
                <td><code>${s.id}</code></td>
                <td>${new Date(s.date).toLocaleDateString('en-ZA')}</td>
                <td>${s.product}</td>
                <td>${s.qty}</td>
                <td>${formatCurrency(s.price)}</td>
                <td><strong>${formatCurrency(s.total)}</strong></td>
                <td>
                    <span class="status-badge ${s.payment === 'Cash' ? 'status-ok' : s.payment === 'Card' ? 'status-acknowledged' : 'status-warning'}">
                        ${s.payment}
                    </span>
                </td>
                <td>${s.store}</td>
            </tr>
        `).join('');

        document.getElementById('tableTotalSales').textContent = formatCurrency(this.totalSales);
    }

    setupEventListeners() {
        // Tab buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                this.activeTab = btn.dataset.tab;
                this.applyFilters();
            });
        });

        document.getElementById('storeFilter').addEventListener('change', () => this.applyFilters());
        document.getElementById('paymentFilter').addEventListener('change', () => this.applyFilters());
        document.getElementById('dateFrom').addEventListener('change', () => this.applyFilters());
        document.getElementById('dateTo').addEventListener('change', () => this.applyFilters());

        document.getElementById('exportPDF').addEventListener('click', () => {
            showToast('PDF export would be generated server-side', 'info');
        });

        document.getElementById('exportCSV').addEventListener('click', () => this.exportCSV());
    }

    applyFilters() {
        const store = document.getElementById('storeFilter').value;
        const payment = document.getElementById('paymentFilter').value;
        const from = document.getElementById('dateFrom').value;
        const to = document.getElementById('dateTo').value;

        this.filtered = this.sales.filter(s => {
            if (store && s.store !== store) return false;
            if (payment && s.payment !== payment) return false;
            if (from && s.date < from) return false;
            if (to && s.date > to) return false;
            return true;
        });

        this.render();
    }

    exportCSV() {
        const headers = ['Transaction ID', 'Date', 'Product', 'Qty', 'Unit Price', 'Total', 'Payment', 'Store'];
        const rows = this.filtered.map(s => [s.id, s.date, s.product, s.qty, s.price, s.total, s.payment, s.store]);
        const csv = [headers, ...rows].map(r => r.join(',')).join('\n');
        const blob = new Blob([csv], { type: 'text/csv' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'bisos-sales-report.csv';
        a.click();
        showToast('Sales report exported', 'success');
    }
}

document.addEventListener('DOMContentLoaded', () => new SalesReport());
