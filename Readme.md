BISOS & Informal Mart - Branch Inventory & Sales Optimization System
https://bios-logo.png/

📌 Project Overview
This repository contains the Part 2 POE submission for XISD6329 and the XISD6319 project. The project consists of a comprehensive web-based platform (BISOS) and a companion mobile application (Informal Mart) designed to help informal supermarkets and retail businesses operating across multiple branches manage their daily activities.

The systems allow users to maintain product records, monitor stock levels, track sales, manage store branches, manage suppliers, and control user access based on assigned roles.

👥 Team Members
Role	Name	Student Number	Email
Group Leader & Front-End Developer	Kun'we Tyrone Mdaka	ST10262122	kunwemdaka@gmail.com
Project Manager	Oratile Maungwa	ST10443081	oratilemaungwa@gmail.com
Analyst & Documentation Composer	Gontse Rakosa	ST10449265	gontsekobue2@gmail.com
Back-End Developer	Richard Sebola	ST10441486	ernestsebola22@gmail.com
🔗 Important Links
Website (Web App) URL: BISOS Web App

YouTube Video Presentation (Website): Watch Here

YouTube Video Presentation (Mobile App): (See PDF reference - Link pending)

Figma Design: (Link to be added)

🎯 Project Objectives
Real-time Stock Management: Enable employees to view current stock levels instantly, track stock movements, and adjustments.

Automated Low-Stock Alerts: Generate real-time notifications when stock falls below threshold and suggest reorder quantities automatically.

Sales Reporting: Generate daily, weekly, and monthly sales reports with analytics and trends.

Supplier Management: Track supplier information, performance, and manage purchase orders efficiently.

Inventory Valuation: Calculate current inventory value at cost and retail, and track inventory turnover rates.

Audit Trail: Log all critical system actions to provide accountability and traceability.

🛠️ Technology Stack
Web Application (BISOS)
Frontend: HTML5, CSS3, JavaScript (Vanilla), Figma (Design)

Backend: PHP (Server-side logic)

Database: MySQL 8.0+

Hosting: Microsoft Azure Web Apps

Local Development: XAMPP, Visual Studio Code, MySQL Workbench, Draw.io

Mobile Application (Informal Mart)
Platform: Android (Native)

Language: Kotlin / Java

Build System: Gradle

Design System: Android Material Design

🏗️ System Architecture
The system follows a three-tier architecture:

text
┌─────────────────────────────────────────────────────┐
│              PRESENTATION TIER (Client)              │
│  ┌─────────────────────────────────────────────┐    │
│  │   Web Browser Interface (HTML5, CSS3, JS)   │    │
│  └─────────────────────────────────────────────┘    │
├─────────────────────────────────────────────────────┤
│              APPLICATION TIER (Server)               │
│  ┌─────────────────────────────────────────────┐    │
│  │   Business Logic & Request Processing        │    │
│  │   (PHP/Node.js)                             │    │
│  └─────────────────────────────────────────────┘    │
├─────────────────────────────────────────────────────┤
│                DATA TIER (Database)                  │
│  ┌─────────────────────────────────────────────┐    │
│  │   MySQL Database (MySQL 8.0)                │    │
│  └─────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────┘
📂 Project Structure
1. Web App (bios-php)
Based on the VS Code structure, the web application is organized as follows:

text
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
2. Mobile App (InformalSupermarket)
Based on the Android Studio structure, the mobile application is organized as follows:

text
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
✨ Key Features
Web App (BISOS)
Dashboard: Overview of daily sales, total products, low-stock alerts, and interactive charts (monthly revenue, stock by category).

Product Management: Add, edit, search, and export product lists (barcodes, cost price, selling price, profit margins).

Stock Management: Monitor inventory across branches with color-coded status labels (Critical, Low, OK).

Low-Stock Alerts: Automated alerts with options to acknowledge, resolve, or reorder stock.

Sales Reports: Generate daily, weekly, monthly, or custom reports. Export data as PDF or CSV.

Store Management: Manage multiple branches, view individual branch stock, and contact details.

