<?php
require_once __DIR__ . '/config/config.php';
require_login();

$pageTitle   = 'Settings';
$activePage  = 'settings';
$extraCss    = ['css/stocks.css'];
$pageScript  = 'js/settings.js';
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
                <button class="settings-nav-item" data-section="security"><i class="fas fa-shield-alt"></i> Security</button>
                <button class="settings-nav-item" data-section="users"><i class="fas fa-users"></i> User Management</button>
            </div>
        </nav>

        <div class="settings-panel">
            <section id="section-profile">
                <div class="settings-section">
                    <h3><i class="fas fa-user"></i> Profile Information</h3>
                    <form class="form-grid" id="profileForm">
                        <div class="form-group"><label for="pfFirst">First Name</label><input type="text" id="pfFirst"></div>
                        <div class="form-group"><label for="pfLast">Last Name</label><input type="text" id="pfLast"></div>
                        <div class="form-group"><label for="pfEmail">Email Address</label><input type="email" id="pfEmail"></div>
                        <div class="form-group"><label for="pfPhone">Phone Number</label><input type="tel" id="pfPhone"></div>
                        <div class="form-group"><label for="pfRole">Role</label><input type="text" id="pfRole" readonly style="background:var(--background)"></div>
                        <div class="form-group"><label for="pfStore">Assigned Store</label>
                            <select id="pfStore"></select>
                        </div>
                        <div class="form-group full" style="grid-column:1/-1"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button></div>
                    </form>
                </div>
            </section>

            <section id="section-security" style="display:none">
                <div class="settings-section">
                    <h3><i class="fas fa-lock"></i> Change Password</h3>
                    <form class="form-grid" style="max-width:400px" id="passwordForm">
                        <div class="form-group full"><label for="curPassword">Current Password</label><input type="password" id="curPassword" required></div>
                        <div class="form-group full"><label for="newPassword">New Password</label><input type="password" id="newPassword" placeholder="Min. 8 characters" required></div>
                        <div class="form-group full"><label for="confirmPassword">Confirm New Password</label><input type="password" id="confirmPassword" required></div>
                        <div class="form-group full"><button type="submit" class="btn btn-primary"><i class="fas fa-key"></i> Update Password</button></div>
                    </form>
                </div>
            </section>

            <section id="section-users" style="display:none">
                <div class="settings-section">
                    <div class="section-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--spacing-md)">
                        <h3 style="margin:0"><i class="fas fa-users"></i> System Users</h3>
                        <button class="btn btn-sm btn-primary" id="addUserBtn"><i class="fas fa-plus"></i> Add User</button>
                    </div>
                    <div class="table-wrapper">
                        <table>
                            <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Store</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody id="usersTableBody">
                                <tr><td colspan="6" style="text-align:center;padding:1.5rem;color:var(--text-secondary)">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
</main>

<div class="modal-overlay" id="modalOverlay">
    <div class="modal">
        <div class="modal-header">
            <h2 id="modalTitle">Add User</h2>
            <button class="modal-close" id="modalClose">&times;</button>
        </div>
        <form id="userForm">
            <input type="hidden" id="fieldUserId">
            <div class="form-grid">
                <div class="form-group"><label for="ufFirst">First Name *</label><input type="text" id="ufFirst" required></div>
                <div class="form-group"><label for="ufLast">Last Name</label><input type="text" id="ufLast"></div>
                <div class="form-group full"><label for="ufEmail">Email *</label><input type="email" id="ufEmail" required></div>
                <div class="form-group"><label for="ufPhone">Phone</label><input type="tel" id="ufPhone"></div>
                <div class="form-group"><label for="ufRole">Role *</label><select id="ufRole" required></select></div>
                <div class="form-group"><label for="ufStore">Store</label><select id="ufStore"></select></div>
                <div class="form-group full"><label for="ufPassword">Password <span id="pwdHint" style="color:var(--text-secondary);font-weight:400">(min. 8 chars)</span></label><input type="password" id="ufPassword" placeholder="Leave blank to keep unchanged"></div>
                <div class="form-group full" id="activeGroup" style="display:none"><label for="ufActive">Status</label><select id="ufActive"><option value="1">Active</option><option value="0">Inactive</option></select></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" id="modalCancel">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>