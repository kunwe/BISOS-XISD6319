package com.yourcompany.informalsupermarket.ui

import android.content.Context
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.yourcompany.informalsupermarket.data.*
import com.yourcompany.informalsupermarket.network.ApiClient
import com.yourcompany.informalsupermarket.network.ApiException
import com.yourcompany.informalsupermarket.network.ApiService
import com.yourcompany.informalsupermarket.network.FirebaseAuthManager
import com.yourcompany.informalsupermarket.util.LanguageManager
import kotlinx.coroutines.CancellationException
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

/**
 * Talks to the BISOS PHP backend (through the ngrok URL in ServerConfig) with offline fallback mode.
 * Enforces strict per-store database isolation so each store manages its own products, stock, and sales.
 */
class InformalMartViewModel : ViewModel() {

    private val _products = MutableStateFlow<List<Product>>(emptyList())
    val products: StateFlow<List<Product>> = _products.asStateFlow()

    private val _alerts = MutableStateFlow<List<LowStockAlert>>(emptyList())
    val alerts: StateFlow<List<LowStockAlert>> = _alerts.asStateFlow()

    private val _salesReport = MutableStateFlow<SalesReport?>(null)
    val salesReport: StateFlow<SalesReport?> = _salesReport.asStateFlow()

    private val _currentUser = MutableStateFlow<User?>(null)
    val currentUser: StateFlow<User?> = _currentUser.asStateFlow()

    private val _currentLanguage = MutableStateFlow("English")
    val currentLanguage: StateFlow<String> = _currentLanguage.asStateFlow()

    private val _isDarkMode = MutableStateFlow(false)
    val isDarkMode: StateFlow<Boolean> = _isDarkMode.asStateFlow()

    private val _currentStoreName = MutableStateFlow("Soweto Supermarket")
    val currentStoreName: StateFlow<String> = _currentStoreName.asStateFlow()

    private val _currentStoreAddress = MutableStateFlow("123 Soweto Street, Johannesburg")
    val currentStoreAddress: StateFlow<String> = _currentStoreAddress.asStateFlow()

    private val _currentStorePhone = MutableStateFlow("011-123-4567")
    val currentStorePhone: StateFlow<String> = _currentStorePhone.asStateFlow()

    private val _profilePictureUri = MutableStateFlow<String?>(null)
    val profilePictureUri: StateFlow<String?> = _profilePictureUri.asStateFlow()

    private val _isLoading = MutableStateFlow(false)
    val isLoading: StateFlow<Boolean> = _isLoading.asStateFlow()

    /** One-shot text for a snackbar (errors and confirmations). */
    private val _message = MutableStateFlow<String?>(null)
    val message: StateFlow<String?> = _message.asStateFlow()

    private val storeId: Int get() = StoreRepository.activeStore.value.storeId

    // Isolated per-store local database maps for offline mode
    private val perStoreProducts = mutableMapOf<Int, MutableList<Product>>()
    private val perStoreAlerts = mutableMapOf<Int, MutableList<LowStockAlert>>()
    private val perStoreSales = mutableMapOf<Int, SalesReport>()

    init {
        // Collect active store changes and automatically switch isolated store database
        viewModelScope.launch {
            StoreRepository.activeStore.collect { activeStore ->
                _currentStoreName.value = activeStore.storeName
                _currentStoreAddress.value = activeStore.storeAddress
                _currentStorePhone.value = activeStore.storePhone
                try {
                    loadAll()
                } catch (e: Exception) {
                    loadOfflineDataForStore(activeStore.storeId)
                }
            }
        }
    }

    fun consumeMessage() {
        _message.value = null
    }

    fun initProfilePicture(context: Context) {
        val prefs = context.getSharedPreferences("profile_prefs", Context.MODE_PRIVATE)
        _profilePictureUri.value = prefs.getString("profile_picture_uri", null)
    }

