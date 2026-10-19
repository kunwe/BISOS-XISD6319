BISOS & Informal Mart – Branch Inventory & Sales Optimization System
📌 Project Overview
This repository contains the Part 2 POE submission for XISD6329 and the XISD6319 project. It includes two connected solutions: BISOS, a web-based system, and Informal Mart, an Android mobile application. Both are intended to help informal supermarkets and small retailers manage their day-to-day work, especially when the business operates from more than one branch.

The applications help staff keep product information up to date, check stock levels, record sales, manage branches and suppliers, and control access according to each user's role.
👥 Team Members
Role	Name	Student Number	Email
Group Leader & Front-End Developer	Kun'we Tyrone Mdaka	ST10262122	kunwemdaka@gmail.com
Project Manager	Oratile Maungwa	ST10443081	oratilemaungwa@gmail.com
Analyst & Documentation Composer	Gontse Rakosa	ST10449265	gontsekobue2@gmail.com
Back-End Developer	Richard Sebola	ST10441486	ernestsebola22@gmail.com


🔗 Important Links
- Website (Web App): BISOS Web App
- Website Presentation: Watch Here
- Mobile App Presentation: Refer to the PDF; the link still needs to be added.
- Figma Design: Link to be added.
🎯 Project Objectives
- Up-to-date stock information: Let staff check current stock, follow stock movements, and record adjustments when needed.
- Automatic low-stock alerts: Notify users when an item drops below its set stock limit and recommend how much should be reordered.
- Sales reporting: Provide daily, weekly, and monthly sales summaries, including useful trends and figures.
- Supplier management: Keep supplier details organised, review supplier performance, and manage purchase orders.
- Inventory valuation: Estimate the value of stock using cost and selling prices, and monitor how quickly inventory moves.
- Audit trail: Keep a record of important actions so that activity can be traced and reviewed when necessary.
🛠️ Technology Stack
Web Application (BISOS)
- Front end: HTML5, CSS3, and vanilla JavaScript
- Design: Figma
- Back end: PHP
- Database: MySQL 8.0 or later
- Hosting: Microsoft Azure Web Apps
- Local development tools: XAMPP, Visual Studio Code, MySQL Workbench, and Draw.io
Mobile Application (Informal Mart)
- Platform: Native Android
- Programming language: Kotlin / Java
- Build system: Gradle
- Design approach: Android Material Design
🏗️ System Architecture
The system is organised into three main layers:
1. Presentation layer (client): The part users interact with through the web browser, built with HTML5, CSS3, and JavaScript.
2. Application layer (server): Processes requests and applies the system's business rules using the server-side code.
3. Data layer (database): Stores the information managed by the system in a MySQL 8.0 database.
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
The web application is divided into folders for API endpoints, configuration, styling, database scripts, shared page components, and JavaScript functionality.
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
The Android project contains the app manifest, source and test packages, resources, and Gradle configuration files. These are used to manage the app's screens, assets, build settings, and tests.
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
- Dashboard: Gives users a quick view of daily sales, product totals, low-stock warnings, monthly revenue, and stock levels by category.
- Product management: Allows users to add, edit, search, and export product records, including barcodes, cost prices, selling prices, and profit margins.
- Stock management: Shows inventory across different branches and uses colour-coded labels to identify critical, low, and normal stock levels.
- Low-stock alerts: Highlights products that need attention and allows users to acknowledge alerts, mark them as resolved, or start a reorder.
- Sales reports: Produces reports for daily, weekly, monthly, or custom periods, with options to export the results as PDF or CSV files.
- Store management: Helps users manage several branches and check each store's stock and contact information.
- Settings: Provides options for profile details, notification preferences, security, and system settings such as currency (ZAR) and time zone.
Mobile App (Informal Mart)
- Home / Dashboard: Summarises total stock, low-stock alerts, and sales for the current day.
- Quick actions: Gives users shortcuts to view stock, add stock, check sales, and open alerts.
- Inventory: Displays products in a grid and uses red indicators to draw attention to items running low.
- Recent transactions: Shows the latest recorded sales.
- Sales reports: Lets users select a date, review report totals, and export a report as a PDF.
- Settings: Includes profile details, notification options, store information, language preferences, and logout.
🗄️ Database Schema
Main Tables
Table	Purpose
Product	Stores product details, including SKU, pricing, and supplier reference.
Stock	Records the quantity of each product held at a particular location.
Category	Groups products into categories and supports a hierarchy.
Supplier	Stores supplier contact details and performance information.
Sales Transaction	Holds daily sales records and transaction details.
Low Stock Alert	Stores automatically generated notifications for products below their stock limit.
Purchase Order	Contains the main details for each purchase order.
Purchase Order Item	Lists the individual products included in a purchase order.
User	Stores user accounts and login details.
Role	Defines user roles and their permissions.
Report Log	Records reports that have been generated.
Audit Log	Keeps a record of important system actions.


