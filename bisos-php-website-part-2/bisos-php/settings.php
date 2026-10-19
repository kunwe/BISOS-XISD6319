<?php
require_once __DIR__ . '/config/config.php';
require_login();

$pageTitle  = 'Settings';
$activePage = 'settings';
$extraCss   = ['css/stocks.css'];
$inlineStyle = '
    .settings-layout { display:grid; grid-template-columns:220px 1fr; gap:var(--spacing-lg); }
    .settings-nav { background:var(--card-bg); border-radius:var(--border-radius); box-shadow:var(--shadow); padding:var(--spacing-md); height:fit-content; }
    .settings-nav-item { display:flex; align-items:center; gap:0.75rem; padding:0.65rem var(--spacing-md); border-radius:var(--border-radius); cursor:pointer; font-size:0.9rem; font-weight:500; color:var(--text-secondary); transition:all 0.2s; border:none; background:none; width:100%; text-align:left; font-family:var(--font-family); }
    .settings-nav-item:hover { background:var(--background); color:var(--primary); }
    .settings-nav-item.active { background:var(--primary-light); color:white; }
    .settings-panel { background:var(--card-bg); border-radius:var(--border-radius); box-shadow:var(--shadow); padding:var(--spacing-lg); }
    .settings-section { margin-bottom:var(--spacing-lg); padding-bottom:var(--spacing-lg); border-bottom:1px solid var(--background); }
    .settings-section:last-child { border-bottom:none; margin-bottom:0; padding-bottom:0; }
    .settings-section h3 { font-size:1rem; margin-bottom:var(--spacing-md); color:var(--primary); }
    .toggle-row { display:flex; justify-content:space-between; align-items:center; padding:0.5rem 0; }
    .toggle-row label { font-size:0.9rem; }
    .toggle-row .toggle-desc { font-size:0.8rem; color:var(--text-secondary); }
    .toggle { position:relative; width:44px; height:24px; }
    .toggle input { opacity:0; width:0; height:0; }
    .toggle-slider { position:absolute; inset:0; background:#ccc; border-radius:24px; cursor:pointer; transition:0.3s; }
    .toggle-slider::before { content:""; position:absolute; width:18px; height:18px; left:3px; bottom:3px; background:white; border-radius:50%; transition:0.3s; }
    .toggle input:checked + .toggle-slider { background:var(--primary-light); }
    .toggle input:checked + .toggle-slider::before { transform:translateX(20px); }
    @media(max-width:768px) { .settings-layout { grid-template-columns:1fr; } }
';
include __DIR__ . '/includes/header.php';
?>
<main role="main">
    <div class="page-header">
        <div>
            <h1>Settings</h1>
            <p>Manage your account and system preferences</p>
        </div>
    </div>

    <div class="settings-layout">
        <nav>
            <div class="settings-nav">
                <button class="settings-nav-item active" data-section="profile"><i class="fas fa-user"></i> Profile</button>
                <button class="settings-nav-item" data-section="notifications"><i class="fas fa-bell"></i> Notifications</button>
                <button class="settings-nav-item" data-section="security"><i class="fas fa-shield-alt"></i> Security</button>
                <button class="settings-nav-item" data-section="system"><i class="fas fa-cog"></i> System</button>
                <button class="settings-nav-item" data-section="users"><i class="fas fa-users"></i> User Management</button>
            </div>
        </nav>

        <div class="settings-panel">
            <section id="section-profile">
                <div class="settings-section">
                    <h3><i class="fas fa-user"></i> Profile Information</h3>
                    <form class="form-grid" id="profileForm">
                        <div class="form-group"><label>First Name</label><input type="text" value="John"></div>
                        <div class="form-group"><label>Last Name</label><input type="text" value="Doe"></div>
                        <div class="form-group"><label>Email Address</label><input type="email" value="john.doe@bisos.co.za"></div>
                        <div class="form-group"><label>Phone Number</label><input type="tel" value="011-123-4567"></div>
                        <div class="form-group"><label>Role</label><input type="text" value="Manager" readonly style="background:var(--background)"></div>
                        <div class="form-group"><label>Assigned Store</label><input type="text" value="Soweto Branch" readonly style="background:var(--background)"></div>
                        <div class="form-group full" style="grid-column:1/-1"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button></div>
                    </form>
                </div>
            </section>

            <section id="section-notifications" style="display:none">
                <div class="settings-section">
                    <h3><i class="fas fa-bell"></i> Alert Notifications</h3>
                    <div class="toggle-row"><div><label>Low Stock Email Alerts</label><p class="toggle-desc">Receive email when stock falls below reorder level</p></div><label class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></label></div>
                    <div class="toggle-row"><div><label>Critical Stock SMS Alerts</label><p class="toggle-desc">SMS for critical stock levels</p></div><label class="toggle"><input type="checkbox" checked><span class="toggle-slider"></span></label></div>
                    <div class="toggle-row"><div><label>Daily Sales Summary</label><p class="toggle-desc">Receive daily sales report at end of day</p></div><label class="toggle"><input type="checkbox"><span class="toggle-slider"></span></label></div>
                </div>
            </section>

            <section id="section-security" style="display:none">
                <div class="settings-section">
                    <h3><i class="fas fa-lock"></i> Change Password</h3>
                    <form class="form-grid" style="max-width:400px">
                        <div class="form-group full"><label>Current Password</label><input type="password"></div>
                        <div class="form-group full"><label>New Password</label><input type="password"></div>
                        <div class="form-group full"><label>Confirm New Password</label><input type="password"></div>
                        <div class="form-group full"><button type="submit" class="btn btn-primary"><i class="fas fa-key"></i> Update Password</button></div>
                    </form>
                </div>
            </section>

            <section id="section-system" style="display:none">
                <div class="settings-section">
                    <h3><i class="fas fa-cog"></i> System Preferences</h3>
                    <div class="form-grid" style="max-width:500px">
                        <div class="form-group"><label>Currency</label><select><option>ZAR (R)</option></select></div>
                        <div class="form-group"><label>Timezone</label><select><option>Africa/Johannesburg (SAST)</option></select></div>
                    </div>
                    <button class="btn btn-primary" style="margin-top:var(--spacing-md)"><i class="fas fa-save"></i> Save Preferences</button>
                </div>
            </section>

            <section id="section-users" style="display:none">
                <div class="settings-section">
                    <h3><i class="fas fa-users"></i> System Users</h3>
                    <table>
                        <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th></tr></thead>
                        <tbody>
                            <tr><td>John Doe</td><td>john.doe@bisos.co.za</td><td>Manager</td><td><span class="status-badge status-ok">Active</span></td></tr>
                            <tr><td>Jane Smith</td><td>jane.smith@bisos.co.za</td><td>Clerk</td><td><span class="status-badge status-ok">Active</span></td></tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</main>
<script type="module">
    import { showToast } from './js/app.js';
    document.querySelectorAll('.settings-nav-item').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.settings-nav-item').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            document.querySelectorAll('[id^="section-"]').forEach(s => s.style.display = 'none');
            document.getElementById(`section-${btn.dataset.section}`).style.display = 'block';
        });
    });
    document.getElementById('profileForm')?.addEventListener('submit', (e) => {
        e.preventDefault();
        showToast('Profile updated successfully', 'success');
    });
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>