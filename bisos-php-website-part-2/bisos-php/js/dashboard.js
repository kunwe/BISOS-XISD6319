import { showToast, formatCurrency } from './app.js';

const MOCK_DATA = {
    monthlyRevenue: [85000, 92000, 78000, 105000, 98000, 112000, 124800],
    months: ['Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'],
    categories: [
        { label: 'Dairy', value: 35, color: '#3498DB' },
        { label: 'Bakery', value: 25, color: '#27AE60' },
        { label: 'Pantry', value: 18, color: '#F39C12' },
        { label: 'Meat', value: 12, color: '#E74C3C' },
        { label: 'Other', value: 10, color: '#95A5A6' },
    ],
    activity: [
        { type: 'stock', icon: 'fa-plus-circle', text: '<strong>Stock Added:</strong> 25 units of Milk 2L', time: '10:30' },
        { type: 'alert', icon: 'fa-exclamation-circle', text: '<strong>Alert Triggered:</strong> Sugar 1kg below reorder level', time: '09:45' },
        { type: 'sale', icon: 'fa-shopping-cart', text: '<strong>Sale Completed:</strong> Transaction #A-4521', time: '09:15' }
    ]
};

class Dashboard {
    constructor() {
        this.render();
        document.getElementById('refreshBtn')?.addEventListener('click', () => {
            this.render();
            showToast('Dashboard refreshed', 'success');
        });
    }

    render() {
        this.renderBarChart();
        this.renderDonutChart();
        this.renderActivity();
    }

    renderBarChart() {
        const container = document.getElementById('barChart');
        if (!container) return;
        const max = Math.max(...MOCK_DATA.monthlyRevenue);
        container.innerHTML = `
            <div class="bar-chart">
                ${MOCK_DATA.monthlyRevenue.map((val, i) => `
                    <div class="bar-group">
                        <div class="bar" style="height:${(val / max) * 160}px"></div>
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
                <div class="donut-visual"></div>
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
        const list = document.getElementById('activityList');
        if (!list) return;
        list.innerHTML = MOCK_DATA.activity.map(a => `
            <li class="activity-item">
                <div class="activity-icon ${a.type}"><i class="fas ${a.icon}"></i></div>
                <div class="activity-content"><p>${a.text}</p><span class="activity-time">${a.time}</span></div>
            </li>
        `).join('');
    }
}

new Dashboard();