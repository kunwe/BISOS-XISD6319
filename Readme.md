# BISOS & Informal Mart – Branch Inventory & Sales Optimization System

> **Part 2 POE Submission** for **XISD6329** and **XISD6319**  
> Rosebank College

---

## 📌 Project Overview

This repository contains two connected solutions:

| Solution | Platform | Description |
|----------|----------|-------------|
| **BISOS** | Web Application | Branch Inventory & Sales Optimization System (`http://localhost/bisos-php`) |
| **Informal Mart** | Android Mobile App | Mobile companion for store management (`com.example.informalsupermarket`) |

Both applications help informal supermarkets and small retailers manage their day-to-day operations across multiple branches. Staff can keep product information up to date, check stock levels, record sales, manage branches, and control access according to user roles both **online and offline**.

---

## 👥 Team Members

| Role | Name | Student Number | Email |
|------|------|----------------|-------|
| Group Leader & Front-End Developer | Kun'we Tyrone Mdaka | ST10262122 | kunwemdaka@gmail.com |
| Project Manager | Oratile Maungwa | ST10443081 | oratilemaungwa@gmail.com |
| Analyst & Documentation Composer | Gontse Rakosa | ST10449265 | gontsekobue2@gmail.com |
| Back-End Developer | Richard Sebola | ST10441486 | ernestsebola22@gmail.com |

---

## 🎯 Key Mobile & Web Features Added

### 1. Multi-Store Database Isolation & Registration
- **Strict Per-Store Data Isolation**: Each store branch (*Soweto Supermarket*, *Sandton Branch*, *Pretoria Branch*) maintains its own isolated database state for products, stock levels, low-stock alerts, sales, and reports.
- **Multi-Store Registration**: *"Store Managing Details"* section on Register screen allows:
  - **Join Existing Store**: Dropdown selection of saved database stores.
  - **Create New Store**: Editable text fields for **Store Name**, **Store Address**, and **Company Tel Number**.
- **Active Store Switcher**: Settings header displays the active store with an interactive dropdown switcher to change managed branches.
- **Edit Store Information Dialog**: Modal dialog in Settings pre-filled with Store Name, Address, and Contact Phone with database persistence.

### 2. Dual-Mode Account Database (Online & Offline Auto-Sync)
- **Account Repository**: Persistent account storage recording accounts created both online (MySQL `User` table) and offline (device `SharedPreferences`).
- **Offline-to-Online Auto-Sync Engine**: Accounts registered offline marked as pending automatically upload to MySQL when the network/ngrok reconnects.
- **Instant Offline Authentication**: Sign in offline feature using the local account database.

### 3. Stock & Inventory Management
- **Clickable Stock Cards**: Tapping any inventory card opens a *"View & Edit Product"* modal dialog to modify Name, Selling Price, Stock Quantity, Threshold, and Barcode.
- **AI Stock Image Generator**: Dynamically generates real-time photorealistic product images based on product names using Pollinations.ai.
- **Smart Measurement Parsing**: Intelligently detects and formats units (`kg`, `L`, `ml`, `mg`, `g`, `pack`) into package prompts (e.g. *2L bottle*, *1kg bag*, *700g loaf*).
- **Image Control Options**:
  - `✨ Regenerate AI Image` (Generates a new AI product photo with fresh seed)
  - `📷 Replace Photo` (Captures/uploads custom photo with camera)
  - `🗑️ Remove Image` (Reverts to clean default `ic_store` icon on soft blue background)
- **Barcode Scanner & Search**: TopBar barcode scan button and search bar filtering inventory by name or barcode (e.g., `6001234567890`).
- **Camera Item Photo Capture**: Camera photo capture launcher on Add Product Screen.

### 4. Real-Time Sales & Automatic Stock Deduction / Restoration
- **Record New Sale**: Select product, quantity, and payment method (`CASH`, `CARD`, `MOBILE`). Automatically deducts sold items from the MySQL `Stock` table in real-time.
- **Void / Delete Sale**: Voiding a transaction in Recent Transactions automatically restores sold item quantities back into available stock.
- **Delete Product**: Option to delete product from inventory with confirmation dialog.

### 5. Low Stock Alerts (< 50 Threshold)
- **Low Stock Threshold (< 50)**: Automatically flags any product with quantity less than 50 as Low Stock on Dashboard, Alert Dialog, and Stock Screen.