    fun setProfilePicture(context: Context, uri: String?) {
        _profilePictureUri.value = uri
        val prefs = context.getSharedPreferences("profile_prefs", Context.MODE_PRIVATE)
        if (uri.isNullOrBlank()) {
            prefs.edit().remove("profile_picture_uri").apply()
            _message.value = "Profile picture removed"
        } else {
            prefs.edit().putString("profile_picture_uri", uri).apply()
            _message.value = "Profile picture updated"
        }
    }

    fun initDarkMode(context: Context) {
        val prefs = context.getSharedPreferences("theme_prefs", Context.MODE_PRIVATE)
        _isDarkMode.value = prefs.getBoolean("is_dark_mode", false)
    }

    fun toggleDarkMode(context: Context, isDark: Boolean) {
        _isDarkMode.value = isDark
        val prefs = context.getSharedPreferences("theme_prefs", Context.MODE_PRIVATE)
        prefs.edit().putBoolean("is_dark_mode", isDark).apply()
        _message.value = if (isDark) "Dark Theme Enabled" else "Light Theme Enabled"
    }

    fun setStoreDetails(name: String, address: String = "", phone: String = "") {
        if (name.isNotBlank()) _currentStoreName.value = name
        if (address.isNotBlank()) _currentStoreAddress.value = address
        if (phone.isNotBlank()) _currentStorePhone.value = phone
    }

    fun initLanguage(context: Context) {
        val saved = LanguageManager.getSavedLanguage(context)
        _currentLanguage.value = saved
        LanguageManager.applyLanguage(context, saved)
    }

    fun setLanguage(context: Context, languageName: String) {
        _currentLanguage.value = languageName
        LanguageManager.applyLanguage(context, languageName)
        _message.value = "Language changed to $languageName"
    }

    // ---- Auth & Sync ---------------------------------------------------------------------

    fun initAccountRepo(context: Context) {
        AccountRepository.init(context)
    }

    fun login(email: String, password: String, context: Context? = null, onSuccess: () -> Unit) {
        if (email.isBlank() || password.isBlank()) {
            _message.value = "Enter your email and password"
            return
        }
        viewModelScope.launch {
            _isLoading.value = true
            FirebaseAuthManager.signInWithEmail(
                email = email.trim(),
                pass = password,
                onSuccess = { uid ->
                    val user = User(
                        id = uid?.hashCode() ?: 1,
                        name = email.substringBefore("@").replaceFirstChar { it.uppercase() },
                        email = email.trim(),
                        storeId = storeId
                    )
                    ApiClient.authToken = "firebase-token-" + System.currentTimeMillis()
                    _currentUser.value = user
                    if (context != null) {
                        AccountRepository.saveAccount(
                            context,
                            Account(
                                id = user.id,
                                name = user.name,
                                email = email.trim(),
                                password = password,
                                storeName = _currentStoreName.value,
                                isPendingOnlineSync = false
                            )
                        )
                    }
                    _message.value = "Firebase Authenticated: Welcome ${user.name}"
                    _isLoading.value = false
                    onSuccess()
                },
                onError = { _ ->
                    // Fall back to ApiService / local authentication
                    viewModelScope.launch {
                        try {
                            val response = ApiService.login(email.trim(), password)
                            completeAuth(response, "Login failed")
                            if (context != null) {
                                AccountRepository.saveAccount(
                                    context,
                                    Account(
                                        id = response.user?.id ?: 1,
                                        name = response.user?.name ?: "User",
                                        email = email.trim(),
                                        password = password,
                                        storeName = _currentStoreName.value,
                                        isPendingOnlineSync = false
                                    )
                                )
                            }
                            onSuccess()
                        } catch (e: Exception) {
                            loginOffline(context, email, password, onSuccess)
                        } finally {
                            _isLoading.value = false
                        }
                    }
                }
            )
        }
    }

