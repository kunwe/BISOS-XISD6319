# BISOS & Informal Mart 🏪📱
### Branch Inventory & Sales Optimization System

**Part 2 POE Submission | XISD6329 & XISD6319 | Rosebank College | 2026**

---

## 📌 Project Overview

BISOS (Branch Inventory and Sales Optimization System) and Informal Mart are two connected solutions developed to help informal supermarkets and small retailers manage their daily business operations.

The project consists of a PHP-based web application and an Android mobile application. Both systems communicate through a PHP backend API and MySQL database, allowing users to manage products, monitor stock, record sales, generate reports, and manage multiple store branches.

### Project Components

| Solution | Platform | Description |
|:---|:---|:---|
| **BISOS** | Web Application | Web-based inventory and sales management system. |
| **Informal Mart** | Android Application | Mobile application for managing store operations. |

### Project Information

| Item | Details |
|:---|:---|
| Website URL | `http://localhost/bisos-php` |
| API Endpoint | `http://localhost/bisos-php/api` |
| Android Package | `com.example.informalsupermarket` |
| Database | MySQL (`bisos_db`) |
| Backend | PHP |
| Institution | Rosebank College |
| Modules | XISD6329 & XISD6319 |

---

## 📑 Table of Contents

- [1. BISOS – Web Application](#1-bisos--web-application)
  - [1.1 Website Features](#11-website-features)
  - [1.2 Technology Stack](#12-website-technology-stack)
  - [1.3 Project Structure](#13-website-project-structure)
  - [1.4 Requirements](#14-website-requirements)
  - [1.5 Installation and Setup](#15-website-installation-and-setup)
  - [1.6 Website Deployment](#16-website-deployment)
- [2. Informal Mart – Android Application](#2-informal-mart--android-application)
  - [2.1 Application Features](#21-android-application-features)
  - [2.2 Technology Stack](#22-android-technology-stack)
- [3. Website and Application Integration](#3-website-and-application-integration)
  - [3.1 System Architecture](#31-system-architecture)
  - [3.2 API Endpoints](#32-api-endpoints)
  - [3.3 Sales and Stock Synchronisation](#33-sales-and-stock-synchronisation)
  - [3.4 Multi-Store Management](#34-multi-store-management)
- [4. Database Structure](#4-database-structure)
- [5. Running Both Systems](#5-running-both-systems)
- [6. Additional Information](#6-additional-information)
- [7. Project Contributors](#7-project-contributors)

---

# 🌐 1. BISOS – WEB APPLICATION

## 1.1 Website Overview

BISOS is a web-based inventory and sales management system developed for informal supermarkets and small retailers. It provides a central platform where store owners and employees can manage business activities, monitor stock levels, process sales, and access business reports.

The system also supports multiple store branches, allowing businesses to manage their operations from one platform.

## 1.2 Website Features

| Feature | Description |
|:---|:---|
| 🔐 User Authentication | User registration, login, logout, and password reset. |
| 📊 Dashboard | Displays an overview of important business information. |
| 📦 Product Management | Add, view, update, and delete products. |
| 📋 Stock Management | Monitor and update inventory quantities. |
| ⚠️ Low-Stock Alerts | Identify products that need restocking. |
| 💰 Sales Management | Record and manage sales transactions. |
| 📈 Sales Reports | View sales information and business performance. |
| 🏪 Multi-Store Management | Manage multiple supermarket branches. |
| ⚙️ Settings | Manage application and user settings. |
| 🔗 JSON API | Allows communication with the Android application. |

## 1.3 Website Technology Stack

| Technology | Purpose |
|:---|:---|
| PHP 8.2+ | Server-side development |
| MySQL / MariaDB | Database management |
| HTML | Website structure |
| CSS | Website styling |
| JavaScript | Client-side functionality |
| GitHub Actions | Automated deployment |
| Azure Web App | Website hosting |

## 1.4 Website Project Structure

```text
bisos-php/
│
├── api.php
├── index.php
├── login.php
├── register.php
├── forgot-password.php
├── logout.php
│
├── dashboard.php
├── products.php
├── stocks.php
├── low-stock-alerts.php
├── sales.php
├── sales-report.php
├── store-management.php
├── settings.php
│
├── config/
│   ├── bootstrap.php
│   ├── config.php
│   └── db.php
│
├── includes/
├── css/
├── js/
│
├── database/
│   ├── schema.sql
│   └── seed.sql
│
└── deploy.yml
```

## 1.5 Website Requirements

Before running the website, ensure that the following software is installed:

- PHP 8.x (PHP 8.2 or later recommended).
- MySQL or MariaDB.
- Apache or another PHP-supported web server.
- WAMP or XAMPP.
- A modern web browser.

## 1.6 Website Installation and Setup

**Step 1: Set up the project**

Clone or copy the repository into your web server's document root.

Example using WAMP:

```text
C:\wamp64\www\bisos-php
```

**Step 2: Start the services**

Open WAMP or XAMPP and start Apache and MySQL.

**Step 3: Create the database**

Create a MySQL database for the BISOS application.

**Step 4: Import the database files**

Import the following files using phpMyAdmin:

```text
database/schema.sql
database/seed.sql
```

Alternatively, use the MySQL command line:

```bash
mysql -u <user> -p <database_name> < database/schema.sql

mysql -u <user> -p <database_name> < database/seed.sql
```

The seed file is optional and contains sample data.

**Step 5: Configure the database**

Update the database connection settings in:

```text
config/db.php
config/config.php
```

Ensure that the database credentials match your local MySQL configuration.

**Step 6: Open the website**

Visit the following address in your browser:

```text
http://localhost/bisos-php/
```

Register an account or log in to access the dashboard.

## 1.7 Website Deployment – Azure

The project includes a GitHub Actions workflow (`deploy.yml`) for deploying the BISOS website to Azure Web App.

The workflow runs when changes are pushed to the `main` branch.

The deployment process includes:

1. Checking out the latest repository code.
2. Setting up PHP 8.2.
3. Validating PHP syntax using `php -l`.
4. Deploying the application using `azure/webapps-deploy`.

**Required GitHub Secret:**

```text
AZURE_WEBAPP_PUBLISH_PROFILE
```

To enable deployment:

- Obtain the publish profile from your Azure Web App.
- Add it to your GitHub repository secrets.
- Ensure the application name in `deploy.yml` matches your Azure Web App.

---

# 📱 2. ANDROID APPLICATION

## 2.1 Application Overview

Informal Mart is the Android mobile application connected to the BISOS web system. It allows store owners and employees to manage their supermarket operations directly from their mobile devices.

The application provides tools for managing inventory, recording sales, monitoring low-stock products, and viewing sales reports.

It also supports multiple store branches, user authentication, profile management, and multiple languages.

## 2.2 Android Application Features

### 🔐 2.2.1 Authentication and Registration

- Firebase authentication using email and password.
- Google Sign-In integration.
- User registration and login.
- Automatic registration redirection for unregistered Google accounts.
- User session management across Firebase, local storage, and MySQL.

### 🏪 2.2.2 Multi-Store Management

The application supports multiple supermarket branches.

Features include:

- Creating new store branches.
- Joining existing stores during registration.
- Switching between active store branches.
- Managing store names, addresses, and contact numbers.
- Separating products, stock, alerts, sales, and reports by store.

Example branches:

- Soweto Supermarket
- Sandton Branch
- Pretoria Branch

### 📦 2.2.3 Stock and Inventory Management

Users can manage their inventory directly from the application.

Features include:

- Viewing available products.
- Adding and editing product information.
- Updating product prices and quantities.
- Setting reorder thresholds.
- Searching products by name or barcode.
- Scanning product barcodes.
- Viewing product images.

Tapping a stock card opens a product editing dialog where users can update the product name, selling price, stock quantity, reorder threshold, and barcode.

### 🤖 2.2.4 AI-Powered Product Image Generator

The application integrates Pollinations.ai to generate product images based on product names.

Available options include:

- Regenerating AI-generated product images.
- Taking or uploading product photos.
- Removing existing images.
- Returning to the default store icon.

The image generator also recognises product measurements such as kg, L, ml, mg, g, and pack.

### 💰 2.2.5 Sales Management

Users can record sales transactions directly from the application.

When a sale is recorded:

- A new transaction is created.
- A unique transaction reference is generated (`TXN-xxxx`).
- Sold quantities are deducted from stock.
- Sales information is updated for reporting.

### 📊 2.2.6 Sales Reports and Transaction Voiding

Sales reports are automatically updated when transactions are recorded.

Reports include:

- Total sales.
- Number of transactions.
- Average sale value.
- Cash payments.
- Card payments.
- Mobile payments.

Users can also void transactions. When a transaction is voided, the system restores the affected stock quantities and recalculates the sales report totals.

### ⚠️ 2.2.7 Low-Stock Alerts

The application monitors stock quantities and identifies products that fall below their reorder levels.

Users can view low-stock products and take the necessary steps to replenish their inventory.

### 👤 2.2.8 Profile Picture Management

Users can personalise their accounts through the Settings screen.

Available options include:

- Taking or uploading a profile picture.
- Selecting an AI-generated avatar.
- Removing an existing profile picture.

Profile picture preferences are stored locally using SharedPreferences.

### 🌍 2.2.9 Multi-Language Support

Informal Mart supports four languages.

| Language | Resource Directory |
|:---|:---|
| English | `res/values` |
| Afrikaans | `res/values-af` |
| isiZulu | `res/values-zu` |
| Sesotho | `res/values-st` |

Changing the language in Settings updates the application interface to the selected language.

## 2.3 Android Technology Stack

| Technology | Purpose |
|:---|:---|
| Kotlin | Android application development |
| Android Studio | Development environment |
| Firebase Authentication | User authentication |
| Firebase BoM 33.9.0 | Firebase dependency management |
| Firebase Auth KTX | Authentication services |
| Google Services Plugin 4.4.2 | Firebase configuration |
| SharedPreferences | Local preference storage |
| PHP | Backend API |
| MySQL | Online database management |
| Pollinations.ai | AI product image generation |

### Application Information

| Item | Details |
|:---|:---|
| Application Name | Informal Mart (BISOS Mobile) |
| Package Name | `com.example.informalsupermarket` |
| Platform | Android |
| Programming Language | Kotlin |
| Development Environment | Android Studio |

---

# 🔗 3. WEBSITE AND APPLICATION INTEGRATION

## 3.1 System Integration Overview

BISOS and Informal Mart communicate through a PHP backend API that connects the Android application to the MySQL database.

The website provides the main web-based management platform, while Informal Mart provides mobile access to important store operations.

## 3.2 System Architecture

```text
             ┌───────────────────────┐
             │   BISOS WEB SYSTEM    │
             │       (PHP)           │
             └───────────┬───────────┘
                         │
                         ▼
             ┌───────────────────────┐
             │    PHP BACKEND API    │
             └───────────┬───────────┘
                         │
                         ▼
             ┌───────────────────────┐
             │    MySQL DATABASE     │
             │      bisos_db         │
             └───────────┬───────────┘
                         │
                         ▼
             ┌───────────────────────┐
             │  INFORMAL MART APP    │
             │      (Kotlin)         │
             └───────────┬───────────┘
                         │
                         ▼
             ┌───────────────────────┐
             │   ANDROID USERS       │
             └───────────────────────┘
```

## 3.3 API Endpoints

The PHP backend API is available at:

```text
http://localhost/bisos-php/api
```

The following endpoints handle communication between the Android application and the backend.

| Method | Endpoint | Description |
|:---|:---|:---|
| GET | `/ping` | Checks whether the API is running. |
| POST | `/auth/login` | Authenticates users and returns a token and user information. |
| POST | `/auth/register` | Registers a new user account. |
| GET | `/stocks?store_id={id}` | Retrieves products and stock levels. |
| POST | `/products` | Adds a new product. |
| POST | `/products/update` | Updates product information. |
| POST | `/products/delete` | Deletes a product. |
| GET | `/alerts?store_id={id}` | Retrieves low-stock alerts. |
| POST | `/sales` | Records a sale and deducts stock. |
| POST | `/sales/void` | Voids a sale and restores stock. |
| GET | `/sales/report?store_id={id}` | Retrieves sales reports and payment totals. |

## 3.4 Sales and Stock Synchronisation

When a sale is recorded, the system:

1. Creates a new sales transaction.
2. Generates a transaction reference.
3. Deducts the sold quantities from available stock.
4. Updates sales totals.
5. Refreshes transaction counts and payment breakdowns.

When a transaction is voided, the system restores the stock and recalculates the relevant sales report information.

## 3.5 Multi-Store Management

The system separates store information using unique store identifiers.

Each branch maintains its own:

- Products.
- Stock quantities.
- Low-stock alerts.
- Sales transactions.
- Sales reports.

This ensures that information belonging to different branches is managed separately.

---

# 🗄️ 4. DATABASE STRUCTURE

## 4.1 Database Information

The project uses MySQL to store and manage information for the BISOS website and its connected Android application.

**Database Name:** `bisos_db`

## 4.2 Database Tables

| Table | Description |
|:---|:---|
| `Store` | Stores branch information, including name, address, and contact details. |
| `User` | Stores user accounts, assigned stores, and roles. |
| `Product` | Stores product information, prices, barcodes, and reorder levels. |
| `Stock` | Stores product quantities and stock limits. |
| `LowStockAlert` | Records products that require restocking. |
| `SalesTransaction` | Stores sales transactions and payment information. |
| `SalesItem` | Stores individual products included in each transaction. |

## 4.3 Database Relationships

The database uses store and product identifiers to connect information between tables.

- Each store can have multiple users.
- Each store can manage multiple products.
- Each product has associated stock information.
- Products can generate low-stock alerts.
- Each sales transaction can contain multiple sales items.
- Each sales item is linked to a product and transaction.

---

# ⚙️ 5. RUNNING BOTH SYSTEMS

Follow these steps to run the BISOS website and Informal Mart application together.

### Step 1: Start the Local Server

Open WAMP or XAMPP and start:

- Apache
- MySQL

### Step 2: Import the Database

Import the following files into MySQL:

```text
database/schema.sql
database/seed.sql
```

### Step 3: Configure the Website

Place the BISOS website inside:

```text
C:\wamp64\www\bisos-php\
```

Configure the database credentials in the relevant configuration files.

### Step 4: Open the Website

Visit:

```text
http://localhost/bisos-php
```

### Step 5: Configure the Android Application

Configure the application to communicate with the correct backend API address.

**For Android Emulator:**

```text
http://10.0.2.2/bisos-php/api
```

**For a Physical Android Device:**

Use your computer's local network IP address or an ngrok URL.

> **Important:** The `localhost` address on an Android device refers to the device itself, not your computer. Ensure that both devices can communicate over the network and that your firewall allows the connection.

### Step 6: Run the Application

1. Open the Android project in Android Studio.
2. Allow Gradle to sync.
3. Connect an Android device or start an emulator.
4. Run the application.

Both systems should now be able to communicate through the PHP API and MySQL database.

---

# 📝 6. ADDITIONAL INFORMATION

## 6.1 Development Notes

- The website and Android application are designed to work together.
- Both platforms use the PHP backend API for relevant shared data operations.
- Firebase Authentication is integrated into the Android application.
- MySQL stores the main business information.
- Store branches maintain separate business records.
- Database credentials and API configuration should be secured before production deployment.
- Local development URLs must be updated when deploying the system to a live server.
- The AI image generator requires an internet connection.

## 6.2 Future Improvements

Possible improvements include:

- More advanced business analytics and reporting.
- Improved offline functionality and automatic data synchronisation.
- Additional payment integrations.
- Enhanced user roles and permissions.
- More language options.
- Improved security and API access controls.

---

# 👥 7. PROJECT CONTRIBUTORS

The BISOS and Informal Mart project was developed by the following group members:

| No. | Group Member |
|:---:|:---|
| 1 | Mdaka K.T. |
| 2 | Maungwa O. |
| 3 | Rakosa G. |
| 4 | Sebola R. |

**Institution:** Rosebank College  
**Academic Year:** 2026  
**Modules:** XISD6329 & XISD6319  
**Submission:** Part 2 POE

---
Updated the Group attendance

<p align="center">

**BISOS & INFORMAL MART**

*Simplifying Inventory Management. Improving Business Operations.*

© 2026 BISOS & Informal Mart. All rights reserved.

</p>