### 6. Sales Report Dashboard
- **Period Tabs**: Daily, Weekly, Monthly (Default), Custom Range.
- **Export Actions**: Export PDF (Red button `#D32F2F`) and Export CSV (Green button `#2E7D32`).
- **Filter Controls**: From Date & To Date pickers, Store selector, Payment Method selector.
- **Key Metrics Cards**: Total Sales, Transaction Count, Average Transaction.
- **Payment Method Breakdown Cards**: CASH, CARD, MOBILE totals with custom icons.
- **Detailed Transaction Table**: Columns for TXN ID, Date, Product, Qty, Unit Price, Total, Store, and Payment (color-coded pill badges for Cash, Card, Mobile).

### 7. App Theme & Localization
- **Official BISOS Branding**: Applied official dark navy (`#0D2C54`) and light blue (`#1E88E5`) brand emblem and full logo on Login, Register, and Launcher Icon.
- **True Light & Dark Theme**: Dynamic Light/Dark Mode toggle in Settings. In Dark Mode, screen background switches to `#12181F`, card surfaces to `#1E2733`, and all words/text switch to pure white (`#FFFFFF`).
- **Multi-Language Localization**: Dynamic language switching supporting:
  - **English** (Default)
  - **Afrikaans** (`res/values-af`)
  - **isiZulu** (`res/values-zu`)
  - **Sesotho** (`res/values-st`)

---

## 🛠️ Technology Stack

### Web Application (BISOS)

| Layer | Technology |
|-------|------------|
| Front End | HTML5, CSS3, Vanilla JavaScript |
| Back End | PHP |
| Database | MySQL 8.0+ (`bisos_db`) |
| Local Server | WAMP / XAMPP Server |

### Mobile Application (Informal Mart)

| Layer | Technology |
|-------|------------|
| Platform | Native Android |
| Language | Kotlin |
| UI Framework | Jetpack Compose (Material 3) |
| Async / Image | Kotlin Coroutines, Flow, Coil |
| Network | HttpURLConnection, Gson |

---

## 📂 Project Structure

```
InformalSupermarket/
├── all_updates.txt                      # Integration documentation for website & app link
├── app/
│   ├── src/
│   │   ├── main/
│   │   │   ├── AndroidManifest.xml
│   │   │   ├── java/com/yourcompany/informalsupermarket/
│   │   │   │   ├── data/                 # Account, Store, Product, Sales, Alerts models & DAOs
│   │   │   │   ├── network/              # ApiClient, ApiService, ServerConfig
│   │   │   │   ├── ui/                   # InformalMartApp, InformalMartViewModel, screens, theme
│   │   │   │   └── util/                 # AiImageGenerator, LanguageManager
│   │   │   └── res/                      # Drawables, values (English, af, zu, st)
├── bisos-php/                           # PHP Backend API and Web Application
│   ├── api/
│   │   ├── alerts.php
│   │   ├── auth.php
│   │   ├── db.php
│   │   ├── index.php
│   │   ├── sales.php
│   │   └── stocks.php
│   └── database/
│       ├── schema.sql
│       └── seed.sql
```

---

## 💻 How to Run and Setup

### Prerequisites
1. **WAMP Server** or **XAMPP** installed on your computer.
2. **MySQL 8.0+** running on port 3306.
3. **Android Studio** (2024.1+ / Ladybug or newer).

### Step-by-Step Setup:

1. **Start Local Server & Database**:
   - Start WAMP or XAMPP (Apache & MySQL).
   - Place `bisos-php` directory in `C:\wamp64\www\bisos-php\` (or `htdocs/bisos-php`).
   - Open phpMyAdmin or MySQL Workbench and run `bisos-php/database/schema.sql` and `bisos-php/database/seed.sql`.

2. **Web Portal Access**:
   - Access the web interface at: `http://localhost/bisos-php`
   - API endpoints are available at: `http://localhost/bisos-php/api/`

3. **Running the Android App**:
   - Open `InformalSupermarket` in Android Studio.
   - Run the app on an Android Emulator or physical device.
   - On the Login screen, tap **"Server settings"** to enter your server URL:
     - For Android Emulator: `http://10.0.2.2/BISOS-PHP/api`
     - For ngrok tunneling: `https://YOUR-NGROK-URL.ngrok-free.dev`
   - You can sign in online or tap **"Sign In Offline"** to test offline mode!

---

<div align="center">

**BISOS & Informal Mart** · XISD6329 / XISD6319 · Rosebank College

</div>
