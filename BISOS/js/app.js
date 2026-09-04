// app.js - Shared Application Logic

// Inject navigation into every page
function renderNav(activePage) {
    const nav = document.getElementById('main-nav');
    if (!nav) return;

    const pages = [
        { href: 'dashboard.html', icon: 'fa-home', label: 'Dashboard', id: 'dashboard' },
        { href: 'stocks.html', icon: 'fa-boxes', label: 'Stocks', id: 'stocks' },
        { href: 'products.html', icon: 'fa-cube', label: 'Products', id: 'products' },
        { href: 'sales-report.html', icon: 'fa-chart-bar', label: 'Reports', id: 'sales-report' },
        { href: 'low-stock-alerts.html', icon: 'fa-exclamation-triangle', label: 'Alerts', id: 'alerts', badge: 8 },
        { href: 'store-management.html', icon: 'fa-store', label: 'Stores', id: 'stores' },
        { href: 'settings.html', icon: 'fa-cog', label: 'Settings', id: 'settings' },
    ];

    const user = JSON.parse(localStorage.getItem('user') || '{"name":"John Doe","initials":"JD"}');

    nav.innerHTML = `
        <a href="dashboard.html" class="nav-brand" aria-label="BISOS Home">
            <div class="logo-icon" aria-hidden="true">B</div>
            <span>BISOS</span>
        </a>

        <button class="hamburger" id="hamburger" aria-label="Toggle navigation" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>

        <ul class="nav-menu" id="nav-menu" role="menubar">
            ${pages.map(p => `
                <li role="none">
                    <a href="${p.href}" role="menuitem" class="${activePage === p.id ? 'active' : ''}"
                       ${activePage === p.id ? 'aria-current="page"' : ''}>
                        <i class="fas ${p.icon}" aria-hidden="true"></i>
                        ${p.label}
                        ${p.badge ? `<span class="badge" aria-label="${p.badge} alerts">${p.badge}</span>` : ''}
                    </a>
                </li>
            `).join('')}
        </ul>

        <div class="nav-user">
            <div class="user-avatar" aria-hidden="true">${user.initials || 'JD'}</div>
            <span class="user-name">${user.name || 'John Doe'}</span>
            <button class="logout-btn" id="logoutBtn" aria-label="Logout">
                <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
            </button>
        </div>
    `;

    // Hamburger toggle
    document.getElementById('hamburger').addEventListener('click', function () {
        const menu = document.getElementById('nav-menu');
        const expanded = this.getAttribute('aria-expanded') === 'true';
        menu.classList.toggle('open');
        this.setAttribute('aria-expanded', String(!expanded));
    });

    // Logout
    document.getElementById('logoutBtn').addEventListener('click', () => {
        localStorage.removeItem('authToken');
        localStorage.removeItem('user');
        window.location.href = 'login.html';
    });
}

// Render shared footer
function renderFooter() {
    const footer = document.getElementById('main-footer');
    if (!footer) return;
    footer.innerHTML = `
        <div class="footer-content">
            <p>&copy; 2026 BISOS &mdash; Branch Inventory and Sales Optimization System</p>
            <p>Developed by: Mdaka K.T. &bull; Maungwa O. &bull; Rakosa G. &bull; Sebola R.</p>
            <nav aria-label="Footer navigation">
                <ul>
                    <li><a href="privacy.html">Privacy Policy</a></li>
                    <li><a href="terms.html">Terms of Service</a></li>
                    <li><a href="support.html">Support</a></li>
                </ul>
            </nav>
        </div>
    `;
}

// Toast notification
function showToast(message, type = 'info') {
    const existing = document.querySelector('.toast');
    if (existing) existing.remove();
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.setAttribute('role', 'alert');
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// Format currency (ZAR)
function formatCurrency(amount) {
    return `R${Number(amount).toLocaleString('en-ZA', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
}

// Format date
function formatDate(dateStr) {
    return new Date(dateStr).toLocaleDateString('en-ZA', { day: '2-digit', month: '2-digit', year: '2-digit' });
}

// Format time
function formatTime(dateStr) {
    return new Date(dateStr).toLocaleTimeString('en-ZA', { hour: '2-digit', minute: '2-digit' });
}

// Auth guard for protected pages
function requireAuth() {
    if (!localStorage.getItem('authToken')) {
        window.location.href = 'login.html';
    }
}

// Demo login helper (sets a fake token for demo purposes)
function demoLogin() {
    localStorage.setItem('authToken', 'demo-token-123');
    localStorage.setItem('user', JSON.stringify({ name: 'John Doe', initials: 'JD', role: 'Manager' }));
}

export { renderNav, renderFooter, showToast, formatCurrency, formatDate, formatTime, requireAuth, demoLogin };
