// auth.js - Authentication Module

import { showToast, demoLogin } from './app.js';

class Auth {
    constructor() {
        this.init();
    }

    init() {
        if (document.getElementById('loginForm')) this.setupLogin();
        if (document.getElementById('registerForm')) this.setupRegister();
        if (document.getElementById('forgotForm')) this.setupForgotPassword();
    }

    setupLogin() {
        const form = document.getElementById('loginForm');
        const togglePwd = document.querySelector('.toggle-password');

        if (togglePwd) {
            togglePwd.addEventListener('click', () => {
                const input = document.getElementById('password');
                const icon = togglePwd.querySelector('i');
                const isHidden = input.type === 'password';
                input.type = isHidden ? 'text' : 'password';
                icon.className = isHidden ? 'fas fa-eye-slash' : 'fas fa-eye';
                togglePwd.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
            });
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const submitBtn = form.querySelector('[type="submit"]');

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing in...';

            try {
                // Demo mode: accept demo credentials
                if (email === 'demo@bisos.co.za' && password === 'Demo@123') {
                    demoLogin();
                    window.location.href = 'dashboard.html';
                    return;
                }
                this.showError('Invalid credentials. Use demo@bisos.co.za / Demo@123');
            } catch (err) {
                this.showError(err.message || 'Login failed. Please try again.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-sign-in-alt" aria-hidden="true"></i> Sign In';
            }
        });

        document.getElementById('googleLogin')?.addEventListener('click', () => {
            showToast('Google SSO would redirect to Google OAuth', 'info');
        });

        document.getElementById('microsoftLogin')?.addEventListener('click', () => {
            showToast('Microsoft SSO would redirect to Microsoft OAuth', 'info');
        });

        document.getElementById('demoLoginBtn')?.addEventListener('click', () => {
            document.getElementById('email').value = 'demo@bisos.co.za';
            document.getElementById('password').value = 'Demo@123';
            form.dispatchEvent(new Event('submit'));
        });
    }

    setupRegister() {
        const form = document.getElementById('registerForm');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirmPassword').value;

            if (password !== confirm) {
                this.showError('Passwords do not match.');
                return;
            }

            if (password.length < 8) {
                this.showError('Password must be at least 8 characters.');
                return;
            }

            this.showSuccess('Registration successful! Redirecting to login...');
            setTimeout(() => window.location.href = 'login.html', 2000);
        });
    }

    setupForgotPassword() {
        document.getElementById('forgotForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.showSuccess('Password reset link sent to your email.');
        });
    }

    showError(message) {
        this.clearAlerts();
        const el = document.createElement('div');
        el.className = 'auth-error';
        el.setAttribute('role', 'alert');
        el.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
        document.querySelector('.auth-form').prepend(el);
        setTimeout(() => el.remove(), 5000);
    }

    showSuccess(message) {
        this.clearAlerts();
        const el = document.createElement('div');
        el.className = 'auth-success';
        el.setAttribute('role', 'status');
        el.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`;
        document.querySelector('.auth-form').prepend(el);
    }

    clearAlerts() {
        document.querySelectorAll('.auth-error, .auth-success').forEach(el => el.remove());
    }
}

document.addEventListener('DOMContentLoaded', () => new Auth());
