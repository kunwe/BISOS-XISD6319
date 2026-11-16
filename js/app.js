export function showToast(message, type = 'info') {
    const existing = document.querySelector('.toast');
    if (existing) existing.remove();
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.setAttribute('role', 'alert');
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

export function formatCurrency(amount) {
    return `R${Number(amount).toLocaleString('en-ZA', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
}

// --- Store context (persisted across pages) ------------------------------
const STORE_KEY = 'bisos_selected_store';

export function getSelectedStore() {
    const v = localStorage.getItem(STORE_KEY);
    return v ? parseInt(v) : null;
}

export function setSelectedStore(storeId) {
    if (storeId) {
        localStorage.setItem(STORE_KEY, String(storeId));
    } else {
        localStorage.removeItem(STORE_KEY);
    }
}