Settings: Profile management, notification preferences, security, and system settings (ZAR currency, timezone).

Mobile App (Informal Mart)
Home/Dashboard: Quick summary of Total Stock, Low Stock Alerts, and Today's Sales.

Quick Actions: Fast access to View Stock, Add Stock, View Sales, and View Alerts.

Inventory (Stock): Grid view of products with visual red alerts for low quantities.

Recent Transactions (Sales): Review recent sales records.

Sales Reports: Generate daily reports with a date picker, summary metrics, and an "Export PDF" function.

Settings: Profile, Notifications, Store Info, Language, and Logout.

🗄️ Database Schema
Core Tables
Table	Description
Product	Product information including SKU, pricing, and supplier reference
Stock	Current inventory levels by product and location
Category	Product categorization with hierarchical support
Supplier	Supplier details and performance metrics
Sales Transaction	Daily sales records with transaction details
Low Stock Alert	Auto-generated low stock notifications
Purchase Order	Purchase order header information
Purchase Order Item	Line items for each purchase order
User	System user accounts and credentials
Role	User roles with permission definitions
Report Log	Audit trail of generated reports
Audit Log	Comprehensive action logging
Entity Relationship Diagram
text
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
            ┌───────┴────────┐
            │                │
        ┌───┴───┐       ┌────┴────┐
        │Product│       │Supplier │
        │       │       └─────────┘
        └───┬───┘            │
            │           ┌────┴────┐
            │           │Purchase │
            │           │  Order  │
            │           └────┬────┘
            │                │
            └────────────────┘
📖 User Documentation Summary
BISOS Web App User Guide
1. Introduction
The Branch Inventory and Sales Optimization System (BISOS) is a web-based platform developed to help informal supermarkets manage daily activities effectively. It allows users to maintain product records, monitor stock levels, track sales, manage store branches, and control user access based on roles.

2. Getting Started

System Requirements: Modern web browser (Chrome, Edge, Safari, Firefox), reliable internet connection, and valid login details.

Logging In: Navigate to the BISOS login page, enter registered email and password, optionally select "Remember Me," and click "Sign In." A "Use Demo Account" option is available for exploration.

Creating a New Account: Click "Register Here" on the login page. Enter first name, last name, email, phone number, select user role (Manager/Clerk), create a password (min 8 chars), accept Terms of Service, and click "Create Account."

3. Navigating the System
BISOS uses a top navigation bar for moving between sections:

Dashboard: Summary of business performance (sales, stock, recent activities).

Stocks: Monitor inventory quantities, update stock records, identify items needing replenishment.

Products: Add, edit, search, and manage product information.

Reports: Display sales and financial information for selected periods.

Alerts: Notifications for products at low or critical stock levels.

Stores: Manage and monitor multiple business branches.

Settings: Update account details, configure preferences, manage notifications, review user accounts.

4. Using the Modules

Dashboard: Displays performance metrics (total stores, products, low-stock alerts, critical items, daily sales), charts (monthly revenue, stock by category), recent activity, and quick actions.

Product Management: View product list (name, category, barcode, cost price, selling price, profit margin, perishable status). Actions include search/filter, add product, edit/delete, and export.

Stock Management: Displays quantity available, reorder level, and color-coded status labels (Critical/Red, Low/Orange, OK/Green). Actions include adding stock, editing quantity/location, and exporting CSV.

Low-Stock Alerts: Summary cards (pending alerts, critical items, resolved alerts). Filters (All, Pending, Acknowledged, Resolved), individual actions (Reorder), and bulk actions (Acknowledge All, Resolve All, Reorder Multiple).

Sales Reports: Select reporting period (Daily, Weekly, Monthly, Custom Range), apply filters (store, payment method), review metrics (total sales, transactions, average transaction value, payment breakdown), and view detailed transaction table. Export as PDF or CSV.

Store Management: View store cards (address, contact, manager, product count, alerts, daily sales). Actions include View Stock, Edit, and Add Store.

Settings: Manage Profile (name, email, phone), Notifications (email/SMS alerts, daily summaries), Security (password), System Preferences (currency, timezone), and User Management (roles, active status).

