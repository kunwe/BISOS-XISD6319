// api.js - API Service Layer

const API_BASE_URL = 'https://api.bisos.co.za/v1';

class ApiService {
    constructor() {
        this.baseURL = API_BASE_URL;
        this.token = localStorage.getItem('authToken');
    }

    getHeaders() {
        const headers = { 'Content-Type': 'application/json', 'Accept': 'application/json' };
        if (this.token) headers['Authorization'] = `Bearer ${this.token}`;
        return headers;
    }

    async request(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        try {
            const response = await fetch(url, { ...options, headers: { ...this.getHeaders(), ...options.headers } });
            if (!response.ok) {
                const error = await response.json().catch(() => ({}));
                throw new Error(error.message || `HTTP ${response.status}`);
            }
            return await response.json();
        } catch (error) {
            console.error('API Error:', error.message);
            throw error;
        }
    }

    // Auth
    async login(email, password) {
        const result = await this.request('/auth/login', { method: 'POST', body: JSON.stringify({ email, password }) });
        if (result.token) {
            this.token = result.token;
            localStorage.setItem('authToken', result.token);
            localStorage.setItem('user', JSON.stringify(result.user));
        }
        return result;
    }

    async googleSSO(token) {
        const result = await this.request('/auth/google', { method: 'POST', body: JSON.stringify({ token }) });
        if (result.token) {
            this.token = result.token;
            localStorage.setItem('authToken', result.token);
            localStorage.setItem('user', JSON.stringify(result.user));
        }
        return result;
    }

    logout() {
        this.token = null;
        localStorage.removeItem('authToken');
        localStorage.removeItem('user');
        window.location.href = 'login.html';
    }

    // Stocks
    async getStocks(filters = {}) {
        return this.request(`/stocks?${new URLSearchParams(filters)}`);
    }

    async updateStock(productId, quantity) {
        return this.request(`/stocks/${productId}`, { method: 'PUT', body: JSON.stringify({ quantity }) });
    }

    async getLowStockAlerts(storeId = '') {
        return this.request(`/stocks/low-stock${storeId ? `?storeId=${storeId}` : ''}`);
    }

    async acknowledgeAlert(alertId) {
        return this.request(`/stocks/alerts/${alertId}/acknowledge`, { method: 'PUT' });
    }

    async resolveAlert(alertId) {
        return this.request(`/stocks/alerts/${alertId}/resolve`, { method: 'PUT' });
    }

    // Products
    async getProducts() { return this.request('/products'); }

    async createProduct(data) {
        return this.request('/products', { method: 'POST', body: JSON.stringify(data) });
    }

    async updateProduct(id, data) {
        return this.request(`/products/${id}`, { method: 'PUT', body: JSON.stringify(data) });
    }

    async deleteProduct(id) {
        return this.request(`/products/${id}`, { method: 'DELETE' });
    }

    // Reports
    async getSalesReport(params = {}) {
        return this.request(`/reports/sales?${new URLSearchParams(params)}`);
    }

    async getDailySales() { return this.request('/reports/daily'); }
    async getWeeklySales() { return this.request('/reports/weekly'); }
    async getMonthlySales() { return this.request('/reports/monthly'); }

    // Stores
    async getStores() { return this.request('/stores'); }

    async createStore(data) {
        return this.request('/stores', { method: 'POST', body: JSON.stringify(data) });
    }

    async updateStore(id, data) {
        return this.request(`/stores/${id}`, { method: 'PUT', body: JSON.stringify(data) });
    }

    // Reorders
    async createReorderOrder(data) {
        return this.request('/reorders', { method: 'POST', body: JSON.stringify(data) });
    }

    async getReorderOrders() { return this.request('/reorders'); }
}

export default new ApiService();
