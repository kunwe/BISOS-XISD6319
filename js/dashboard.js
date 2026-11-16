import { showToast, formatCurrency, getSelectedStore, setSelectedStore } from './app.js?v=2';

class Dashboard {
    constructor() {
        this.storeId = null;
        this.init();
    }

    async init() {
        this.bind();
        try {
            await this.loadStores();
            this.storeId = getSelectedStore();
            const sel = document.getElementById('storeSelect');
            if (sel && this.storeId) {
                sel.value = String(this.storeId);
                const nameEl = document.getElementById('dashStoreName');
                if (nameEl && sel.options[sel.selectedIndex]) {
                    nameEl.textContent = sel.options[sel.selectedIndex].text;
                }
            }
            await this.loadStats();
        } catch (err) {
            console.error(err);
            showToast('Failed to load dashboard', 'error');
        }
    }

    bind() {
        const storeSelect = document.getElementById('storeSelect');
        if (storeSelect) {
            storeSelect.addEventListener('change', (e) => {
                this.storeId = e.target.value ? parseInt(e.target.value) : null;
                setSelectedStore(this.storeId);
                const name = e.target.options[e.target.selectedIndex]?.text || 'All Stores';
                const nameEl = document.getElementById('dashStoreName');
                if (nameEl) nameEl.textContent = name;
                this.loadStats();
            });
        }
        document.getElementById('refreshBtn')?.addEventListener('click', () => {
            this.loadStats();
            showToast('Dashboard refreshed', 'success');
        });
    }

    async loadStores() {
        try {
            const res = await fetch('api/sales.php?action=stores');
            const json = await res.json();
            const sel = document.getElementById('storeSelect');
            if (sel && json.data) {
                sel.innerHTML = '<option value="">All Stores</option>' +
                    json.data.map(s => `<option value="${s.store_id}">${s.name}</option>`).join('');
            }
        } catch (err) {
            console.error(err);
        }
    }

    async loadStats() {
        try {
            let url = 'api.php?action=get_dashboard_stats';
            if (this.storeId) url += '&store_id=' + this.storeId;
            const res = await fetch(url);
            const stats = await res.json();

            const set = (id, val) => {
                const el = document.getElementById(id);
                if (el) el.textContent = val;
            };

            set('statStores', stats.totalStores ?? 0);
            set('statProducts', (stats.totalProducts ?? 0).toLocaleString());
            set('statAlerts', stats.lowStockAlerts ?? 0);
            set('statSales', formatCurrency(stats.todaySales ?? 0));

            const lowCount = stats.lowStockAlerts ?? 0;
            const outCount = stats.outOfStock ?? 0;
            const subParts = [];
            if (outCount > 0) subParts.push(`${outCount} out of stock`);
            if (lowCount - outCount > 0) subParts.push(`${lowCount - outCount} running low`);
            set('statAlertsSub', lowCount > 0 ? (subParts.join(', ') || `${lowCount} pending`) : 'All clear');

            // Render the low-stock items table.
            this.renderLowStockList(stats.lowStockItems || []);

            // Load the charts and activity feed.
            await Promise.all([
                this.loadRevenueTrend(),
                this.loadStockByCategory(),
                this.loadRecentActivity()
            ]);

            // Fetch monthly revenue total for the selected store.
            await this.loadMonthlyRevenue();
        } catch (err) {
            console.error(err);
        }
    }

    renderLowStockList(items) {
        const tbody = document.getElementById('lowStockTableBody');
        if (!tbody) return;

        if (!items || items.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:var(--success);padding:1.5rem"><i class="fas fa-check-circle"></i> All stock levels are healthy</td></tr>';
            return;
        }

        tbody.innerHTML = items.map(item => {
            const qty = parseInt(item.quantity);
            const reorder = parseInt(item.reorder_level);
            const isOut = qty <= 0;
            const statusLabel = isOut ? 'OUT' : 'LOW';
            const statusClass = 'status-low';
            return `
                <tr>
                    <td><strong>${item.name}</strong></td>
                    <td>${item.category || '-'}</td>
                    <td>${item.store_name}</td>
                    <td><strong style="color:var(--danger)">${qty}</strong></td>
                    <td>${reorder}</td>
                    <td><span class="status-badge ${statusClass}">${statusLabel}</span></td>
                </tr>`;
        }).join('');
    }

    async loadRevenueTrend() {
        try {
            let url = 'api.php?action=get_revenue_trend';
            if (this.storeId) url += '&store_id=' + this.storeId;
            const res = await fetch(url);
            const json = await res.json();
            this.renderBarChart(json.months || [], json.values || []);
        } catch (err) {
            console.error(err);
        }
    }

