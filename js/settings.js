import { showToast } from './app.js?v=2';

const API = 'api/settings.php?action=';

class SettingsPage {
    constructor() {
        this.editingId = null;
        this.init();
    }

    async init() {
        this.bindNav();
        this.bindForms();
        try {
            await this.loadProfile();
            await this.loadStores();
            await this.loadRoles();
            await this.loadUsers();
        } catch (err) {
            console.error(err);
            showToast('Failed to load settings', 'error');
        }
    }

    bindNav() {
        document.querySelectorAll('.settings-nav-item').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.settings-nav-item').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                document.querySelectorAll('[id^="section-"]').forEach(s => s.style.display = 'none');
                document.getElementById(`section-${btn.dataset.section}`).style.display = 'block';
            });
        });
    }

    bindForms() {
        document.getElementById('profileForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.saveProfile();
        });
        document.getElementById('passwordForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.changePassword();
        });
        document.getElementById('addUserBtn').addEventListener('click', () => this.openAddUser());
        document.getElementById('modalClose').addEventListener('click', () => this.closeModal());
        document.getElementById('modalCancel').addEventListener('click', () => this.closeModal());
        document.getElementById('userForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.saveUser();
        });
    }

    async loadProfile() {
        try {
            const res = await fetch(API + 'get_profile');
            const json = await res.json();
            const p = json.data || {};
            document.getElementById('pfFirst').value = p.first_name || '';
            document.getElementById('pfLast').value = p.last_name || '';
            document.getElementById('pfEmail').value = p.email || '';
            document.getElementById('pfPhone').value = p.phone || '';
            document.getElementById('pfRole').value = p.role_name || '';
            // Store select is populated in loadStores; set after.
            this.profileStoreId = p.store_id;
            const sel = document.getElementById('pfStore');
            if (sel) sel.value = p.store_id ? String(p.store_id) : '';
        } catch (err) {
            console.error(err);
            showToast('Failed to load profile', 'error');
        }
    }

    async loadStores() {
        try {
            const res = await fetch('api/sales.php?action=stores');
            const json = await res.json();
            const opts = '<option value="">Unassigned</option>' +
                (json.data || []).map(s => `<option value="${s.store_id}">${s.name}</option>`).join('');
            document.getElementById('pfStore').innerHTML = opts;
            document.getElementById('ufStore').innerHTML = opts;
            if (this.profileStoreId) {
                document.getElementById('pfStore').value = String(this.profileStoreId);
            }
        } catch (err) {
            console.error(err);
        }
    }

    async loadRoles() {
        try {
            const res = await fetch(API + 'roles');
            const json = await res.json();
            document.getElementById('ufRole').innerHTML = '<option value="">Select role</option>' +
                (json.data || []).map(r => `<option value="${r.role_id}">${r.role_name}</option>`).join('');
        } catch (err) {
            console.error(err);
        }
    }

    async saveProfile() {
        const payload = {
            first_name: document.getElementById('pfFirst').value.trim(),
            last_name: document.getElementById('pfLast').value.trim(),
            email: document.getElementById('pfEmail').value.trim(),
            phone: document.getElementById('pfPhone').value.trim(),
            store_id: document.getElementById('pfStore').value ? parseInt(document.getElementById('pfStore').value) : null
        };
        try {
            const res = await fetch(API + 'update_profile', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const json = await res.json();
            if (!res.ok || json.status === 'error') {
                showToast(json.message || 'Update failed', 'error');
                return;
            }
            showToast('Profile updated', 'success');
        } catch (err) {
            console.error(err);
            showToast('Network error', 'error');
        }
    }

    async changePassword() {
        const payload = {
            current_password: document.getElementById('curPassword').value,
            new_password: document.getElementById('newPassword').value,
            confirm_password: document.getElementById('confirmPassword').value
        };
        try {
            const res = await fetch(API + 'change_password', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const json = await res.json();
            if (!res.ok || json.status === 'error') {
                showToast(json.message || 'Password update failed', 'error');
                return;
            }
            showToast('Password updated', 'success');
            document.getElementById('passwordForm').reset();
        } catch (err) {
            console.error(err);
            showToast('Network error', 'error');
        }
    }

    async loadUsers() {
        try {
            const res = await fetch(API + 'list_users');
            const json = await res.json();
            this.renderUsers(json.data || []);
        } catch (err) {
            console.error(err);
            showToast('Failed to load users', 'error');
        }
    }

    renderUsers(users) {
        const tbody = document.getElementById('usersTableBody');
        if (!users || users.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:var(--text-secondary);padding:1.5rem">No users found</td></tr>';
            return;
        }
        tbody.innerHTML = users.map(u => {
            const active = u.is_active == 1;
            const statusBadge = active
                ? '<span class="status-badge status-ok">Active</span>'
                : '<span class="status-badge status-low">Inactive</span>';
            return `
                <tr>
                    <td><strong>${u.first_name || ''} ${u.last_name || ''}</strong></td>
                    <td>${u.email}</td>
                    <td>${u.role_name || '-'}</td>
                    <td>${u.store_name || 'Unassigned'}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <div style="display:flex;gap:0.4rem">
                            <button class="btn btn-sm btn-outline" data-edit="${u.user_id}"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-danger" data-del="${u.user_id}"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>`;
        }).join('');

        tbody.querySelectorAll('[data-edit]').forEach(btn => {
            btn.addEventListener('click', () => this.openEditUser(parseInt(btn.dataset.edit)));
        });
        tbody.querySelectorAll('[data-del]').forEach(btn => {
            btn.addEventListener('click', () => this.deleteUser(parseInt(btn.dataset.del)));
        });
    }

    openAddUser() {
        this.editingId = null;
        document.getElementById('modalTitle').textContent = 'Add User';
        document.getElementById('userForm').reset();
        document.getElementById('fieldUserId').value = '';
        document.getElementById('ufPassword').required = true;
        document.getElementById('ufPassword').placeholder = 'Min. 8 characters';
        document.getElementById('activeGroup').style.display = 'none';
        document.getElementById('modalOverlay').classList.add('active');
    }

    openEditUser(userId) {
        const users = this._users || [];
        const u = users.find(x => parseInt(x.user_id) === parseInt(userId));
        if (!u) return;
        this.editingId = parseInt(userId);
        document.getElementById('modalTitle').textContent = 'Edit User';
        document.getElementById('fieldUserId').value = u.user_id;
        document.getElementById('ufFirst').value = u.first_name || '';
        document.getElementById('ufLast').value = u.last_name || '';
        document.getElementById('ufEmail').value = u.email || '';
        document.getElementById('ufPhone').value = u.phone || '';
        document.getElementById('ufRole').value = u.role_id || '';
        document.getElementById('ufStore').value = u.store_id ? String(u.store_id) : '';
        document.getElementById('ufPassword').value = '';
        document.getElementById('ufPassword').required = false;
        document.getElementById('ufPassword').placeholder = 'Leave blank to keep unchanged';
        document.getElementById('activeGroup').style.display = '';
        document.getElementById('ufActive').value = u.is_active == 1 ? '1' : '0';
        document.getElementById('modalOverlay').classList.add('active');
    }

    closeModal() {
        document.getElementById('modalOverlay').classList.remove('active');
        this.editingId = null;
    }

    async saveUser() {
        const payload = {
            first_name: document.getElementById('ufFirst').value.trim(),
            last_name: document.getElementById('ufLast').value.trim(),
            email: document.getElementById('ufEmail').value.trim(),
            phone: document.getElementById('ufPhone').value.trim(),
            role_id: parseInt(document.getElementById('ufRole').value) || 2,
            store_id: document.getElementById('ufStore').value ? parseInt(document.getElementById('ufStore').value) : null
        };
        const pwd = document.getElementById('ufPassword').value;
        if (pwd) payload.password = pwd;

        if (!payload.first_name || !payload.email) {
            showToast('Name and email are required', 'error');
            return;
        }
        if (!this.editingId && !pwd) {
            showToast('Password is required for a new user', 'error');
            return;
        }

        try {
            if (this.editingId) {
                payload.user_id = this.editingId;
                payload.is_active = document.getElementById('ufActive').value === '1';
                const res = await fetch(API + 'update_user', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const json = await res.json();
                if (!res.ok || json.status === 'error') {
                    showToast(json.message || 'Update failed', 'error');
                    return;
                }
                showToast('User updated', 'success');
            } else {
                const res = await fetch(API + 'create_user', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const json = await res.json();
                if (!res.ok || json.status === 'error') {
                    showToast(json.message || 'Create failed', 'error');
                    return;
                }
                showToast('User created', 'success');
            }
            this.closeModal();
            await this.loadUsers();
        } catch (err) {
            console.error(err);
            showToast('Network error', 'error');
        }
    }

    async deleteUser(userId) {
        if (!confirm('Delete this user? This cannot be undone.')) return;
        try {
            const res = await fetch(API + 'delete_user', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ user_id: userId })
            });
            const json = await res.json();
            if (!res.ok || json.status === 'error') {
                showToast(json.message || 'Delete failed', 'error');
                return;
            }
            showToast('User removed', 'success');
            await this.loadUsers();
        } catch (err) {
            console.error(err);
            showToast('Network error', 'error');
        }
    }
}

// Keep a reference so openEditUser can look up role_id/store_id.
const _origRender = SettingsPage.prototype.renderUsers;
SettingsPage.prototype.renderUsers = function (users) {
    this._users = users;
    return _origRender.call(this, users);
};

window.settingsPage = new SettingsPage();