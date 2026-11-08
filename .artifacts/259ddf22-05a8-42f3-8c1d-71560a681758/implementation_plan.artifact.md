# Implementation Plan - Functional Informal Mart App

I will transform the current UI prototype into a functional application by implementing state management, robust navigation, and all missing screens (Stock, Sales, Register, etc.). I will also integrate web images using Coil.

## User Review Required

> [!IMPORTANT]
> I will be using **Navigation Compose** for screen transitions.
> I will use a central **ViewModel** to manage mock data for Stock, Sales, and Alerts to ensure consistency across the app.
> Product images will be loaded from standard placeholder web services (e.g., Unsplash or Lorem Flickr) unless specific URLs are provided.

## Proposed Changes

### Dependencies
- Add `androidx.navigation:navigation-compose` for routing.
- Add `io.coil-kt:coil-compose` for web image loading.
- Add `androidx.lifecycle:lifecycle-viewmodel-compose`.

### Data & State
- Create `InformalMartViewModel` in `com.yourcompany.informalsupermarket.ui` to manage:
    - `productList`: List of `Product` objects.
    - `salesHistory`: List of `SalesReport` objects.
    - `lowStockAlerts`: List of `LowStockAlert` objects.
    - `currentUser`: Current logged-in user state.

### Navigation
- Update `InformalMartApp` to use `NavHost` with the following routes:
    - `Login`, `Register`, `Dashboard`, `Stock`, `AddStock`, `Sales`, `Reports`, `Settings`, `Alerts`.

### New Screens
- `StockScreen`: Displays a grid/list of products with web images, names, and stock levels.
- `AddStockScreen`: A form to enter product name, price, category, and initial stock.
- `SalesScreen`: A list of recent transactions.
- `RegisterScreen`: Form for new users (Name, Email, Password).
- `AlertsScreen`: A dedicated page for all low-stock warnings.

### UI Polishing
- Integrate `AsyncImage` from Coil in all product lists.
- Update `DashboardScreen` and `SalesReportScreen` to use real data from the ViewModel.
- Wire all buttons to `navController.navigate()`.

## Verification Plan

### Manual Verification
- Deploy to emulator/device.
- Verify Login -> Dashboard flow.
- Verify Dashboard -> Add Stock -> Stock List (check if new item appears).
- Verify Bottom Nav switching between Home, Stock, Sales, Reports, and Settings.
- Verify image loading from the web in the Stock list.