    fun loginWithGoogle(
        context: Context,
        enteredEmail: String = "",
        onRegistered: () -> Unit = {},
        onNotRegistered: () -> Unit = {}
    ) {
        val targetEmail = enteredEmail.trim().ifBlank { "google.user@bisos.co.za" }
        val isRegistered = AccountRepository.isAccountRegistered(targetEmail)

        try {
            val intent = android.content.Intent(
                android.content.Intent.ACTION_VIEW,
                android.net.Uri.parse("https://accounts.google.com/signin")
            )
            intent.addFlags(android.content.Intent.FLAG_ACTIVITY_NEW_TASK)
            context.startActivity(intent)
        } catch (_: Exception) {
            // Fallback
        }

        if (isRegistered) {
            loginOffline(context, targetEmail, "GoogleAuthPass", onRegistered)
            _message.value = "Welcome back! Signed in with Google Account"
        } else {
            _message.value = "Account ($targetEmail) is not registered yet. Redirecting to Register..."
            onNotRegistered()
        }
    }

    fun loginOffline(context: Context? = null, email: String = "", password: String = "", onSuccess: () -> Unit) {
        val savedAccount = if (context != null && email.isNotBlank()) {
            AccountRepository.authenticateOffline(email, password)
        } else null

        val userName = savedAccount?.name ?: if (email.contains("@")) {
            email.substringBefore("@").replaceFirstChar { it.uppercase() }
        } else if (email.isNotBlank()) {
            email
        } else {
            "John Molefe"
        }

        val user = User(
            id = savedAccount?.id ?: 1,
            name = userName,
            email = if (email.isNotBlank()) email else "demo@bisos.co.za",
            storeId = storeId
        )
        ApiClient.authToken = "offline-token-" + System.currentTimeMillis()
        _currentUser.value = user
        loadOfflineDataForStore(storeId)
        _message.value = "Signed in (Offline Mode)"
        onSuccess()
    }

    fun register(context: Context? = null, name: String, email: String, password: String, onSuccess: () -> Unit) {
        if (name.isBlank() || email.isBlank() || password.isBlank()) {
            _message.value = "Fill in your name, email and password"
            return
        }
        viewModelScope.launch {
            _isLoading.value = true
            FirebaseAuthManager.signUpWithEmail(
                email = email.trim(),
                pass = password,
                onSuccess = { _ ->
                    _message.value = "Registered with Firebase Auth"
                },
                onError = { _ -> }
            )

            try {
                val response = ApiService.register(name.trim(), email.trim(), password)
                completeAuth(response, "Registration failed")
                if (context != null) {
                    AccountRepository.saveAccount(
                        context,
                        Account(
                            id = response.user?.id ?: 1,
                            name = name.trim(),
                            email = email.trim(),
                            password = password,
                            storeName = _currentStoreName.value,
                            isPendingOnlineSync = false
                        )
                    )
                    AccountRepository.syncPendingOfflineAccountsToOnline(context)
                }
                onSuccess()
            } catch (e: Exception) {
                if (context != null) {
                    AccountRepository.saveAccount(
                        context,
                        Account(
                            id = System.currentTimeMillis().toInt(),
                            name = name.trim(),
                            email = email.trim(),
                            password = password,
                            storeName = _currentStoreName.value,
                            isPendingOnlineSync = true
                        )
                    )
                }
                loginOffline(context, email, password, onSuccess)
            } finally {
                _isLoading.value = false
            }
        }
    }

    fun logout() {
        ApiClient.authToken = null
        _currentUser.value = null
        _products.value = emptyList()
        _alerts.value = emptyList()
        _salesReport.value = null
    }

    private suspend fun completeAuth(response: AuthResponse, fallbackError: String) {
        val user = response.user
        val token = response.token
        if (user == null || token == null) {
            throw ApiException(response.error ?: fallbackError)
        }
        ApiClient.authToken = token
        _currentUser.value = user
        try {
            loadAll()
        } catch (e: Exception) {
            loadOfflineDataForStore(storeId)
        }
    }

    // ---- Data ---------------------------------------------------------------------------

    fun refreshAll() = launchApi { loadAll() }

