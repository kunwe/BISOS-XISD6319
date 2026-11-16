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

### 1. Multi-Store Database Isolation & Store Creation
- **Strict Per-Store Data Isolation**: Each store branch (*Soweto Supermarket*, *Sandton Branch*, *Pretoria Branch*, etc.) maintains its own isolated database state on disk (`store_products_{id}`, `store_sales_{id}`) and online for products, stock levels, low-stock alerts, sales, and reports.
- **Add New Store Branch in Settings**: Dedicated **`+ New Branch`** button in **Settings ➔ Store Information** opens an Add Store dialog (**Store Name**, **Address**, **Company Tel**), automatically creates a new isolated store database, and switches active management.
- **Multi-Store Registration**: *"Store Managing Details"* section on Register screen allows:
  - **Join Existing Store**: Dropdown selection of saved database stores.
  - **Create New Store**: Editable text fields creating a new store branch and isolated database upon registration.
- **Active Store Switcher**: Settings header displays the active store with an interactive dropdown switcher to change managed branches.

### 2. Real-Time Sales Report Synchronization & Voiding
- **Real-Time Report Updates**: Recording a new sale in `recordSale()` automatically deducts product stock, creates a new `SaleItem` with reference ID (`TXN-xxxx`), updates **Total Sales**, **Transaction Count**, **Average Sale**, and payment method totals (**Cash**, **Card**, **Mobile**), and saves the report to disk per `storeId`.
- **Void & Restore Stock**: Voiding a transaction in `voidSale()` restores stock quantities and recalculates report totals in real-time.

### 3. Google Sign-In with Mandatory Registration Enforcement
- **Registration Verification**: Tapping **"Sign in with Google"** or **"Sign up with Google"** verifies if the Google email address is registered.
- **Automatic Redirection**: If the Google account is NOT registered, the system alerts the user (*"Account is not registered yet. Redirecting to Register..."*) and automatically redirects them to the **Registration Screen (`RegisterScreen.kt`)** to complete store selection and registration.

### 4. Firebase Authentication Integration
- **Cloud Identity Management**: Configured **Firebase BoM** (`33.9.0`), **Firebase Auth** (`firebase-auth-ktx`), and **Google Services** plugin (`com.google.gms.google-services:4.4.2`).
- **`FirebaseAuthManager`**: Handles `signInWithEmailAndPassword` and `createUserWithEmailAndPassword`, keeping cloud user sessions synced across Firebase, local `AccountRepository`, and MySQL.

### 5. Profile Picture Management in Settings
- **Interactive Avatar**: User Avatar on the Settings screen header card opens a **Profile Photo Control** dialog.
- **Photo Options**:
  - `📷 Camera / Photo` (Capture or upload custom photo)
  - `✨ AI Avatar` (Select from avatars)
  - `🗑️ Remove Profile Photo` (Revert to default account icon)
- **Persistence**: Saved in `SharedPreferences` (`profile_prefs`) across app restarts.

### 6. Dynamic Full-App Localization (Multi-Language)
- **4 Languages Supported**: Complete string resources and translations for:
  - **English** (`res/values/strings.xml`)
  - **Afrikaans** (`res/values-af/strings.xml`)
  - **isiZulu** (`res/values-zu/strings.xml`)
  - **Sesotho** (`res/values-st/strings.xml`)
- **Instant UI Refresh**: Changing language in Settings immediately updates the app locale and translates all text elements across all screens.

### 7. Stock & Inventory AI Image Management
- **Clickable Stock Cards**: Tapping any inventory card opens a *"View & Edit Product"* modal dialog to modify Name, Selling Price, Stock Quantity, Threshold, and Barcode.
- **AI Stock Image Generator**: Dynamically generates real-time photorealistic product images based on product names using Pollinations.ai API with smart measurement parsing (`kg`, `L`, `ml`, `mg`, `g`, `pack`).
- **Image Controls**: `✨ Regenerate AI Image`, `📷 Replace Photo`, and `🗑️ Remove Image`.
- **Barcode Scanner & Search**: Barcode scan button and search bar filtering inventory by name or barcode (e.g., `6001234567890`).

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
| Cloud & Auth | Firebase Auth (`firebase-auth-ktx`), Google Services |
| Async & Image | Kotlin Coroutines, Flow, Coil |
| Network | HttpURLConnection, Gson |

---

## 📂 Project Structure

```
InformalSupermarket/
├── all_updates.txt                      # Integration documentation for website & app link
├── Readme.md                            # Comprehensive system documentation
├── app/
│   ├── google-services.json             # Firebase configuration
│   ├── src/
│   │   ├── main/
│   │   │   ├── AndroidManifest.xml
│   │   │   ├── java/com/yourcompany/informalsupermarket/
│   │   │   │   ├── data/                 # Account, Store, Product, Sales, PerStoreDatabase repositories
│   │   │   │   ├── network/              # ApiClient, ApiService, FirebaseAuthManager, ServerConfig
│   │   │   │   ├── ui/                   # InformalMartApp, InformalMartViewModel, screens, theme
│   │   │   │   └── util/                 # AiImageGenerator, LanguageManager
│   │   │   └── res/                      # Drawables, values (English, af, zu, st)
├── bisos-php/                           # PHP Backend API and Web Application
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
