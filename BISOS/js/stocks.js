// stocks.js - Stock Management Logic

import { renderNav, renderFooter, showToast, requireAuth } from './app.js';

const MOCK_STOCKS = [
    { id: 1, name: 'Milk 2L', category: 'Dairy', barcode: '6001234567890', stock: 15, reorderLevel: 20, maxLevel: 50, location: 'A-01' },
    { id: 2, name: 'Bread 700g', category: 'Bakery', barcode: '6001234567891', stock: 45, reorderLevel: 10, maxLevel: 60, location: 'A-02' },
    { id: 3, name: 'Sugar 1kg', category: 'Pantry', barcode: '6001234567892', stock: 8, reorderLevel: 15, maxLevel: 40, location: 'B-01' },
    { id: 4, name: 'Cooking Oil 750ml', category: 'Pantry', barcode: '6001234567893', stock: 32, reorderLevel: 20, maxLevel: 45, location: 'B-02' },
    { id: 5, name: 'Chicken 1kg', category: 'Meat', barcode: '6001234567894', stock: 5, reorderLevel: 10, maxLevel: 30, location: 'C-01' },
    { id: 6, name: 'Rice 5kg', category: 'Grains', barcode: '6001234567895', stock: 20, reorderLevel: 5, maxLevel: 50, location: 'C-02' },
    { id: 7, name: 'Pasta 500g', category: 'Pantry', barcode: '6001234567896', stock: 55, reorderLevel: 20, maxLevel: 80, location: 'D-01' },
    { id: 8, name: 'Canned Beans 410g', category: 'Canned Goods', barcode: '6001234567897', stock: 80, reorderLevel: 30, maxLevel: 120, location: 'D-02' },
    { id: 9, name: 'Maize Meal 5kg', category: 'Grains', barcode: '6001234567898', stock: 18, reorderLevel: 10, maxLevel: 40, location: 'E-01' },
    { id: 10, name: 'Eggs 30-pack', category: 'Dairy', barcode: '6001234567899', stock: 12, reorderLevel: 15, maxLevel: 35, location: 'E-02' },
];

class StocksPage {
    constructor() {
        requireAuth();
        renderNav('stocks');
        renderFooter();
        this.stocks = [...MOCK_STOCKS];
        this.filtered = [...this.stocks];
        this.currentEdit = null;
        this.render();
        this.setupEventListeners();
    }

    getStatus(stock) {
        const pct = stock.stock / stock.reorderLevel;
        if (pct <= 0.5) return { label: '🔴 CRITICAL', cls: 'status-low' };
        if (pct < 1) return { label: '🔴 LOW', cls: 'status-low' };
        return { label: '🟢 OK', cls: 'status-ok' };
    }

    getBarPct(stock) {
        return Math.min(100, Math.round((stock.stock / stock.maxLevel) * 100));
    }

    getBarClass(stock) {
        const pct = stock.stock / stock.reorderLevel;
        if (pct < 1) return 'low';
        if (pct < 1.5) return 'warning';
        return 'ok';
    }