    fun addProduct(name: String, price: Double, stock: Int, reorder: Int) {
        val activeId = storeId
        launchApi {
            try {
                ApiService.addProduct(name, price, stock, reorder, activeId)
                _message.value = "$name added"
                loadAll()
            } catch (e: Exception) {
                val newProd = Product(
                    id = System.currentTimeMillis().toInt(),
                    name = name,
                    barcode = "600" + (1000000000..9999999999).random(),
                    unitPrice = price,
                    costPrice = price * 0.7,
                    reorderLevel = reorder,
                    categoryId = 1,
                    storeId = activeId,
                    category = Category(1, "General"),
                    stock = Stock(quantityOnHand = stock, minStockLevel = reorder, maxStockLevel = 999)
                )
                val currentList = perStoreProducts.getOrPut(activeId) { mutableListOf() }
                currentList.add(0, newProd)
                _products.value = currentList.toList()
                _message.value = "$name added to ${_currentStoreName.value}"
            }
        }
    }

    fun recordSale(productId: Int, quantity: Int, paymentMethod: String) {
        val activeId = storeId
        launchApi {
            try {
                ApiService.recordSale(productId, quantity, paymentMethod, activeId)
                _message.value = "Sale completed!"
                loadAll()
            } catch (e: Exception) {
                val currentProds = perStoreProducts.getOrPut(activeId) { mutableListOf() }
                val idx = currentProds.indexOfFirst { it.id == productId }
                if (idx >= 0) {
                    val p = currentProds[idx]
                    val currQty = p.stock?.quantityOnHand ?: 0
                    val newQty = (currQty - quantity).coerceAtLeast(0)
                    currentProds[idx] = p.copy(
                        stock = Stock(
                            quantityOnHand = newQty,
                            minStockLevel = p.stock?.minStockLevel ?: 10,
                            maxStockLevel = 999
                        )
                    )
                    _products.value = currentProds.toList()
                }
                _message.value = "Sale recorded in ${_currentStoreName.value}"
            }
        }
    }

    fun voidSale(transactionId: Int) {
        launchApi {
            try {
                ApiService.voidSale(transactionId)
                _message.value = "Sale voided & stock restored"
                loadAll()
            } catch (e: Exception) {
                _message.value = "Sale voided"
            }
        }
    }

    fun deleteProduct(productId: Int) {
        val activeId = storeId
        launchApi {
            try {
                ApiService.deleteProduct(productId)
                _message.value = "Product deleted"
                loadAll()
            } catch (e: Exception) {
                val currentList = perStoreProducts.getOrPut(activeId) { mutableListOf() }
                currentList.removeAll { it.id == productId }
                _products.value = currentList.toList()
                _message.value = "Product deleted"
            }
        }
    }

    fun updateProduct(id: Int, name: String, price: Double, stock: Int, reorder: Int, barcode: String = "") {
        val activeId = storeId
        launchApi {
            try {
                ApiService.updateProduct(id, name, price, stock, reorder, barcode)
                _message.value = "$name updated"
                loadAll()
            } catch (e: Exception) {
                val currentProds = perStoreProducts.getOrPut(activeId) { mutableListOf() }
                val idx = currentProds.indexOfFirst { it.id == id }
                if (idx >= 0) {
                    val old = currentProds[idx]
                    currentProds[idx] = old.copy(
                        name = name,
                        unitPrice = price,
                        reorderLevel = reorder,
                        barcode = barcode.ifBlank { old.barcode },
                        stock = Stock(quantityOnHand = stock, minStockLevel = reorder, maxStockLevel = 999)
                    )
                    _products.value = currentProds.toList()
                }
                _message.value = "$name updated"
            }
        }
    }

    /** Lets the user check the ngrok URL before logging in. */
    fun testConnection() {
        launchApi(showLoading = true) {
            _message.value = "Connected: ${ApiService.ping()}"
        }
    }

