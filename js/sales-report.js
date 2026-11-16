import { showToast, formatCurrency, getSelectedStore, setSelectedStore } from './app.js?v=2';

const API = 'api/sales.php?action=';

class SalesReport {
    constructor() {
        this.sales = [];
        this.filtered = [];
        this.storeId = null;
        this.init();
    }

    async init() {
        await this.loadStores();
        this.storeId = getSelectedStore();
        const sel = document.getElementById('storeFilter');
        if (sel && this.storeId) sel.value = String(this.storeId);
        await this.loadSales();
        this.bind();
    }

    bind() {
        document.getElementById('storeFilter').addEventListener('change', (e) => {
            this.storeId = e.target.value ? parseInt(e.target.value) : null;
            setSelectedStore(this.storeId);
            this.loadSales();
        });
        document.getElementById('dateFrom').addEventListener('change', () => this.loadSales());
        document.getElementById('dateTo').addEventListener('change', () => this.loadSales());
        document.getElementById('paymentFilter').addEventListener('change', () => this.applyFilters());
    }

    async loadStores() {
        try {
            const res = await fetch(API + 'stores');
            const json = await res.json();
            const sel = document.getElementById('storeFilter');
            const current = sel.value;
            sel.innerHTML = '<option value="">All Stores</option>' +
                json.data.map(s => `<option value="${s.store_id}">${s.name}</option>`).join('');
            if ([...sel.options].some(o => o.value === current)) sel.value = current;
            // Default to the user's store if none chosen.
            if (!sel.value && json.data.length) {
                // keep All Stores as default selection
            }
        } catch (err) {
            console.error(err);
            showToast('Failed to load stores', 'error');
        }
    }

    async loadSales() {
        const from = document.getElementById('dateFrom').value || '';
        const to = document.getElementById('dateTo').value || '';

        // Build query. When All Stores, fetch each store and merge.
        const storeSelect = document.getElementById('storeFilter');
        const storeIds = storeSelect.value
            ? [parseInt(storeSelect.value)]
            : [...storeSelect.options].filter(o => o.value).map(o => parseInt(o.value));

        if (storeIds.length === 0) {
            // No stores available.
            this.sales = [];
            this.applyFilters();
            return;
        }

        try {
            const results = await Promise.all(storeIds.map(async (sid) => {
                let url = API + 'report&store_id=' + sid;
                if (from) url += '&from=' + encodeURIComponent(from);
                if (to) url += '&to=' + encodeURIComponent(to);
                const res = await fetch(url);
                const json = await res.json();
                return (json.data || []);
            }));
            this.sales = [].concat(...results).sort((a, b) =>
                new Date(b.transaction_date) - new Date(a.transaction_date)
            );
            this.applyFilters();
        } catch (err) {
            console.error(err);
            showToast('Failed to load sales data', 'error');
        }
    }

    applyFilters() {
        const pay = document.getElementById('paymentFilter').value;
        this.filtered = this.sales.filter(s => !pay || s.payment_method === pay.toUpperCase());
        this.render();
    }

    get totalSales() {
        return this.filtered.reduce((sum, s) => sum + parseFloat(s.total_price), 0);
    }

    render() {
        const totalSales = this.totalSales;
        const txIds = new Set(this.filtered.map(s => s.transaction_id));
        const txnCount = txIds.size;
        const avg = txnCount ? totalSales / txnCount : 0;

        document.getElementById('totalSales').textContent = formatCurrency(totalSales);
        document.getElementById('totalTransactions').textContent = txnCount;
        document.getElementById('avgTransaction').textContent = formatCurrency(avg);

        const byPayment = (method) => this.filtered
            .filter(s => s.payment_method === method)
            .reduce((sum, s) => sum + parseFloat(s.total_price), 0);

        document.getElementById('cashSales').textContent = formatCurrency(byPayment('CASH'));
        document.getElementById('cardSales').textContent = formatCurrency(byPayment('CARD'));
        document.getElementById('mobileSales').textContent = formatCurrency(byPayment('MOBILE'));

        // Update header labels.
        const from = document.getElementById('dateFrom').value;
        const to = document.getElementById('dateTo').value;
        const periodEl = document.getElementById('reportPeriod');
        if (periodEl) periodEl.textContent = (from && to) ? `${from} to ${to}` : 'Current period';
        const storeEl = document.getElementById('reportStore');
        if (storeEl) {
            const sel = document.getElementById('storeFilter');
            storeEl.textContent = sel.value ? sel.options[sel.selectedIndex].text : 'All Stores';
        }

        const tbody = document.getElementById('salesTableBody');
        if (this.filtered.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:var(--text-secondary);padding:2rem">No sales in this period.</td></tr>';
        } else {
            tbody.innerHTML = this.filtered.map(s => `
                <tr>
                    <td><code>${s.transaction_ref}</code></td>
                    <td>${s.transaction_date}</td>
                    <td>${s.product_name}</td>
                    <td>${s.quantity}</td>
                    <td>${formatCurrency(s.unit_price)}</td>
                    <td><strong>${formatCurrency(s.total_price)}</strong></td>
                    <td><span class="status-badge status-ok">${s.payment_method}</span></td>
                    <td>${s.store_name}</td>
                </tr>
            `).join('');
        }

        document.getElementById('tableTotalSales').textContent = formatCurrency(totalSales);
    }
}

window.salesReport = new SalesReport();