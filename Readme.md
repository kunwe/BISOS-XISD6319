# XISD6319 - Branch Inventory & Sales Optimization System

## Project Overview

The **Branch Inventory & Sales Optimization System** is a comprehensive inventory management solution designed specifically for informal supermarket stores in South African townships. This interactive web-based prototype addresses critical inventory management challenges including real-time stock visibility, automated low-stock alerts, sales reporting, and supplier management.

---

## 📋 Table of Contents

- [Project Overview](#project-overview)
- [Team Members](#team-members)
- [Project Objectives](#project-objectives)
- [Key Features](#key-features)
- [Technology Stack](#technology-stack)
- [System Architecture](#system-architecture)
- [Database Schema](#database-schema)
- [Reports](#reports)
- [Project Timeline](#project-timeline)
- [Installation & Setup](#installation--setup)
- [Documentation](#documentation)
- [Contact Information](#contact-information)

---

## 👥 Team Members

| **Role** | **Name** | **Student Number** |
|----------|----------|-------------------|
| Group Leader & Front-End Developer | Kun'we Tyrone Mdaka | ST10262122 |
| Project Manager | Oratile Maungwa | ST10443081 |
| Analyst & Documentation Composer | Gontse Rakosa | ST10449265 |
| Back-End Developer | Richard Sebola | ST10441486 |

---

## 🎯 Project Objectives

1. **Real-time Stock Management**
   - Enable employees to view current stock levels instantly
   - Track stock movements and adjustments

2. **Automated Low-Stock Alerts**
   - Generate real-time notifications when stock falls below threshold
   - Suggest reorder quantities automatically

3. **Sales Reporting**
   - Generate daily, weekly, and monthly sales reports
   - Provide sales analytics and trends

4. **Supplier Management**
   - Track supplier information and performance
   - Manage purchase orders efficiently

5. **Inventory Valuation**
   - Calculate current inventory value at cost and retail
   - Track inventory turnover rates

6. **Audit Trail**
   - Log all critical system actions
   - Provide accountability and traceability

---

## ✨ Key Features

### Inventory Management
- ✅ Real-time stock visibility
- ✅ Stock adjustments (damaged, expired, lost)
- ✅ Product categorization
- ✅ Bulk product management
- ✅ Expiry date tracking

### Alert System
- ✅ Low-stock notifications
- ✅ Critical stock alerts
- ✅ Expiry date reminders
- ✅ Auto-generated reorder suggestions

### Sales & Reporting
- ✅ Daily sales reports (PDF/Excel)
- ✅ Low-stock alert reports
- ✅ Inventory valuation reports
- ✅ Supplier performance reports
- ✅ Purchase order history reports

### Supplier Management
- ✅ Supplier database
- ✅ Contact information management
- ✅ Performance rating system
- ✅ Payment terms tracking

### Purchase Orders
- ✅ Automated PO generation
- ✅ PO approval workflow
- ✅ Delivery tracking
- ✅ Receiving confirmation

### Security
- ✅ Role-based access control
- ✅ Secure password hashing
- ✅ Session management
- ✅ Audit logging

---

## 🛠️ Technology Stack

### Frontend
- **HTML5** - Structure and content
- **CSS3** - Styling and responsive design
- **JavaScript** - Client-side interactivity
- **Figma** - UI/UX design and wireframing

### Backend
- **PHP** / **Node.js** - Server-side logic
- **MySQL** - Database management
- **XAMPP** - Local development server

### Development Tools
- **Visual Studio Code** - Code editor
- **Draw.io** - ERD and architecture diagrams
- **MySQL Workbench** - Database design
- **Git/GitHub** - Version control

---

## 🏗️ System Architecture

The system follows a **three-tier architecture**:

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
│  │   (PHP/Node.js)                             │    │
│  └─────────────────────────────────────────────┘    │
├─────────────────────────────────────────────────────┤
│                DATA TIER (Database)                  │
│  ┌─────────────────────────────────────────────┐    │
│  │   MySQL Database (MySQL 8.0)                │    │
│  └─────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────┘
```

---

## 🗄️ Database Schema

### Core Tables

| **Table** | **Description** |
|-----------|-----------------|
| **Product** | Product information including SKU, pricing, and supplier reference |
| **Stock** | Current inventory levels by product and location |
| **Category** | Product categorization with hierarchical support |
| **Supplier** | Supplier details and performance metrics |
| **Sales Transaction** | Daily sales records with transaction details |
| **Low Stock Alert** | Auto-generated low stock notifications |
| **Purchase Order** | Purchase order header information |
| **Purchase Order Item** | Line items for each purchase order |
| **User** | System user accounts and credentials |
| **Role** | User roles with permission definitions |
| **Report Log** | Audit trail of generated reports |
| **Audit Log** | Comprehensive action logging |

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
```

---

## 📊 Reports

The system generates five key reports:

### 1. Daily Sales Report
- 📅 Daily sales summary by product
- 💰 Revenue breakdown with VAT
- 📈 Top-selling products

### 2. Low-Stock Alert Report
- ⚠️ Products below threshold
- 🔄 Recommended reorder quantities
- 💵 Estimated reorder costs

### 3. Inventory Valuation Report
- 💲 Current inventory value (cost & retail)
- 📊 Category-wise breakdown
- 🔄 Inventory turnover ratio

### 4. Supplier Performance Report
- ⭐ Supplier reliability metrics
- 📦 On-time delivery rates
- 💰 Total spend by supplier

### 5. Purchase Order History Report
- 📋 PO tracking by status
- ⏱️ Processing time analysis
- 💳 Total order values

---

## 📅 Project Timeline

| **Milestone** | **Duration** | **Week** |
|---------------|--------------|----------|
| Project Planning | 1 week | Week 1 |
| Requirements Analysis | 1 week | Week 2-3 |
| System Design | 2 weeks | Week 4-5 |
| Prototype Development | 2 weeks | Week 6-7 |
| User Acceptance Testing | 1 week | Week 8 |
| Final Submission | 1 week | Week 8 |

---

## 💻 Installation & Setup

### Prerequisites
- **XAMPP** (or similar local server environment)
- **MySQL 8.0+**
- **Web Browser** (Chrome, Firefox, Edge, etc.)
- **Text Editor** (VS Code recommended)

### Installation Steps

1. **Clone the Repository**
   ```bash
   git clone https://github.com/yourusername/xisd6319-inventory-system.git
   ```

2. **Set Up Local Server**
   ```bash
   # Start XAMPP services
   sudo /opt/lampp/lampp start
   ```

3. **Import Database**
   ```bash
   # Navigate to phpMyAdmin or run SQL script
   mysql -u root -p < database/schema.sql
   ```

4. **Configure Database Connection**
   ```php
   // config/database.php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'inventory_system');
   ```

5. **Start Development Server**
   ```bash
   # If using PHP built-in server
   php -S localhost:8000 -t public/
   ```

6. **Access the Application**
   ```bash
   http://localhost:8000
   ```

---

## 📚 Documentation

### Project Documentation
- 📄 [Project Plan Document](docs/XISD6319_PROJECT_PLAN.pdf)
- 📄 [Requirements Analysis](docs/REQUIREMENTS_ANALYSIS.pdf)
- 📄 [System Design Document](docs/SYSTEM_DESIGN.pdf)
- 📄 [Database Schema](docs/DATABASE_SCHEMA.pdf)

### Technical Documentation
- 📘 [API Reference](docs/API_REFERENCE.md)
- 📗 [Database Guide](docs/DATABASE_GUIDE.md)
- 📕 [User Manual](docs/USER_MANUAL.md)

---

## 🔒 Security Features

- **Password Hashing**: bcrypt or Argon2 for secure password storage
- **Session Management**: Auto-logout after 30 minutes inactivity
- **Input Validation**: Client-side and server-side validation
- **SQL Injection Prevention**: Prepared statements and parameterized queries
- **XSS Protection**: Output sanitization
- **CSRF Protection**: Token-based validation for forms
- **Role-Based Access Control**: Granular permissions system
- **Audit Logging**: Comprehensive action tracking

---

## 📊 Budget Summary

| **Project Phase** | **Hours** | **Cost (R)** |
|-------------------|-----------|--------------|
| Planning | 30 | R4,500 |
| Analysis | 40 | R6,000 |
| Design | 45 | R6,750 |
| Prototype Development | 35 | R5,250 |
| **Subtotal** | **150** | **R22,500** |
| Contingency (5%) | - | R1,125 |
| **Total** | **150** | **R23,625** |

---

## 📧 Contact Information

| **Team Member** | **Email** | **Role** |
|-----------------|-----------|----------|
| Kun'we Tyrone Mdaka | kunwemdaka@gmail.com | Group Leader & Front-End Developer |
| Oratile Maungwa | oratilemaungwa@gmail.com | Project Manager |
| Gontse Rakosa | gontsekobue2@gmail.com | Analyst & Documentation Composer |
| Richard Sebola | ernestsebola22@gmail.com | Back-End Developer |

---

## 📝 License

This project is developed for academic purposes as part of the XISD6319 course at Rosebank College.

---

## 🙏 Acknowledgments

- Rosebank College for providing the academic framework
- Supermarket staff for their valuable input during requirements gathering
- All team members for their dedication and contributions


## Website prototype 

![image alt](https://github.com/kunwe/BISOS-XISD6319/blob/83ad0ed89d8a8128ec9d49f76f4b7a5efca9857d/login.jpeg)
![image alt](https://github.com/kunwe/BISOS-XISD6319/blob/8d096cfbe4ff616dcd59113da35ba613853b36cb/Dashboard.jpeg)
![image alt](https://github.com/kunwe/BISOS-XISD6319/blob/8d096cfbe4ff616dcd59113da35ba613853b36cb/Daily%20Sales%20Report.jpeg)
![image alt](https://github.com/kunwe/BISOS-XISD6319/blob/8d096cfbe4ff616dcd59113da35ba613853b36cb/Inventory%20Valuation.jpeg)
![image alt](https://github.com/kunwe/BISOS-XISD6319/blob/8d096cfbe4ff616dcd59113da35ba613853b36cb/Low%20Stock%20Alerts.jpeg)
![image alt](https://github.com/kunwe/BISOS-XISD6319/blob/8d096cfbe4ff616dcd59113da35ba613853b36cb/Low%20Stock%20Report.jpeg)
![image alt](https://github.com/kunwe/BISOS-XISD6319/blob/8d096cfbe4ff616dcd59113da35ba613853b36cb/Products.jpeg)
![image alt](https://github.com/kunwe/BISOS-XISD6319/blob/8d096cfbe4ff616dcd59113da35ba613853b36cb/Thresholds.jpeg)
![image alt](https://github.com/kunwe/BISOS-XISD6319/blob/8d096cfbe4ff616dcd59113da35ba613853b36cb/Update%20Stock.jpeg)
![image alt](https://github.com/kunwe/BISOS-XISD6319/blob/8d096cfbe4ff616dcd59113da35ba613853b36cb/User%20Mange.jpeg)
![image alt](https://github.com/kunwe/BISOS-XISD6319/blob/8d096cfbe4ff616dcd59113da35ba613853b36cb/View%20Stock.jpeg)

![image alt]()