    render() {
        const low = this.stocks.filter(s => s.stock < s.reorderLevel).length;
        const critical = this.stocks.filter(s => s.stock / s.reorderLevel <= 0.5).length;
        const ok = this.stocks.length - low;

        document.getElementById('alertSummary').innerHTML = `
            <div class="alert-chip critical"><i class="fas fa-exclamation-circle"></i> ${critical} Critical</div>
            <div class="alert-chip warning"><i class="fas fa-exclamation-triangle"></i> ${low - critical} Low</div>
            <div class="alert-chip ok"><i class="fas fa-check-circle"></i> ${ok} OK</div>
        `;

        const tbody = document.getElementById('stockTableBody');
        tbody.innerHTML = this.filtered.map(s => {
            const status = this.getStatus(s);
            const pct = this.getBarPct(s);
            const barCls = this.getBarClass(s);
            return `
                <tr>
                    <td><strong>${s.name}</strong></td>
                    <td>${s.category}</td>
                    <td>
                        <div class="stock-level-bar">
                            <div class="stock-bar-track">
                                <div class="stock-bar-fill ${barCls}" style="width:${pct}%"></div>
                            </div>
                            <span class="stock-qty">${s.stock}</span>
                        </div>
                    </td>
                    <td>${s.reorderLevel}</td>
                    <td>${s.location}</td>
                    <td><span class="status-badge ${status.cls}">${status.label}</span></td>
                    <td>
                        <div style="display:flex;gap:0.4rem">
                            <button class="btn btn-sm btn-outline" onclick="stocksPage.openEdit(${s.id})" aria-label="Edit ${s.name}">
                                <i class="fas fa-edit"></i>
                            </button>
                            ${s.stock < s.reorderLevel ? `
                                <button class="btn btn-sm btn-warning" onclick="stocksPage.reorder(${s.id})" aria-label="Reorder ${s.name}">
                                    <i class="fas fa-truck"></i> Reorder
                                </button>
                            ` : ''}
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    setupEventListeners() {
        document.getElementById('searchInput').addEventListener('input', (e) => {
            const q = e.target.value.toLowerCase();
            this.filtered = this.stocks.filter(s =>
                s.name.toLowerCase().includes(q) || s.category.toLowerCase().includes(q)
            );
            this.render();
        });

        document.getElementById('categoryFilter').addEventListener('change', (e) => {
            const cat = e.target.value;
            this.filtered = cat ? this.stocks.filter(s => s.category === cat) : [...this.stocks];
            this.render();
        });

        document.getElementById('statusFilter').addEventListener('change', (e) => {
            const val = e.target.value;
            if (!val) { this.filtered = [...this.stocks]; }
            else if (val === 'low') { this.filtered = this.stocks.filter(s => s.stock < s.reorderLevel); }
            else { this.filtered = this.stocks.filter(s => s.stock >= s.reorderLevel); }
            this.render();
        });

        document.getElementById('addStockBtn').addEventListener('click', () => this.openAdd());
        document.getElementById('exportBtn').addEventListener('click', () => this.exportCSV());
        document.getElementById('modalClose').addEventListener('click', () => this.closeModal());
        document.getElementById('modalCancel').addEventListener('click', () => this.closeModal());
        document.getElementById('stockForm').addEventListener('submit', (e) => { e.preventDefault(); this.saveStock(); });
        document.getElementById('modalOverlay').addEventListener('click', (e) => {
            if (e.target === document.getElementById('modalOverlay')) this.closeModal();
        });
    }

    openAdd() {
        this.currentEdit = null;
        document.getElementById('modalTitle').textContent = 'Add New Stock Item';
        document.getElementById('stockForm').reset();
        document.getElementById('modalOverlay').classList.add('active');
    }

    openEdit(id) {
        const stock = this.stocks.find(s => s.id === id);
        if (!stock) return;
        this.currentEdit = id;
        document.getElementById('modalTitle').textContent = 'Update Stock';
        document.getElementById('fieldName').value = stock.name;
        document.getElementById('fieldCategory').value = stock.category;
        document.getElementById('fieldQuantity').value = stock.stock;
        document.getElementById('fieldReorderLevel').value = stock.reorderLevel;
        document.getElementById('fieldLocation').value = stock.location;
        document.getElementById('modalOverlay').classList.add('active');
    }

    closeModal() {
        document.getElementById('modalOverlay').classList.remove('active');
    }

    saveStock() {
        const name = document.getElementById('fieldName').value.trim();
        const category = document.getElementById('fieldCategory').value;
        const quantity = parseInt(document.getElementById('fieldQuantity').value);
        const reorderLevel = parseInt(document.getElementById('fieldReorderLevel').value);
        const location = document.getElementById('fieldLocation').value.trim();

        if (this.currentEdit) {
            const idx = this.stocks.findIndex(s => s.id === this.currentEdit);
            this.stocks[idx] = { ...this.stocks[idx], name, category, stock: quantity, reorderLevel, location };
            showToast('Stock updated successfully', 'success');
        } else {
            this.stocks.push({ id: Date.now(), name, category, barcode: '', stock: quantity, reorderLevel, maxLevel: reorderLevel * 3, location });
            showToast('Stock item added successfully', 'success');
        }

        this.filtered = [...this.stocks];
        this.render();
        this.closeModal();
    }

    reorder(id) {
        const stock = this.stocks.find(s => s.id === id);
        showToast(`Reorder placed for ${stock.name}`, 'success');
    }

    exportCSV() {
        const headers = ['Name', 'Category', 'Stock', 'Reorder Level', 'Location', 'Status'];
        const rows = this.stocks.map(s => {
            const status = this.getStatus(s);
            return [s.name, s.category, s.stock, s.reorderLevel, s.location, status.label.replace(/[🔴🟢]/g, '').trim()];
        });
        const csv = [headers, ...rows].map(r => r.join(',')).join('\n');
        const blob = new Blob([csv], { type: 'text/csv' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'bisos-stocks.csv';
        a.click();
        showToast('Stock report exported', 'success');
    }
}

const stocksPage = new StocksPage();
window.stocksPage = stocksPage;
