// dashboard.js - Dashboard Page Logic

import { renderNav, renderFooter, showToast, formatCurrency, requireAuth } from './app.js';

const MOCK_DATA = {
    summary: { totalStores: 3, totalProducts: 1247, lowStockAlerts: 8, criticalAlerts: 3, todaySales: 4560, monthlyRevenue: 124800 },
    activity: [
        { type: 'stock', icon: 'fa-plus-circle', text: '<strong>Stock Added:</strong> 25 units of Milk 2L', time: '2026-03-26T10:30:00' },
        { type: 'alert', icon: 'fa-exclamation-circle', text: '<strong>Alert Triggered:</strong> Sugar 1kg below reorder level', time: '2026-03-26T09:45:00' },
        { type: 'sale', icon: 'fa-shopping-cart', text: '<strong>Sale Completed:</strong> Transaction #A-4521', time: '2026-03-26T09:15:00' },
        { type: 'reorder', icon: 'fa-truck', text: '<strong>Reorder Placed:</strong> 50 units of Cooking Oil', time: '2026-03-26T08:30:00' },
        { type: 'stock', icon: 'fa-plus-circle', text: '<strong>Stock Added:</strong> 30 units of Bread', time: '2026-03-26T08:00:00' },
    ],
    monthlyRevenue: [85000, 92000, 78000, 105000, 98000, 112000, 124800],
    months: ['Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'],
    categories: [
        { label: 'Dairy', value: 35, color: '#3498DB' },
        { label: 'Bakery', value: 25, color: '#27AE60' },
        { label: 'Pantry', value: 18, color: '#F39C12' },
        { label: 'Meat', value: 12, color: '#E74C3C' },
        { label: 'Other', value: 10, color: '#95A5A6' },
    ]
};

class Dashboard {
    constructor() {
        requireAuth();
        renderNav('dashboard');
        renderFooter();
        this.render();
        this.setupEventListeners();
        this.startAutoRefresh();
    }

    render() {
        this.renderSummary();
        this.renderBarChart();
        this.renderDonutChart();
        this.renderActivity();
    }

    renderSummary() {
        const d = MOCK_DATA.summary;
        document.querySelector('.card-store .card-value').textContent = d.totalStores;
        document.querySelector('.card-products .card-value').textContent = d.totalProducts.toLocaleString();
        document.querySelector('.card-alerts .card-value').textContent = d.lowStockAlerts;
        document.querySelector('.card-alerts .card-change').textContent = `${d.criticalAlerts} critical`;
        document.querySelector('.card-sales .card-value').textContent = formatCurrency(d.todaySales);
        document.querySelectorAll('.badge').forEach(b => b.textContent = d.lowStockAlerts);
    }

    renderBarChart() {
        const container = document.getElementById('barChart');
        if (!container) return;
        const max = Math.max(...MOCK_DATA.monthlyRevenue);
        container.innerHTML = `
            <div class="bar-chart" role="img" aria-label="Monthly revenue bar chart">
                ${MOCK_DATA.monthlyRevenue.map((val, i) => `
                    <div class="bar-group">
                        <div class="bar" style="height:${(val / max) * 160}px" title="${MOCK_DATA.months[i]}: ${formatCurrency(val)}"></div>
                        <span class="bar-label">${MOCK_DATA.months[i]}</span>
                    </div>
                `).join('')}
            </div>
        `;
    }

    renderDonutChart() {
        const container = document.getElementById('donutChart');
        if (!container) return;
        container.innerHTML = `
            <div class="donut-chart">
                <div class="donut-visual" role="img" aria-label="Stock by category donut chart"></div>
                <div class="donut-legend">
                    ${MOCK_DATA.categories.map(c => `
                        <div class="legend-item">
                            <span class="legend-dot" style="background:${c.color}"></span>
                            <span class="legend-label">${c.label}</span>
                            <span class="legend-value">${c.value}%</span>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }

    renderActivity() {
        const list = document.querySelector('.activity-list');
        if (!list) return;
        list.innerHTML = MOCK_DATA.activity.map(a => `
            <li class="activity-item" role="listitem">
                <div class="activity-icon ${a.type}" aria-hidden="true">
                    <i class="fas ${a.icon}"></i>
                </div>
                <div class="activity-content">
                    <p>${a.text}</p>
                    <span class="activity-time">${new Date(a.time).toLocaleTimeString('en-ZA', { hour: '2-digit', minute: '2-digit' })}</span>
                </div>
            </li>
        `).join('');
    }

    setupEventListeners() {
        document.getElementById('refreshBtn')?.addEventListener('click', () => {
            this.render();
            showToast('Dashboard refreshed', 'success');
        });
    }

    startAutoRefresh() {
        setInterval(() => this.render(), 30000);
    }
}

document.addEventListener('DOMContentLoaded', () => new Dashboard());