    renderBarChart(months, values) {
        const container = document.getElementById('barChart');
        if (!container) return;
        const max = Math.max(...values, 1);
        if (values.every(v => v === 0)) {
            container.innerHTML = '<div class="chart-placeholder" style="height:180px">No sales data yet</div>';
            return;
        }
        container.innerHTML = `
            <div class="bar-chart">
                ${values.map((val, i) => `
                    <div class="bar-group">
                        <div class="bar" style="height:${Math.max(4, (val / max) * 160)}px" title="${months[i]}: ${formatCurrency(val)}"></div>
                        <span class="bar-label">${months[i]}</span>
                    </div>
                `).join('')}
            </div>
        `;
    }

    async loadStockByCategory() {
        try {
            let url = 'api.php?action=get_stock_by_category';
            if (this.storeId) url += '&store_id=' + this.storeId;
            const res = await fetch(url);
            const json = await res.json();
            this.renderDonutChart(json.data || []);
        } catch (err) {
            console.error(err);
        }
    }

    renderDonutChart(categories) {
        const container = document.getElementById('donutChart');
        if (!container) return;

        if (!categories || categories.length === 0) {
            container.innerHTML = '<div class="chart-placeholder" style="height:180px">No stock data</div>';
            return;
        }

        const total = categories.reduce((s, c) => s + c.total_qty, 0) || 1;
        // Build conic-gradient segments.
        let cursor = 0;
        const segments = categories.map(c => {
            const pct = (c.total_qty / total) * 100;
            const start = cursor;
            cursor += pct;
            return `${c.color} ${start}% ${cursor}%`;
        }).join(', ');

        container.innerHTML = `
            <div class="donut-chart">
                <div class="donut-visual" style="background:conic-gradient(${segments})"></div>
                <div class="donut-legend">
                    ${categories.map(c => `
                        <div class="legend-item">
                            <span class="legend-dot" style="background:${c.color}"></span>
                            <span class="legend-label">${c.category}</span>
                            <span class="legend-value">${c.total_qty.toLocaleString()}</span>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }

    async loadRecentActivity() {
        try {
            let url = 'api.php?action=get_recent_activity';
            if (this.storeId) url += '&store_id=' + this.storeId;
            const res = await fetch(url);
            const json = await res.json();
            this.renderActivity(json.sales || [], json.low_stock || []);
        } catch (err) {
            console.error(err);
        }
    }

    renderActivity(sales, lowStock) {
        const list = document.getElementById('activityList');
        if (!list) return;

        const items = [];

        sales.forEach(s => {
            const time = new Date(s.transaction_date).toLocaleTimeString('en-ZA', { hour: '2-digit', minute: '2-digit' });
            items.push({
                type: 'sale',
                icon: 'fa-shopping-cart',
                text: `<strong>Sale:</strong> ${s.transaction_ref} — ${formatCurrency(s.total_amount)} <span style="color:var(--text-secondary)">(${s.store_name})</span>`,
                time
            });
        });

        lowStock.forEach(l => {
            items.push({
                type: 'alert',
                icon: 'fa-exclamation-circle',
                text: `<strong>Low stock:</strong> ${l.product_name} — ${l.quantity} left <span style="color:var(--text-secondary)">(${l.store_name})</span>`,
                time: 'now'
            });
        });

        if (items.length === 0) {
            list.innerHTML = '<li style="color:var(--text-secondary);padding:1rem 0">No recent activity</li>';
            return;
        }

        list.innerHTML = items.slice(0, 10).map(a => `
            <li class="activity-item">
                <div class="activity-icon ${a.type}"><i class="fas ${a.icon}"></i></div>
                <div class="activity-content"><p>${a.text}</p><span class="activity-time">${a.time}</span></div>
            </li>
        `).join('');
    }

    async loadMonthlyRevenue() {
        try {
            const now = new Date();
            const from = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-01`;
            const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0).getDate();
            const to = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${lastDay}`;

            const storeIds = this.storeId ? [this.storeId] : null;

            if (!storeIds) {
                // All stores: sum each store's monthly total.
                const storesRes = await fetch('api/sales.php?action=stores');
                const storesJson = await storesRes.json();
                const ids = (storesJson.data || []).map(s => s.store_id);
                const totals = await Promise.all(ids.map(async (sid) => {
                    const res = await fetch(`api/sales.php?action=report&store_id=${sid}&from=${from}&to=${to}`);
                    const json = await res.json();
                    return (json.data || []).reduce((s, r) => s + parseFloat(r.total_price), 0);
                }));
                const total = totals.reduce((s, v) => s + v, 0);
                const el = document.getElementById('monthlyRevenue');
                if (el) el.textContent = formatCurrency(total);
            } else {
                const res = await fetch(`api/sales.php?action=report&store_id=${this.storeId}&from=${from}&to=${to}`);
                const json = await res.json();
                const total = (json.data || []).reduce((s, r) => s + parseFloat(r.total_price), 0);
                const el = document.getElementById('monthlyRevenue');
                if (el) el.textContent = formatCurrency(total);
            }
        } catch (err) {
            console.error(err);
        }
    }
}

window.dashboard = new Dashboard();