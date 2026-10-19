# BISOS & Informal Mart – Branch Inventory & Sales Optimization System

> **Part 2 POE Submission** for **XISD6329** and **XISD6319**  
> Rosebank College

---

## 📌 Project Overview

This repository contains two connected solutions:

| Solution | Platform | Description |
|----------|----------|-------------|
| **BISOS** | Web Application | Branch Inventory & Sales Optimization System |
| **Informal Mart** | Android Mobile App | Mobile companion for store management |

Both applications help informal supermarkets and small retailers manage their day-to-day operations, especially when the business operates from more than one branch. Staff can keep product information up to date, check stock levels, record sales, manage branches and suppliers, and control access according to each user's role.

---

## 👥 Team Members

| Role | Name | Student Number | Email |
|------|------|----------------|-------|
| Group Leader & Front-End Developer | Kun'we Tyrone Mdaka | ST10262122 | kunwemdaka@gmail.com |
| Project Manager | Oratile Maungwa | ST10443081 | oratilemaungwa@gmail.com |
| Analyst & Documentation Composer | Gontse Rakosa | ST10449265 | gontsekobue2@gmail.com |
| Back-End Developer | Richard Sebola | ST10441486 | ernestsebola22@gmail.com |

---

## 🔗 Important Links

| Resource | Link |
|----------|------|
| Website (Web App) | [BISOS Web App](https://bisos.co.za) |
| Website Presentation | Watch Here |
| Mobile App Presentation | Refer to the PDF *(link to be added)* |
| Figma Design | Link to be added |

---

## 🎯 Project Objectives

- **Up-to-date stock information** — Let staff check current stock, follow stock movements, and record adjustments when needed.
- **Automatic low-stock alerts** — Notify users when an item drops below its set stock limit and recommend how much should be reordered.
- **Sales reporting** — Provide daily, weekly, and monthly sales summaries, including useful trends and figures.
- **Supplier management** — Keep supplier details organised, review supplier performance, and manage purchase orders.
- **Inventory valuation** — Estimate the value of stock using cost and selling prices, and monitor how quickly inventory moves.
- **Audit trail** — Keep a record of important actions so that activity can be traced and reviewed when necessary.

---

## 🛠️ Technology Stack

### Web Application (BISOS)

| Layer | Technology |
|-------|------------|
| Front End | HTML5, CSS3, Vanilla JavaScript |
| Design | Figma |
| Back End | PHP |
| Database | MySQL 8.0+ |
| Hosting | Microsoft Azure Web Apps |
| Local Tools | XAMPP, Visual Studio Code, MySQL Workbench, Draw.io |

### Mobile Application (Informal Mart)

| Layer | Technology |
|-------|------------|
| Platform | Native Android |
| Language | Kotlin / Java |
| Build System | Gradle |
| Design Approach | Android Material Design |

---

## 🏗️ System Architecture

The system is organised into three main layers:

1. **Presentation Layer (Client)** — The part users interact with through the web browser, built with HTML5, CSS3, and JavaScript.
2. **Application Layer (Server)** — Processes requests and applies the system's business rules using server-side code.
3. **Data Layer (Database)** — Stores the information managed by the system in a MySQL 8.0 database.

```
┌─────────────────────────────────────────────────────┐
│              PRESENTATION TIER (Client)              │
│  ┌─────────────────────────────────────────────┐    │
│  │   Web Browser Interface (HTML5, CSS3, JS)   │    │
│  └─────────────────────────────────────────────┘    │
├─────────────────────────────────────────────────────┤
│              APPLICATION TIER (Server)               │
│  ┌─────────────────────────────────────────────┐    │
│  │   Business Logic & Request Processing        │    │
│  │   (PHP/Node.js)                              │    │
│  └─────────────────────────────────────────────┘    │
├─────────────────────────────────────────────────────┤
│                DATA TIER (Database)                  │
│  ┌─────────────────────────────────────────────┐    │
│  │   MySQL Database (MySQL 8.0)                 │    │
│  └─────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────┘
```

---

## 📂 Project Structure

### 1. Web App (`bios-php`)

```
bios-php/
├── api/
│   ├── alerts.php
│   ├── auth.php
│   ├── db.php
│   ├── index.php
│   └── stocks.php
├── config/
│   ├── bootstrap.php
│   ├── config.php
│   └── db.php
├── css/
│   ├── dashboard.css
│   ├── responsive.css
│   ├── stocks.css
│   └── style.css
├── database/
│   ├── schema.sql
│   └── seed.sql
├── includes/
│   ├── footer.php
│   ├── header.php
│   └── nav.php
├── js/
│   ├── alerts.js
│   ├── api.js
│   ├── app.js
│   ├── auth.js
│   ├── dashboard.js
│   ├── sales-report.js
│   └── stocks.js
├── dashboard.php
├── deploy.yml
├── forgot-password.php
├── index.php
├── login.php
├── logout.php
├── low-stock-alerts.php
├── products.php
├── register.php
├── sales-report.php
├── settings.php
├── stocks.php
└── store-management.php
```

### 2. Mobile App (`InformalSupermarket`)

```
InformalSupermarket/
├── app/
│   ├── manifests/
│   │   └── AndroidManifest.xml
│   ├── kotlin+java/
│   │   ├── com.example.informalsupermarket (androidTest)
│   │   └── com.example.informalsupermarket (test)
│   ├── java (generated)/
│   │   └── com.example.informalsupermarket/
│   │       └── BuildConfig
│   ├── res/
│   │   ├── drawable/
│   │   ├── layout/
│   │   ├── mipmap/
│   │   ├── values/
│   │   │   ├── colors.xml
│   │   │   └── strings.xml
│   │   └── xml/
│   └── keepRules/
├── Gradle Scripts/
│   ├── build.gradle.kts (Project)
│   ├── build.gradle.kts (Module :app)
│   ├── gradle.properties
│   ├── gradle-wrapper.properties
│   ├── libs.versions.toml
│   ├── local.properties
│   └── settings.gradle.kts
```

---

## ✨ Key Features

### Web App (BISOS)

| Feature | Description |
|---------|-------------|
| **Dashboard** | Quick view of daily sales, product totals, low-stock warnings, monthly revenue, and stock levels by category. |
| **Product Management** | Add, edit, search, and export product records — including barcodes, cost prices, selling prices, and profit margins. |
| **Stock Management** | Shows inventory across branches with colour-coded labels for critical, low, and normal stock levels. |
| **Low-Stock Alerts** | Highlights products needing attention; allows acknowledgement, resolution, and reorder initiation. |
| **Sales Reports** | Daily, weekly, monthly, or custom reports with PDF/CSV export options. |
| **Store Management** | Manage multiple branches and check each store's stock and contact information. |
| **Settings** | Profile details, notification preferences, security, and system settings (currency ZAR, time zone). |

### Mobile App (Informal Mart)

| Feature | Description |
|---------|-------------|
| **Home / Dashboard** | Summarises total stock, low-stock alerts, and sales for the current day. |
| **Quick Actions** | Shortcuts to view stock, add stock, check sales, and open alerts. |
| **Inventory** | Products displayed in a grid with red indicators for items running low. |
| **Recent Transactions** | Shows the latest recorded sales. |
| **Sales Reports** | Select a date, review report totals, and export as PDF. |
| **Settings** | Profile details, notification options, store information, language preferences, and logout. |

---

## 🗄️ Database Schema

### Main Tables

| Table | Purpose |
|-------|---------|
| **Product** | Stores product details, including SKU, pricing, and supplier reference. |
| **Stock** | Records the quantity of each product held at a particular location. |
| **Category** | Groups products into categories and supports a hierarchy. |
| **Supplier** | Stores supplier contact details and performance information. |
| **Sales Transaction** | Holds daily sales records and transaction details. |
| **Low Stock Alert** | Stores automatically generated notifications for products below their stock limit. |
| **Purchase Order** | Contains the main details for each purchase order. |
| **Purchase Order Item** | Lists the individual products included in a purchase order. |
| **User** | Stores user accounts and login details. |
| **Role** | Defines user roles and their permissions. |
| **Report Log** | Records reports that have been generated. |
| **Audit Log** | Keeps a record of important system actions. |

### Entity Relationship Diagram

```
┌─────────┐     ┌─────────┐     ┌─────────────┐
│Category │─────│ Product │─────│    Stock    │
└─────────┘     └─────────┘     └─────────────┘
                    │  │
                    │  └───────┐
                    │          │
                ┌───┴───┐  ┌───┴──────────┐
                │Sales  │  │LowStockAlert │
                │Trans  │  └──────────────┘
                └───────┘
                    │
             ┌──────┴────────┐
             │               │
         ┌───┴───┐      ┌────┴────┐
         │Product│      │Supplier │
         │       │      └─────────┘
         └───┬───┘           │
             │          ┌────┴────┐
             │          │Purchase │
             │          │  Order  │
             │          └────┬────┘
             └───────────────┘
```

---

## 📖 User Documentation

### BISOS Web App User Guide

#### 1. Introduction

The **Branch Inventory and Sales Optimization System (BISOS)** is a web application built to help informal supermarkets organise their daily operations. Users can manage product information, track stock, review sales, oversee branches, and manage staff access permissions.

#### 2. Getting Started

**System Requirements**

- A current web browser (Chrome, Edge, Safari, or Firefox)
- A reliable internet connection
- Valid login details

**Logging In**

1. Open the BISOS login page.
2. Enter your email address and password.
3. Optionally select **Remember Me**.
4. Click **Sign In**.
5. Use **Demo Account** if you want to explore the system.

**Creating an Account**

1. On the login page, select **Register Here**.
2. Enter your first name, surname, email address, and phone number.
3. Choose your user role (e.g., Manager or Clerk).
4. Create a password with at least eight characters.
5. Read and accept the Terms of Service.
6. Select **Create Account**.

#### 3. Finding Your Way Around BISOS

| Section | Purpose |
|---------|---------|
| **Dashboard** | Key business figures, stock information, and recent activity. |
| **Stocks** | View stock quantities, update records, and identify products needing replenishment. |
| **Products** | Add, search, and update product details. |
| **Reports** | Review sales and financial information for a selected period. |
| **Alerts** | Check warnings for products with low or critical stock levels. |
| **Stores** | Manage and monitor different branches. |
| **Settings** | Update account details, preferences, notifications, and user accounts. |

#### 4. Working with the Main Modules

**Dashboard**  
Overview of stores, products, low-stock warnings, critical items, and daily sales. Includes monthly revenue charts, stock-by-category charts, recent activity, and quick shortcuts.

**Product Management**  
Displays product name, category, barcode, cost price, selling price, profit margin, and perishability. Search, filter, add, edit, delete, and export product records (permissions permitting).

**Stock Management**  
Shows available quantity and reorder level for each product with colour indicators:

| Colour | Status | Meaning |
|--------|--------|---------|
| 🔴 Red | Critical | Quantity is at a critical level. |
| 🟠 Orange | Low | Quantity is low and may need replenishing. |
| 🟢 Green | OK | Quantity is at an acceptable level. |

Users can add stock, change quantities/locations, and export to CSV.

**Low-Stock Alerts**  
Summarises pending, critical, and resolved alerts. Filter by **All**, **Pending**, **Acknowledged**, or **Resolved**. Actions include reorder, acknowledge, resolve, and bulk actions.

**Sales Reports**  
Choose daily, weekly, monthly, or custom date ranges. Filter by store or payment method. View total sales, transaction count, average transaction value, and payment-method breakdown. Export as PDF or CSV.

**Store Management**  
Displays branch address, contact details, manager, product count, alerts, and daily sales. View stock, edit details, or add stores (permissions permitting).

**Settings**  
- **Profile** — Update name, email, and phone number.
- **Notifications** — Email/SMS alerts and daily summaries.
- **Security** — Change password.
- **System Preferences** — Currency and time zone.
- **User Management** — Manage roles and account status (if permitted).

---

### Informal Mart Mobile App User Guide

#### 1. Introduction

**Informal Mart** is an Android app for convenient everyday store management. Check stock, record and review sales, spot low-stock products, and prepare sales reports.

#### 2. Getting Started

**Creating an Account**

1. Open Informal Mart and go to the registration screen.
2. Enter your full name and a valid email address.
3. Create a password.
4. Tap **Create Account** to continue to the dashboard.

#### 3. Navigating the App

The bottom navigation bar contains five sections:

| Section | Purpose |
|---------|---------|
| **Home** | Summary of store activity. |
| **Stock** | Browse and manage inventory. |
| **Sales** | Review recent transactions. |
| **Reports** | View sales summaries and create reports. |
| **Settings** | Manage account information and app preferences. |

#### 4. Using the App's Features

**Home (Dashboard)**  
Summarises total stock, low-stock alerts, and today's sales. Shortcuts: View Stock, Add Stock, View Sales, View Alerts. Scroll for products needing restocking.

**Stock (Inventory)**  
1. Tap **Stock** in the bottom navigation bar.  
2. Browse product cards (name, selling price, available quantity).  
3. Red indicators identify low-stock products.  
4. Tap the **+** icon to add a product or update inventory.

**Sales**  
Tap **Sales** to view recent transactions — product name, quantity sold, and total transaction amount.

**Reports**  
1. Open **Reports** from the bottom navigation bar.  
2. Use the date picker to choose a date.  
3. Check summary figures (total sales, transactions, average sale value).  
4. Review the sales table (product, quantity, unit price, total).  
5. Tap **Export PDF** to save or share.

**Settings**  
- **Profile Settings** — View or update personal details.
- **Notification Preferences** — Manage notifications.
- **Store Information** — Update store details.
- **Language** — Choose preferred language.
- **Logout** — Sign out.

---

## 🎨 UI Design Summaries

### BISOS Web App UI Design System

#### Colour Palette

| Role | Colour | Hex |
|------|--------|-----|
| Primary Brand | Deep Navy Blue | `#1E293B` |
| Secondary Brand | Bright Blue | `#3B82F6` |
| Success | Green | `#10B981` |
| Danger | Red | `#EF4444` |
| Warning | Orange/Yellow | `#F59E0B` |
| Main Background | Light Grey | `#F3F4F6` |
| Card/Panel Background | White | `#FFFFFF` |
| Primary Text | Dark Slate | `#1F2937` |
| Secondary Text | Muted Grey | `#6B7280` |

#### Typography

Sans-serif font (Inter, Roboto, or system default). Headings are bold and dark navy; body text is regular weight and dark grey. Table headings are smaller, muted, and usually uppercase.

#### Layout and Grid Structure

Fixed top navigation bar (logo, module links, notification bell, profile avatar). Main content in a centred container with maximum width and consistent spacing. Information arranged in cards, tables, and grouped panels. Dark-navy footer with copyright, developer credits, and Privacy Policy / Terms of Service / Support links.

#### UI Components

**Navigation** — Horizontal text links with icons. Active section shown with solid blue background and white text. Sub-navigation uses rounded pill-shaped controls.

**Buttons** — Primary: solid navy/blue background, white text, ~6px rounded corners. Secondary: white background, grey border, dark text. Table actions: small icon buttons. Export buttons: red (PDF), green (CSV), with icon + label.

**Tables** — Horizontal row separators, light-grey column headers, subtle hover effect, pagination at bottom-right. Rounded status labels for perishable, Low, OK, and Critical stock.

**Cards & Dashboard Widgets** — Metric cards: white background, light shadow, short label, large bold figure, trend indicator. Store cards with coloured header/banner. Charts: bar charts (revenue) and donut charts (stock distribution) with minimal grid lines and tooltips.

**Forms & Input Fields** — White background, light-grey borders, rounded corners, placeholder text, and relevant icons (envelope, lock). Dropdowns with downward arrow. iOS-style toggle switches. Invalid entries: red border and error message.

#### Screen-by-Screen Interface Breakdown

| Screen | Layout |
|--------|--------|
| **Login/Registration** | Simple centred form card on dark-navy background. Visible labels. Demo-account info in grey box on login. |
| **Dashboard** | Four metric cards (four-column layout). Chart area: monthly revenue bar chart (~2/3 width) + stock-by-category donut chart (~1/3 width). Bottom: recent-activity feed beside quick-action shortcuts. |
| **Product & Stock Management** | Full-width card with search/filter controls above the main table. Add/Export actions above table on the right. Action icons per row. |
| **Sales Reports** | Date-range controls and filter dropdowns at top. Three metric cards + payment-method breakdown in the middle. Detailed transaction table at the bottom. |
| **Settings** | Two columns: left sidebar (Profile, Notifications, Security, System, User Management) + main panel with the selected category's form/settings/user table. |

---

### Informal Mart Mobile App UI Design System

#### Colour Palette

| Role | Colour | Hex |
|------|--------|-----|
| Primary | Dark Green | `#1B5E20` |
| Secondary | White | `#FFFFFF` |
| Alert | Red | `#D32F2F` |
| Text | Dark Grey / Black | — |
| Labels/Hints | Light Grey | — |

#### Typography and Icons

Roboto or standard Android system font. Headings and important numbers are bold. Simple, flat icons consistent with Android Material Design.

#### Common Interface Elements

**Bottom Navigation Bar** — Five sections: Home, Stock, Sales, Reports, Settings. Selected section highlighted with light-green pill-shaped background; icon and label in dark green.

**Top App Bar** — Dark-green background with white text. Back arrow (where needed) on the left, screen title in bold. Action icons on the right (notification bell, profile, plus, filter).

#### Screen-by-Screen Interface Breakdown

| Screen | Layout |
|--------|--------|
| **Registration** | Centred layout. Top bar: back arrow + "Register". Store icon near top centre. Three rounded fields (full name, email, password). Full-width dark-green "Create Account" button. |
| **Home (Dashboard)** | Top bar: app icon + name (left), notification bell with red dot + profile avatar (right). Bold dark-green "DASHBOARD" heading. Three circular indicators: Total Stock, Low Stock Alerts, Today's Sales. Four white shortcut cards (2×2): View Stock, Add Stock, View Sales, View Alerts. "LOW STOCK ALERTS" section with View All link and product list (red warning icon + stock indicator). |
| **Stock (Inventory)** | Top bar: back arrow + "Inventory" + plus icon. Two-column grid of white product cards with subtle shadows (image/placeholder, name, selling price, quantity). Low quantities shown in red. |
| **Sales (Recent Transactions)** | Top bar: back arrow + "Recent Transactions". Vertical list with faint horizontal separators. Product name (bold, left), quantity sold (below), total amount (bold dark green, right). |
| **Reports (Sales Report)** | Top bar: back arrow + "Sales Report" + filter icon. Prominent rounded date field with calendar icon. Three outlined cards: Total Sales, Transactions, Average Sale (values bold dark green). Sales table: Product, Qty, Unit Price, Total. Full-width dark-green "Export PDF" button with PDF icon. |
| **Settings** | Top bar: back arrow + "Settings". Vertical list of options, each with grey icon (left), label, and right-facing chevron. Options: Profile Settings, Notification Preferences, Store Information, Language. Red "Logout" at the bottom centre. |

---

## 📸 Screenshots

### BISOS Web App Screenshots

| Screen | Link |
|--------|------|
| Registration and Login Page | [View](https://screenshots/bisos-login.png) |
| Dashboard Page | [View](https://screenshots/bisos-dashboard.png) |
| Stock Levels Page | [View](https://screenshots/bisos-stocks.png) |
| Product Management Page | [View](https://screenshots/bisos-products.png) |
| Reports Page | [View](https://screenshots/bisos-reports.png) |
| Alerts Page | [View](https://screenshots/bisos-alerts.png) |
| Stores Page | [View](https://screenshots/bisos-stores.png) |
| Settings Page | [View](https://screenshots/bisos-settings.png) |

### Informal Mart Mobile App Screenshots

| Screen | Link |
|--------|------|
| Register Page | [View](https://screenshots/mobile-register.png) |
| Dashboard Page | [View](https://screenshots/mobile-dashboard.png) |
| Sales Report Page | [View](https://screenshots/mobile-sales-report.png) |
| Inventory Page | [View](https://screenshots/mobile-inventory.png) |
| Transaction Page | [View](https://screenshots/mobile-transaction.png) |
| Settings Page | [View](https://screenshots/mobile-settings.png) |
| Informal Mart Page | [View](https://screenshots/mobile-informal-mart.png) |

---

## 📊 Reports

The system provides five main types of reports:

### 1. Daily Sales Report

- Daily sales totals grouped by product
- Revenue breakdown, including VAT
- List of best-selling products

### 2. Low-Stock Alert Report

- Products with quantities below set limits
- Suggested reorder quantities
- Estimated costs for suggested reorders

### 3. Inventory Valuation Report

- Current stock value based on cost and retail prices
- Breakdown of inventory by category
- Inventory turnover ratio

### 4. Supplier Performance Report

- Supplier reliability measures
- On-time delivery rates
- Total spending by supplier

### 5. Purchase Order History Report

- Purchase orders grouped/tracked by status
- Analysis of processing times
- Total values of purchase orders

---

## 📅 Project Timeline

| Milestone | Duration | Week |
|-----------|----------|------|
| Project Planning | 1 week | Week 1 |
| Requirements Analysis | 1 week | Weeks 2–3 |
| System Design | 2 weeks | Weeks 4–5 |
| Prototype Development | 2 weeks | Weeks 6–7 |
| User Acceptance Testing | 1 week | Week 8 |
| Final Submission | 1 week | Week 8 |

---

## 💻 Installation and Setup

### Prerequisites

- XAMPP or a similar local server environment
- MySQL 8.0 or later
- A modern browser (Chrome, Firefox, or Edge)
- A code editor (Visual Studio Code recommended)

### Installation Steps

**1. Clone the repository**

```bash
git clone https://github.com/yourusername/xisd6319-inventory-system.git
```

**2. Start the local server**

```bash
sudo /opt/lampp/lampp start
```

**3. Import the database**

Using phpMyAdmin, or via the command line:

```bash
mysql -u root -p < database/schema.sql
```

**4. Configure the database connection**

Update `config/database.php`:

```php
// config/database.php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'inventory_system');
```

**5. Start the development server**

```bash
php -S localhost:8000 -t public/
```

**6. Open the application**

Visit [http://localhost:8000](http://localhost:8000) in your browser.

---

## 🔒 Security Features

| Feature | Description |
|---------|-------------|
| **Password Hashing** | Uses bcrypt or Argon2 to store passwords securely. |
| **Session Management** | Automatically signs users out after 30 minutes of inactivity. |
| **Input Validation** | Validates submitted information on both client and server sides. |
| **SQL Injection Prevention** | Uses prepared statements and parameterised queries. |
| **XSS Protection** | Sanitizes output before displaying it. |
| **CSRF Protection** | Token-based validation for forms. |
| **Role-Based Access Control** | Limits system functions according to assigned permissions. |
| **Audit Logging** | Records important actions for later review. |

---

## 📚 Documentation

The project documentation includes:

- Project Plan Document
- Requirements Analysis
- System Design Document
- Database Schema
- API Reference
- Database Guide
- User Manual

---

## 📊 Budget Summary

| Project Phase | Hours | Cost (R) |
|---------------|-------|----------|
| Planning | 30 | R4,500 |
| Analysis | 40 | R6,000 |
| Design | 45 | R6,750 |
| Prototype Development | 35 | R5,250 |
| **Subtotal** | **150** | **R22,500** |
| Contingency (5%) | — | R1,125 |
| **Total** | **150** | **R23,625** |

---

## 📝 License

This project was created for academic purposes as part of the **XISD6319** course at **Rosebank College**.

---

## 🙏 Acknowledgements

- **Rosebank College** — for providing the academic framework for the project.
- **Supermarket staff** — for sharing useful input during the requirements-gathering stage.
- **All team members** — for the time, effort, and contributions they brought to the project.

---

<div align="center">

**BISOS & Informal Mart** · XISD6329 / XISD6319 · Rosebank College

</div>