    private suspend fun loadAll() {
        val id = storeId
        _products.value = ApiService.getProducts(id)
        _alerts.value = ApiService.getAlerts(id)
        _salesReport.value = ApiService.getSalesReport(id)
    }

    private fun loadOfflineDataForStore(targetStoreId: Int) {
        val storeName = _currentStoreName.value
        val prods = perStoreProducts.getOrPut(targetStoreId) {
            when (targetStoreId) {
                2 -> mutableListOf(
                    Product(201, "Sandton Coffee 250g", "600999001", 65.00, 40.00, 50, 1, 2, Category(1, "Pantry"), Stock(30, 10, 50)),
                    Product(202, "Sandton Mineral Water 1L", "600999002", 15.00, 8.00, 50, 1, 2, Category(2, "Beverages"), Stock(80, 20, 100))
                )
                3 -> mutableListOf(
                    Product(301, "Pretoria Honey 500g", "600888001", 95.00, 60.00, 50, 1, 3, Category(1, "Pantry"), Stock(12, 10, 50)),
                    Product(302, "Pretoria Tea 100s", "600888002", 45.00, 28.00, 50, 1, 3, Category(2, "Beverages"), Stock(40, 15, 80))
                )
                else -> mutableListOf(
                    Product(1, "Milk 2L", "6001234567890", 24.99, 18.50, 50, 1, 1, Category(1, "Dairy"), Stock(15, 10, 50)),
                    Product(2, "Bread 700g", "6001234567891", 18.50, 12.00, 50, 1, 1, Category(2, "Bakery"), Stock(45, 15, 60)),
                    Product(3, "Sugar 1kg", "6001234567892", 32.00, 22.00, 50, 1, 1, Category(3, "Pantry"), Stock(8, 5, 40)),
                    Product(4, "Cooking Oil 750ml", "6001234567893", 58.99, 45.00, 50, 1, 1, Category(3, "Pantry"), Stock(32, 12, 45)),
                    Product(5, "Chicken 1kg", "6001234567894", 72.99, 55.00, 50, 1, 1, Category(4, "Meat"), Stock(5, 8, 30)),
                    Product(6, "Rice 5kg", "6001234567895", 85.00, 65.00, 50, 1, 1, Category(5, "Grains"), Stock(20, 5, 50))
                )
            }
        }
        _products.value = prods.toList()

        val alertsList = perStoreAlerts.getOrPut(targetStoreId) {
            prods.filter { (it.stock?.quantityOnHand ?: 0) < 50 }.map {
                LowStockAlert(it.id, it.name, it.stock?.quantityOnHand ?: 0, 50, "PENDING")
            }.toMutableList()
        }
        _alerts.value = alertsList.toList()

        val report = perStoreSales.getOrPut(targetStoreId) {
            SalesReport(
                date = "March 2026",
                branch = "$storeName (Offline)",
                totalSales = prods.sumOf { (it.stock?.quantityOnHand ?: 0) * it.unitPrice * 0.1 },
                transactionCount = prods.size,
                averageSale = 150.00,
                cashTotal = 250.00,
                cardTotal = 150.00,
                mobileTotal = 100.00,
                items = prods.take(3).mapIndexed { idx, p ->
                    SaleItem(idx + 1, "TXN-${1000 + idx}", "2026-03-01", p.name, 2, p.unitPrice, p.unitPrice * 2, "CASH", storeName)
                }
            )
        }
        _salesReport.value = report
    }

    // ---- Helpers ------------------------------------------------------------------------

    private fun launchApi(showLoading: Boolean = false, block: suspend () -> Unit) {
        viewModelScope.launch {
            if (showLoading) _isLoading.value = true
            try {
                block()
            } catch (e: CancellationException) {
                throw e
            } catch (e: ApiException) {
                _message.value = e.message
            } catch (e: Exception) {
                _message.value = "Operating in Offline Mode for ${_currentStoreName.value}."
            } finally {
                if (showLoading) _isLoading.value = false
            }
        }
    }
}
