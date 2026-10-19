import { showToast } from './app.js';

class StocksPage {
    constructor() {
        this.stocks = [];
        this.filtered = [];
        this.currentEdit = null;
        this.loadData(); // Fetch from PHP API
        this.setupEventListeners();
    }

    async loadData() {
        try {
            const response = await fetch('api.php?action=get_stocks');
            this.stocks = await response.json();
            this.filtered = [...this.stocks];
            this.render();
        } catch (error) {
            console.error("Failed to load stocks", error);
        }
    }

    getStatus(stock) {
        const pct = stock.stock / stock.reorderLevel;
        if (pct <= 0.5) return { label: '🔴 CRITICAL', cls: 'status-low' };
        if (pct < 1) return { label: '🔴 LOW', cls: 'status-low' };
        return { label: '🟢 OK', cls: 'status-ok' };
    }

    render() {
        const low = this.stocks.filter(s => s.stock < s.reorderLevel).length;
        const critical = this.stocks.filter(s => s.stock / s.reorderLevel <= 0.5).length;
        
        document.getElementById('alertSummary').innerHTML = `
            <div class="alert-chip critical"><i class="fas fa-exclamation-circle"></i> ${critical} Critical</div>
            <div class="alert-chip warning"><i class="fas fa-exclamation-triangle"></i> ${low - critical} Low</div>
            <div class="alert-chip ok"><i class="fas fa-check-circle"></i> ${this.stocks.length - low} OK</div>
        `;

        document.getElementById('stockTableBody').innerHTML = this.filtered.map(s => {
            const status = this.getStatus(s);
            const pct = Math.min(100, Math.round((s.stock / s.maxLevel) * 100));
            return `
                <tr>
                    <td><strong>${s.name}</strong></td>
                    <td>${s.category}</td>
                    <td>
                        <div class="stock-level-bar">
                            <div class="stock-bar-track"><div class="stock-bar-fill ${pct < 50 ? 'low' : 'ok'}" style="width:${pct}%"></div></div>
                            <span class="stock-qty">${s.stock}</span>
                        </div>
                    </td>
                    <td>${s.reorderLevel}</td>
                    <td>${s.location}</td>
                    <td><span class="status-badge ${status.cls}">${status.label}</span></td>
                    <td>
                        <div style="display:flex;gap:0.4rem">
                            <button class="btn btn-sm btn-outline" onclick="window.stocksPage.openEdit(${s.id})"><i class="fas fa-edit"></i></button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    setupEventListeners() {
        document.getElementById('searchInput').addEventListener('input', (e) => {
            const q = e.target.value.toLowerCase();
            this.filtered = this.stocks.filter(s => s.name.toLowerCase().includes(q));
            this.render();
        });
        document.getElementById('addStockBtn').addEventListener('click', () => this.openAdd());
        document.getElementById('modalClose').addEventListener('click', () => this.closeModal());
        document.getElementById('modalCancel').addEventListener('click', () => this.closeModal());
        document.getElementById('stockForm').addEventListener('submit', (e) => { e.preventDefault(); this.saveStock(); });
    }

    openAdd() {
        this.currentEdit = null;
        document.getElementById('modalTitle').textContent = 'Add New Stock';
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

    closeModal() { document.getElementById('modalOverlay').classList.remove('active'); }

    saveStock() {
        const name = document.getElementById('fieldName').value;
        const category = document.getElementById('fieldCategory').value;
        const quantity = parseInt(document.getElementById('fieldQuantity').value);
        const reorderLevel = parseInt(document.getElementById('fieldReorderLevel').value);
        const location = document.getElementById('fieldLocation').value;

        if (this.currentEdit) {
            const idx = this.stocks.findIndex(s => s.id === this.currentEdit);
            this.stocks[idx] = { ...this.stocks[idx], name, category, stock: quantity, reorderLevel, location };
            showToast('Stock updated', 'success');
        } else {
            this.stocks.push({ id: Date.now(), name, category, stock: quantity, reorderLevel, maxLevel: reorderLevel * 3, location });
            showToast('Stock added', 'success');
        }
        this.filtered = [...this.stocks];
        this.render();
        this.closeModal();
    }
}

window.stocksPage = new StocksPage();