Informal Mart Mobile App User Guide
1. Introduction
Informal Mart is a mobile application designed to make managing a small business easier. It allows store owners to track stock, record sales, monitor low products, and generate sales reports.

2. Getting Started

Creating an Account: Open the app to access the registration screen. Enter full name, valid email address, and create a password. Tap "Create Account" to complete registration and continue to the main dashboard.

3. Navigating the Application
Informal Mart uses a bottom navigation bar with five main sections:

Home: Overview of store activities and important information.

Stock: View and manage inventory.

Sales: List of recent sales transactions.

Reports: Sales summaries and report generation options.

Settings: Manage account details and app preferences.

4. Features and How to Use Them

Home (Dashboard): View business overview (total stock, low-stock alerts, today's sales), quick actions (View Stock, Add Stock, View Sales, View Alerts), and low-stock notifications (scroll down to see products needing restocking, select "View All" for complete list).

Stock (Inventory): Select "Stock" from bottom nav. Browse products in a grid (product name, selling price, current stock quantity). Check stock indicators (red = running low). Tap "+" icon to add new product or update inventory.

Sales: Tap "Sales" on bottom nav. View list of recent transactions (product name, quantity sold, total amount).

Reports: Select "Reports" from bottom nav. Choose a date using the date picker. Review summary (total sales, number of transactions, average value per sale). Check sales table (products sold, quantities, unit prices, total amounts). Tap green "Export PDF" button to save or share report.

Settings: Tap "Settings" on bottom nav. Open "Profile Settings" to view/update personal details. Select "Notification Preferences" to manage notifications. Update "Store Information" and choose preferred "Language." Select red "Logout" option to sign out.

🎨 UI Design Summaries
BISOS (Web App) UI Design System
1. Design System and Style Guide

Colour Palette:

Primary Brand: Deep Navy Blue (#1E293B) - Headers, important text, primary elements.

Secondary Brand: Bright Blue (#3B82F6) - Links, highlighted elements, active nav items.

Success: Green (#10B981) - Positive outcomes, normal stock levels, CSV export button.

Danger: Red (#EF4444) - Critical situations, destructive actions, PDF export button.

Warning: Orange/Yellow (#F59E0B) - Low-stock conditions, attention-required situations.

Main Background: Light Grey (#F3F4F6).

Card/Panel Background: White (#FFFFFF).

Primary Text: Dark Slate (#1F2937).

Secondary Text: Muted Grey (#6B7280).

Typography: Sans-serif font (Inter, Roboto, or system default). Headings in bold, dark navy. Body text regular weight, dark grey. Table headings in smaller uppercase, muted grey.

Layout and Grid Structure: Fixed top navigation bar with logo, module links, notification bell, profile avatar. Main content area uses a container with max-width and consistent spacing, arranged in cards, tables, and grouped panels. Dark navy footer with copyright, developer credits, and links (Privacy Policy, Terms of Service, Support).

2. User Interface Components

Navigation Elements: Top navigation uses horizontal text links with icons. Active section highlighted with solid blue background and white text. Sub-navigation uses rounded, pill-shaped controls with active option distinguished by white background and subtle shadow.

Buttons: Primary buttons use solid navy/blue background, white text, 6px rounded corners. Secondary buttons use white background, grey border, dark text. Table action buttons are small icon-based controls. Export buttons include icon and text (red for PDF, green for CSV).

Tables: Simple layout with horizontal lines separating rows. Light grey backgrounds for column headings. Subtle hover effects. Pagination controls at bottom-right. Rounded status badges (perishable, Low/OK/Critical).

Cards and Dashboard Widgets: Metric cards have white background, subtle shadow, small descriptive label, large bold number, trend indicator. Store cards include colored header/banner. Dashboard charts include bar charts (revenue) and donut charts (stock distribution) with minimal grid lines and interactive tooltips.

Forms and Input Fields: Text fields use white background, light grey border, rounded corners, placeholder text, and relevant icons (envelope, lock). Dropdown menus include downward-facing arrow. Toggle switches use iOS-style design. Input validation highlights invalid entries with red borders and error messages.

3. Screen-by-Screen Interface Breakdown

Login and Registration Screen: Simple, centred layout with narrow form card against dark navy background. Input fields clearly arranged with labels. Demo account information displayed in grey box near bottom of login screen.

Dashboard Screen: Top section contains four metric cards in a four-column grid. Chart section divided into two areas (monthly revenue bar chart occupies two-thirds width, stock-by-category donut chart occupies one-third). Bottom section contains recent activity feed alongside grid of quick-action shortcuts.

Product and Stock Management Screens: Full-width content card for inventory-related information. Search field and filter dropdowns above main table. Primary action buttons (add, export) positioned above table, typically on right-hand side. Table provides structured view with action icons for individual entries.

Sales Reports Screen: Top section contains date-range controls and dropdown menus for filtering by store or payment method. Middle section displays three metric cards and separate row for payment method breakdown. Bottom section contains detailed transaction table.

Settings Screen: Two-column layout. Sidebar on left displays settings categories (Profile, Notifications, Security, System, User Management). Corresponding form, configuration options, or user table appears in main panel on right.

Informal Mart (Mobile App) UI Design System
1. Design System and Theme

Primary Colour: Dark Green (#1B5E20) - Top app bars, main action buttons, selected navigation items, positive financial information.

Secondary Colour: White (#FFFFFF) - Screen backgrounds and cards.

Alert Colour: Red (#D32F2F) - Low-stock warnings, stock quantities needing attention, logout option.

Text Colours: Dark grey/black for important information. Light grey for secondary text, labels, placeholder content.

Typography: Sans-serif font (Roboto or standard Android system font). Bold text for headings and numerical values.

Icons: Simple, flat icons following Android Material-style approach.

2. Common User Interface Elements

Bottom Navigation Bar: Appears across main sections with five icons (Home, Stock, Sales, Reports, Settings). Active section highlighted with light-green, pill-shaped background behind icon. Selected icon and label appear in dark green.

Top App Bar: Dark-green background with white text. Back arrow appears on screens needing navigation back. Screen title positioned on left in bold text. Action icons on right may include notification bell, profile icon, plus icon, or filter icon.

3. Screen-by-Screen UI Breakdown

Registration Screen: Simple, centred layout. Top app bar displays back arrow and title "Register." Store icon near top centre. Three rounded text fields (full name, email, password) with light-grey borders and placeholder text. Large, full-width dark-green "Create Account" button below input fields.

Home (Dashboard) Screen: Top app bar displays app icon and name "Informal Mart" on left, notification bell with red dot and profile avatar on right. "DASHBOARD" heading in bold, dark-green text below top bar. Three circular indicators (Total Stock, Low Stock Alerts, Today's Sales). Two-column by two-row grid of four white cards with subtle shadows (View Stock, Add Stock, View Sales, View Alerts). "LOW STOCK ALERTS" section with "View All" link and products needing attention (red warning icon, product name, red stock indicator).

Stock (Inventory) Screen: Top app bar with back arrow, title "Inventory," and plus icon on right. Products arranged in two-column grid, each in separate white card with subtle shadows. Each product card contains product image/placeholder icon, product name in light-grey text, selling price in dark-green text, and available stock quantity. Stock quantities below required level displayed in red.

Sales (Recent Transactions) Screen: Top app bar with back arrow and title "Recent Transactions." Vertical list layout with faint horizontal lines separating entries. Each transaction contains product name (left, bold, light-grey), quantity sold (below product name, darker text), total amount (right, bold, dark-green).

Reports (Sales Report) Screen: Top app bar with back arrow, title "Sales Report," and filter icon on right. Prominent, pill-shaped date field with calendar icon. Three outlined summary cards (Total Sales, Transactions, Average Sale) with values in bold, dark-green text. Sales data table with light-grey header row (Product, Qty, Unit Price, Total). Full-width dark-green "Export PDF" button with PDF icon at bottom.

Settings Screen: Top app bar with back arrow and title "Settings." Vertical list of options, each with grey icon on left, text label, and right-facing chevron. Options include Profile Settings (user icon), Notification Preferences (bell icon), Store Information (store icon), and Language (settings-style icon). Red "Logout" label at bottom centre.

📸 Screenshots
BISOS Web App Screenshots
Registration and Login Page
https://screenshots/bisos-login.png

Dashboard Page
https://screenshots/bisos-dashboard.png

Stock Levels Page
https://screenshots/bisos-stocks.png

Product Management Page
https://screenshots/bisos-products.png

Reports Page
https://screenshots/bisos-reports.png

Alerts Page
https://screenshots/bisos-alerts.png

Stores Page
https://screenshots/bisos-stores.png

Settings Page
https://screenshots/bisos-settings.png

Informal Mart Mobile App Screenshots
Register Page
https://screenshots/mobile-register.png

Dashboard Page
https://screenshots/mobile-dashboard.png

Sales Report Page
https://screenshots/mobile-sales-report.png

Inventory Page
https://screenshots/mobile-inventory.png

Transaction Page
https://screenshots/mobile-transaction.png

Settings Page
https://screenshots/mobile-settings.png

Informal Mart Page
https://screenshots/mobile-informal-mart.png

📊 Reports
The system generates five key reports:

Daily Sales Report

📅 Daily sales summary by product

💰 Revenue breakdown with VAT

📈 Top-selling products

Low-Stock Alert Report

⚠️ Products below threshold

🔄 Recommended reorder quantities

💵 Estimated reorder costs

Inventory Valuation Report

💲 Current inventory value (cost & retail)

📊 Category-wise breakdown

🔄 Inventory turnover ratio

Supplier Performance Report

⭐ Supplier reliability metrics

📦 On-time delivery rates

💰 Total spend by supplier

Purchase Order History Report

📋 PO tracking by status

⏱️ Processing time analysis

💳 Total order values

📅 Project Timeline
Milestone	Duration	Week
Project Planning	1 week	Week 1
Requirements Analysis	1 week	Week 2-3
System Design	2 weeks	Week 4-5
Prototype Development	2 weeks	Week 6-7
User Acceptance Testing	1 week	Week 8
Final Submission	1 week	Week 8
💻 Installation & Setup
Prerequisites
XAMPP (or similar local server environment)

MySQL 8.0+

Web Browser (Chrome, Firefox, Edge, etc.)

Text Editor (VS Code recommended)

Installation Steps
Clone the Repository

bash
git clone https://github.com/yourusername/xisd6319-inventory-system.git
Set Up Local Server

bash
# Start XAMPP services
sudo /opt/lampp/lampp start
Import Database

bash
# Navigate to phpMyAdmin or run SQL script
mysql -u root -p < database/schema.sql
Configure Database Connection

php
// config/database.php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'inventory_system');
Start Development Server

bash
# If using PHP built-in server
php -S localhost:8000 -t public/
Access the Application

text
http://localhost:8000
🔒 Security Features
Password Hashing: bcrypt or Argon2 for secure password storage

Session Management: Auto-logout after 30 minutes inactivity

Input Validation: Client-side and server-side validation

SQL Injection Prevention: Prepared statements and parameterized queries

XSS Protection: Output sanitization

CSRF Protection: Token-based validation for forms

Role-Based Access Control: Granular permissions system

Audit Logging: Comprehensive action tracking

📚 Documentation
📄 Project Plan Document

📄 Requirements Analysis

📄 System Design Document

📄 Database Schema

📘 API Reference

📗 Database Guide

📕 User Manual

📊 Budget Summary
Project Phase	Hours	Cost (R)
Planning	30	R4,500
Analysis	40	R6,000
Design	45	R6,750
Prototype Development	35	R5,250
Subtotal	150	R22,500
Contingency (5%)	-	R1,125
Total	150	R23,625
📝 License
This project is developed for academic purposes as part of the XISD6319 course at Rosebank College.

🙏 Acknowledgments
Rosebank College for providing the academic framework

Supermarket staff for their valuable input during requirements gathering

All team members for their dedication and contributions
