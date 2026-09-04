# BISOS - Branch Inventory and Sales Optimization System

## Overview
BISOS is a comprehensive inventory management system designed for informal supermarkets in South Africa. It provides real-time stock visibility, automated low-stock notifications, and daily sales reporting across multiple store branches.

## Features
- Real-time stock management with visual level indicators
- Automated low-stock alerts with severity levels (Low / Medium / High / Critical)
- Daily, weekly, and monthly sales reports with CSV/PDF export
- Multi-store support (Soweto, Sandton, Pretoria branches)
- Supplier and reorder management
- Role-based access control (Store Owner / Manager / Clerk / Cashier)
- Google SSO and Microsoft SSO integration
- Responsive design (desktop, tablet, mobile)

## Technology Stack
| Layer | Technology |
|---|---|
| Frontend | HTML5, CSS3, Vanilla JavaScript (ES Modules) |
| Backend | Node.js + Express REST API |
| Mobile | Kotlin + Android Studio |
| Database | MySQL |
| Auth | JWT + Google OAuth 2.0 |
| CI/CD | GitHub Actions |
| Hosting | Netlify (website), Render (API), ClearDB (database) |

## Project Structure
```
BISOS-XISD6319/
├── website/          # Responsive web application (HTML pages)
├── css/              # Stylesheets (style.css, dashboard.css, stocks.css, responsive.css)
├── js/               # JavaScript modules (app.js, api.js, dashboard.js, stocks.js, ...)
├── backend/          # Node.js Express API
├── database/         # schema.sql + seed.sql
├── docs/             # Documentation, wireframes, sitemaps
├── .github/          # GitHub Actions CI/CD workflows
├── index.html        # Landing page
└── README.md
```

## Getting Started

### Prerequisites
- Node.js v18+
- MySQL 8.0+
- A modern web browser

### Frontend (Website)
Open `index.html` in a browser, or serve with any static file server:
```bash
npx serve .
```
Use demo credentials: `demo@bisos.co.za` / `Demo@123`

### Backend API
```bash
cd backend
npm install
cp .env.example .env   # fill in DB credentials and JWT secret
npm run dev
```

### Database Setup
```bash
mysql -u root -p < database/schema.sql
mysql -u root -p bisos_db < database/seed.sql
```

## Web Pages
| Page | File | Description |
|---|---|---|
| Landing | `index.html` | Entry point with feature overview |
| Login | `website/login.html` | Email/password + Google/Microsoft SSO |
| Register | `website/register.html` | New user registration |
| Dashboard | `website/dashboard.html` | KPI cards, charts, recent activity |
| Stocks | `website/stocks.html` | Stock levels, search, filter, CRUD |
| Products | `website/products.html` | Product catalogue management |
| Sales Report | `website/sales-report.html` | Transactions, filters, export |
| Low Stock Alerts | `website/low-stock-alerts.html` | Alert management and reorders |
| Store Management | `website/store-management.html` | Multi-store overview |
| Settings | `website/settings.html` | Profile, notifications, security |

## API Endpoints
```
POST   /v1/auth/login
GET    /v1/auth/google
GET    /v1/stocks?storeId=&category=&status=
PUT    /v1/stocks/:productId
GET    /v1/stocks/low-stock
PUT    /v1/stocks/alerts/:alertId/acknowledge
PUT    /v1/stocks/alerts/:alertId/resolve
GET    /v1/reports/sales?from=&to=&storeId=
GET    /v1/reports/daily
GET    /v1/reports/weekly
GET    /v1/reports/monthly
GET    /v1/stores
POST   /v1/stores
PUT    /v1/stores/:storeId
```

## Contributors
| Name | Student No. | Role |
|---|---|---|
| Kun'we Tyrone Mdaka | ST10262122 | Group Leader & Front-End Developer |
| Oratile Maungwa | ST10443081 | Project Manager |
| Gontse Rakosa | ST10449265 | Analyst & Documentation Composer |
| Richard Sebola | ST10441486 | Back-End Developer |

## License
Developed for academic purposes — XISD6319.