Entity Relationship Diagram
The diagram below gives a simplified view of how the main inventory, product, supplier, sales, and purchasing entities relate to one another.
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
📖 User Documentation Summary
BISOS Web App User Guide
1. Introduction
The Branch Inventory and Sales Optimization System (BISOS) is a web application built to help informal supermarkets organise their daily operations. Users can manage product information, keep track of stock, review sales, oversee different branches, and manage access permissions for staff.
2. Getting Started
System requirements
Before using BISOS, make sure you have:
- A current version of a web browser, such as Chrome, Edge, Safari, or Firefox.
- A reliable internet connection.
- Valid login details for the system.
Logging in
1. Open the BISOS login page.
2. Enter the email address and password linked to your account.
3. If you want the system to remember your login, select Remember Me where available.
4. Click Sign In to continue.
5. If you only want to explore the system, use the Use Demo Account option if it is available.
Creating an account
1. On the login page, select Register Here.
2. Enter your first name, surname, email address, and phone number.
3. Choose your user role, such as Manager or Clerk.
4. Create a password with at least eight characters.
5. Read and accept the Terms of Service.
6. Select Create Account to submit your registration.
3. Finding Your Way Around BISOS
The top navigation bar provides access to the main sections:
- Dashboard: Check key business figures, stock information, and recent activity.
- Stocks: View stock quantities, update records, and identify products that need replenishing.
- Products: Add new products or search for and update existing product details.
- Reports: Review sales and financial information for a selected period.
- Alerts: Check warnings for products with low or critical stock levels.
- Stores: Manage and monitor the business's different branches.
- Settings: Update account details, preferences, notifications, and user accounts where permitted.
4. Working with the Main Modules
Dashboard
The dashboard provides an overview of the business, including the number of stores and products, low-stock warnings, critical items, and daily sales. It also includes charts for monthly revenue and stock by category, a recent-activity section, and shortcuts to common tasks.
Product Management
The product section displays information such as product name, category, barcode, cost price, selling price, profit margin, and whether an item is perishable. Depending on your access permissions, you can search or filter the list, add products, edit or delete records, and export product information.
Stock Management
This section shows the available quantity for each product and its reorder level. Colour indicators make it easier to identify stock that needs attention:
- Red – Critical: The quantity is at a critical level.
- Orange – Low: The quantity is low and may need replenishing.
- Green – OK: The quantity is at an acceptable level.
Users can add stock, change quantities or locations, and export stock information to CSV.
Low-Stock Alerts
The alerts section summarises pending alerts, critical products, and resolved alerts. You can filter the list by All, Pending, Acknowledged, or Resolved. Available actions include starting a reorder, acknowledging alerts, resolving them, and using bulk actions such as Acknowledge All, Resolve All, or Reorder Multiple.
Sales Reports
Choose a reporting period—daily, weekly, monthly, or a custom date range—and apply filters such as store or payment method. The report can show total sales, transaction count, average transaction value, and a breakdown by payment method. A detailed table lists the individual transactions. Reports can be exported as PDF or CSV files.
Store Management
The store section displays information about each branch, including its address, contact details, manager, product count, alerts, and daily sales. Depending on your permissions, you can view a branch's stock, edit its details, or add another store.
Settings
The settings section contains several areas:
- Profile: Update your name, email address, and phone number.
- Notifications: Choose available email or SMS alerts and daily summaries.
- Security: Change your password.
- System preferences: Review options such as currency and time zone.
- User management: Manage user roles and account status if your role allows it.
Informal Mart Mobile App User Guide
1. Introduction
Informal Mart is an Android app intended to make everyday store management more convenient. It helps store owners check stock, record and review sales, spot products that are running low, and prepare sales reports.
2. Getting Started
Creating an account
1. Open Informal Mart and go to the registration screen.
2. Enter your full name and a valid email address.
3. Create a password.
4. Tap Create Account to submit your details and continue to the dashboard.
3. Navigating the App
The bottom navigation bar contains five main sections:
- Home: View a summary of store activity.
- Stock: Browse and manage inventory.
- Sales: Review recent transactions.
- Reports: View sales summaries and create reports.
- Settings: Manage account information and app preferences.
4. Using the App's Features
Home (Dashboard)
The dashboard summarises the store's total stock, low-stock alerts, and sales for the current day. It also provides shortcuts to View Stock, Add Stock, View Sales, and View Alerts. Scroll down to see products that need restocking, or select View All to open the full list of low-stock items.
Stock (Inventory)
1. Tap Stock in the bottom navigation bar.
2. Browse the product cards to see each product's name, selling price, and available quantity.
3. Look for red indicators to identify products with low stock.
4. Tap the + icon to add a product or update inventory, depending on the options available in the app.
Sales
Tap Sales in the bottom navigation bar to view recent transactions. Each entry shows the product name, quantity sold, and total transaction amount.
Reports
1. Open Reports from the bottom navigation bar.
2. Use the date picker to choose the date you want to review.
3. Check the summary figures, including total sales, number of transactions, and average sale value.
4. Review the sales table for product names, quantities, unit prices, and totals.
5. Tap Export PDF to save or share the report using the options available on your device.
Settings
Open Settings from the bottom navigation bar. From there, you can:
- Open Profile Settings to view or update personal details.
- Use Notification Preferences to manage notifications.
- Update Store Information.
- Choose a preferred Language.
- Select Logout when you want to sign out.
🎨 UI Design Summaries
BISOS Web App UI Design System
1. Design System and Style Guide
Colour palette
- Primary brand – Deep Navy Blue (#1E293B): Used for headers, key text, and important interface elements.
- Secondary brand – Bright Blue (#3B82F6): Used for links, highlighted content, and the active navigation item.
- Success – Green (#10B981): Indicates successful actions and normal stock levels; also used for the CSV export button.
- Danger – Red (#EF4444): Highlights critical situations and destructive actions; also used for the PDF export button.
- Warning – Orange/Yellow (#F59E0B): Draws attention to low stock and other items that need action.
- Main background – Light Grey (#F3F4F6): Provides the page background.
- Card/panel background – White (#FFFFFF): Keeps cards and panels clear and easy to read.
- Primary text – Dark Slate (#1F2937): Used for the main content.
- Secondary text – Muted Grey (#6B7280): Used for supporting information and labels.
Typography
The interface uses a sans-serif font such as Inter, Roboto, or the system default. Headings are bold and dark navy, while body text uses a regular weight and dark grey. Table headings are smaller, muted, and usually uppercase.
Layout and grid structure
A fixed top navigation bar contains the logo, links to the main modules, a notification bell, and a profile avatar. The main content sits in a centred container with a maximum width and consistent spacing. Information is arranged in cards, tables, and grouped panels. The dark-navy footer includes copyright information, developer credits, and links to the Privacy Policy, Terms of Service, and Support.
2. User Interface Components
Navigation
The top navigation uses horizontal text links with icons. The selected section is shown with a solid blue background and white text. Sub-navigation options use rounded, pill-shaped controls, with the active option shown on a white background and a subtle shadow.
Buttons
Primary buttons use a solid navy or blue background, white text, and rounded corners of about 6 px. Secondary buttons have a white background, grey border, and dark text. Actions inside tables use small icon buttons. Export buttons include both an icon and a label, with red used for PDF and green for CSV.
Tables
Tables use horizontal lines to separate rows and light-grey column headers. A subtle hover effect helps users follow a row. Pagination controls appear at the bottom-right. Rounded status labels identify conditions such as perishable products and Low, OK, or Critical stock.
Cards and dashboard widgets
Metric cards have a white background, a light shadow, a short label, a large bold figure, and a trend indicator where relevant. Store cards include a coloured header or banner. Dashboard charts use bar charts for revenue and donut charts for stock distribution, with minimal grid lines and tooltips that display additional information.
Forms and input fields
Input fields have a white background, light-grey borders, rounded corners, placeholder text, and relevant icons such as an envelope or lock. Dropdowns include a downward arrow, while toggle switches follow an iOS-style design. Invalid entries are highlighted with a red border and an error message.
3. Screen-by-Screen Interface Breakdown
Login and registration
The login and registration screens use a simple, centred form card against a dark-navy background. Fields are arranged clearly and have visible labels. On the login screen, demo-account information appears in a grey box near the bottom.
Dashboard
Four metric cards appear across the top in a four-column layout. Below them, the chart area is split into two sections: a monthly revenue bar chart taking roughly two-thirds of the width and a stock-by-category donut chart taking the remaining third. The bottom area places the recent-activity feed beside a set of quick-action shortcuts.
Product and stock management
A full-width card contains the product or stock information. Search and filter controls sit above the main table, while actions such as Add and Export are usually placed above the table on the right. Each table row includes action icons for managing that record.
Sales reports
The top of the page contains date-range controls and dropdowns for filtering by store or payment method. The middle section displays three metric cards and a separate payment-method breakdown. A detailed transaction table appears at the bottom.
Settings
The settings screen uses two columns. A sidebar on the left lists categories such as Profile, Notifications, Security, System, and User Management. The selected category's form, settings, or user table appears in the main panel.
Informal Mart Mobile App UI Design System
1. Design System and Theme
Colour palette
- Primary – Dark Green (#1B5E20): Used for app bars, main action buttons, selected navigation items, and positive financial information.
- Secondary – White (#FFFFFF): Used for screen backgrounds and cards.
- Alert – Red (#D32F2F): Used for low-stock warnings, quantities that need attention, and the logout option.
- Text: Dark grey or black for important information, with light grey for labels, hints, and secondary details.
Typography and icons
The app uses Roboto or the standard Android system font. Headings and important numbers are bold to make them easier to spot. Icons are simple, flat, and consistent with Android Material Design.
2. Common Interface Elements
Bottom navigation bar
The bottom navigation bar appears throughout the app and contains five sections: Home, Stock, Sales, Reports, and Settings. The selected section is highlighted with a light-green, pill-shaped background. Its icon and label appear in dark green.
Top app bar
The top app bar has a dark-green background and white text. Screens that need it include a back arrow on the left, followed by the screen title in bold. Action icons on the right may include a notification bell, profile icon, plus sign, or filter icon.
3. Screen-by-Screen Interface Breakdown
Registration screen
The registration screen has a straightforward, centred layout. The top bar contains a back arrow and the title Register, with a store icon near the top centre. Three rounded fields collect the user's full name, email, and password. A large, full-width dark-green Create Account button appears below the fields.
Home (Dashboard) screen
The top bar displays the Informal Mart app icon and name on the left. A notification bell with a red dot and a profile avatar appear on the right. Below it is a bold, dark-green DASHBOARD heading and three circular indicators for Total Stock, Low Stock Alerts, and Today's Sales.

Four white shortcut cards are arranged in two columns and two rows: View Stock, Add Stock, View Sales, and View Alerts. A LOW STOCK ALERTS section appears below, with a View All link and a list of products that need attention. Each item includes a red warning icon and a stock indicator.
Stock (Inventory) screen
The top bar contains a back arrow, the title Inventory, and a plus icon. Products appear in a two-column grid of white cards with subtle shadows. Each card shows a product image or placeholder icon, the product name, selling price, and available quantity. Quantities below the required level are displayed in red.
Sales (Recent Transactions) screen
The top bar displays a back arrow and the title Recent Transactions. Sales are listed vertically, with faint horizontal lines separating entries. Each entry shows the product name in bold on the left, the quantity sold underneath, and the total amount in bold dark green on the right.
Reports (Sales Report) screen
The top bar contains a back arrow, the title Sales Report, and a filter icon. A prominent rounded date field includes a calendar icon. Three outlined cards show Total Sales, Transactions, and Average Sale, with their values in bold dark green. A sales table below has columns for Product, Qty, Unit Price, and Total. A full-width dark-green Export PDF button with a PDF icon appears at the bottom.
Settings screen
The top bar contains a back arrow and the title Settings. Options are listed vertically, each with a grey icon on the left, a label, and a right-facing chevron. The options include Profile Settings, Notification Preferences, Store Information, and Language. A red Logout label appears at the bottom centre.
📸 Screenshots
BISOS Web App Screenshots
- Registration and Login Page: https://screenshots/bisos-login.png
- Dashboard Page: https://screenshots/bisos-dashboard.png
- Stock Levels Page: https://screenshots/bisos-stocks.png
- Product Management Page: https://screenshots/bisos-products.png
- Reports Page: https://screenshots/bisos-reports.png
- Alerts Page: https://screenshots/bisos-alerts.png
- Stores Page: https://screenshots/bisos-stores.png
- Settings Page: https://screenshots/bisos-settings.png
Informal Mart Mobile App Screenshots
- Register Page: https://screenshots/mobile-register.png
- Dashboard Page: https://screenshots/mobile-dashboard.png
- Sales Report Page: https://screenshots/mobile-sales-report.png
- Inventory Page: https://screenshots/mobile-inventory.png
- Transaction Page: https://screenshots/mobile-transaction.png
- Settings Page: https://screenshots/mobile-settings.png
- Informal Mart Page: https://screenshots/mobile-informal-mart.png
📊 Reports
The system is intended to provide five main types of reports:
1. Daily Sales Report
- Daily sales totals grouped by product.
- Revenue breakdown, including VAT.
- A list of the best-selling products.
2. Low-Stock Alert Report
- Products with quantities below their set limits.
- Suggested quantities to reorder.
- Estimated costs for the suggested reorders.
3. Inventory Valuation Report
- Current stock value based on cost and retail prices.
- A breakdown of inventory by category.
- Inventory turnover ratio.
4. Supplier Performance Report
- Measures of supplier reliability.
- On-time delivery rates.
- Total spending by supplier.
5. Purchase Order History Report
- Purchase orders grouped or tracked by status.
- Analysis of processing times.
- Total values of purchase orders.
📅 Project Timeline
Milestone	Duration	Week
Project Planning	1 week	Week 1
Requirements Analysis	1 week	Weeks 2–3
System Design	2 weeks	Weeks 4–5
Prototype Development	2 weeks	Weeks 6–7
User Acceptance Testing	1 week	Week 8
Final Submission	1 week	Week 8


💻 Installation and Setup
Prerequisites
Before setting up the project locally, install or prepare the following:
- XAMPP or a similar local server environment.
- MySQL 8.0 or later.
- A modern browser, such as Chrome, Firefox, or Edge.
- A code editor; Visual Studio Code is recommended.
Installation steps
1. Clone the repository
git clone https://github.com/yourusername/xisd6319-inventory-system.git
2. Start the local server
Start the required services in XAMPP. On a Linux installation using the path shown below, you can run:
sudo /opt/lampp/lampp start
3. Import the database
Use phpMyAdmin to import the database schema, or run the SQL script from the command line:
mysql -u root -p < database/schema.sql
4. Configure the database connection
Update the database settings to match your local setup. The original example uses these values:
// config/database.php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'inventory_system');
5. Start the development server
If you are using PHP's built-in server, run:
php -S localhost:8000 -t public/
6. Open the application
In your browser, visit:
http://localhost:8000
🔒 Security Features
The project identifies several security measures to help protect accounts and business data:
- Password hashing: Use bcrypt or Argon2 to store passwords securely.
- Session management: Automatically sign users out after 30 minutes of inactivity.
- Input validation: Validate submitted information on both the client and server sides.
- SQL injection prevention: Use prepared statements and parameterised queries.
- Cross-site scripting (XSS) protection: Sanitize output before displaying it.
- Cross-site request forgery (CSRF) protection: Use token-based validation for forms.
- Role-based access control: Limit system functions according to each user's assigned permissions.
- Audit logging: Record important actions so activity can be reviewed later.
📚 Documentation
The project documentation includes:
- Project Plan Document
- Requirements Analysis
- System Design Document
- Database Schema
- API Reference
- Database Guide
- User Manual
📊 Budget Summary
Project Phase	Hours	Cost (R)
Planning	30	R4,500
Analysis	40	R6,000
Design	45	R6,750
Prototype Development	35	R5,250
Subtotal	150	R22,500
Contingency (5%)	—	R1,125
Total	150	R23,625


📝 License
This project was created for academic purposes as part of the XISD6319 course at Rosebank College.
🙏 Acknowledgements
- Rosebank College, for providing the academic framework for the project.
- Supermarket staff, for sharing useful input during the requirements-gathering stage.
- All team members, for the time, effort, and contributions they brought to the project.
