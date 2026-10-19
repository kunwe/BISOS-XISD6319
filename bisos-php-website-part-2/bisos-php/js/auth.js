import { showToast } from './app.js';

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.toggle-password').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.parentElement.querySelector('input');
            const icon  = btn.querySelector('i');
            const hidden = input.type === 'password';
            input.type = hidden ? 'text' : 'password';
            icon.className = hidden ? 'fas fa-eye-slash' : 'fas fa-eye';
        });
    });

    document.getElementById('demoLoginBtn')?.addEventListener('click', () => {
        document.getElementById('email').value = 'demo@bisos.co.za';
        document.getElementById('password').value = 'Demo@123';
        document.getElementById('loginForm').submit();
    });

    document.getElementById('googleLogin')?.addEventListener('click', () => showToast('Google SSO would redirect', 'info'));
    document.getElementById('microsoftLogin')?.addEventListener('click', () => showToast('Microsoft SSO would redirect', 'info'));
});