package com.yourcompany.informalsupermarket.ui

import androidx.compose.foundation.layout.padding
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Modifier
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.res.stringResource
import androidx.compose.ui.unit.dp
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.NavDestination.Companion.hierarchy
import androidx.navigation.NavGraph.Companion.findStartDestination
import androidx.navigation.compose.*
import com.example.informalsupermarket.R
import com.yourcompany.informalsupermarket.network.ServerConfig
import com.yourcompany.informalsupermarket.ui.dialogs.LowStockAlertDialog
import com.yourcompany.informalsupermarket.ui.screens.*
import com.yourcompany.informalsupermarket.ui.theme.*

object Routes {
    const val LOGIN = "login"
    const val REGISTER = "register"
    const val DASHBOARD = "dashboard"
    const val STOCK = "stock"
    const val ADD_STOCK = "add_stock"
    const val SALES = "sales"
    const val REPORTS = "reports"
    const val SETTINGS = "settings"
}

sealed class Screen(val route: String, val iconRes: Int, val labelRes: Int) {
    object Dashboard : Screen(Routes.DASHBOARD, R.drawable.ic_home, R.string.home)
    object Stock : Screen(Routes.STOCK, R.drawable.ic_stock, R.string.stock)
    object Sales : Screen(Routes.SALES, R.drawable.ic_sales, R.string.sales)
    object Reports : Screen(Routes.REPORTS, R.drawable.ic_reports, R.string.reports)
    object Settings : Screen(Routes.SETTINGS, R.drawable.ic_settings, R.string.settings)
}

val mainScreens = listOf(
    Screen.Dashboard,
    Screen.Stock,
    Screen.Sales,
    Screen.Reports,
    Screen.Settings
)

@Composable
fun InformalMartApp() {
    val context = LocalContext.current
    val navController = rememberNavController()
    val viewModel: InformalMartViewModel = viewModel()

    LaunchedEffect(Unit) {
        viewModel.initLanguage(context)
        viewModel.initDarkMode(context)
        viewModel.initAccountRepo(context)
        viewModel.initProfilePicture(context)
    }

    var showLowStockDialog by remember { mutableStateOf(false) }
    val navBackStackEntry by navController.currentBackStackEntryAsState()
    val currentDestination = navBackStackEntry?.destination

    val isMainScreen = mainScreens.any { it.route == currentDestination?.route }

    val isLoading by viewModel.isLoading.collectAsState()
    val message by viewModel.message.collectAsState()
    val currentUser by viewModel.currentUser.collectAsState()
    val currentLanguage by viewModel.currentLanguage.collectAsState()
    val isDarkMode by viewModel.isDarkMode.collectAsState()
    val profilePictureUri by viewModel.profilePictureUri.collectAsState()
    val currentStoreName by viewModel.currentStoreName.collectAsState()
    val currentStoreAddress by viewModel.currentStoreAddress.collectAsState()
    val currentStorePhone by viewModel.currentStorePhone.collectAsState()
    val allStores by com.yourcompany.informalsupermarket.data.StoreRepository.stores.collectAsState()

    val snackbarHostState = remember { SnackbarHostState() }
    LaunchedEffect(message) {
        message?.let {
            snackbarHostState.showSnackbar(it)
            viewModel.consumeMessage()
        }
    }

    key(currentLanguage) {
        InformalSupermarketTheme(darkTheme = isDarkMode) {
        Scaffold(
            snackbarHost = { SnackbarHost(snackbarHostState) },
            bottomBar = {
                if (isMainScreen) {
                    NavigationBar(containerColor = White, tonalElevation = 8.dp) {
                        mainScreens.forEach { screen ->
                            NavigationBarItem(
                                selected = currentDestination?.hierarchy?.any { it.route == screen.route } == true,
                                onClick = {
                                    navController.navigate(screen.route) {
                                        popUpTo(navController.graph.findStartDestination().id) {
                                            saveState = true
                                        }
                                        launchSingleTop = true
                                        restoreState = true
                                    }
                                },
                                icon = { Icon(painterResource(id = screen.iconRes), contentDescription = null) },
                                label = { Text(stringResource(id = screen.labelRes)) },
                                colors = NavigationBarItemDefaults.colors(
                                    selectedIconColor = Green900,
                                    selectedTextColor = Green900,
                                    indicatorColor = Green50
                                )
                            )
                        }
                    }
                }
            }
        ) { paddingValues ->
            NavHost(
                navController = navController,
                startDestination = Routes.LOGIN,
                modifier = Modifier.padding(if (isMainScreen) paddingValues else androidx.compose.foundation.layout.PaddingValues())
            ) {
                composable(Routes.LOGIN) {
                    LoginScreen(
                        isLoading = isLoading,
                        serverUrl = ServerConfig.baseUrl,
                        onServerUrlSave = { ServerConfig.save(it) },
                        onTestConnection = { viewModel.testConnection() },
                        onLoginClick = { email, password ->
                            viewModel.login(email, password, context) {
                                navController.navigate(Routes.DASHBOARD) {
                                    popUpTo(0) { inclusive = true }
                                }
                            }
                        },
                        onLoginOffline = { email, password ->
                            viewModel.loginOffline(context, email, password) {
                                navController.navigate(Routes.DASHBOARD) {
                                    popUpTo(0) { inclusive = true }
                                }
                            }
                        },
                        onGoogleSignInClick = { emailInput ->
                            viewModel.loginWithGoogle(
                                context = context,
                                enteredEmail = emailInput,
                                onRegistered = {
                                    navController.navigate(Routes.DASHBOARD) {
                                        popUpTo(0) { inclusive = true }
                                    }
                                },
                                onNotRegistered = {
                                    navController.navigate(Routes.REGISTER)
                                }
                            )
                        },
                        onRegisterClick = { navController.navigate(Routes.REGISTER) }
                    )
                }
                composable(Routes.REGISTER) {
                    RegisterScreen(
                        isLoading = isLoading,
                        onBackClick = { navController.popBackStack() },
                        onGoogleSignInClick = {
                            viewModel.loginWithGoogle(
                                context = context,
                                enteredEmail = "",
                                onRegistered = {
                                    navController.navigate(Routes.DASHBOARD) {
                                        popUpTo(0) { inclusive = true }
                                    }
                                },
                                onNotRegistered = {
                                    // Already on register screen
                                }
                            )
                        },
                        onRegisterClick = { name, email, password, sName, sAddress, sPhone ->
                            viewModel.setStoreDetails(sName, sAddress, sPhone)
                            viewModel.register(context, name, email, password) {
                                navController.navigate(Routes.DASHBOARD) {
                                    popUpTo(0) { inclusive = true }
                                }
                            }
                        }
                    )
                }
                composable(Routes.DASHBOARD) {
                    DashboardScreen(
                        viewModel = viewModel,
                        onStockClick = { navController.navigate(Routes.STOCK) },
                        onAddStockClick = { navController.navigate(Routes.ADD_STOCK) },
                        onSalesClick = { navController.navigate(Routes.SALES) },
                        onAlertsClick = { showLowStockDialog = true },
                        onSettingsClick = { navController.navigate(Routes.SETTINGS) },
                        onLogout = {
                            viewModel.logout()
                            navController.navigate(Routes.LOGIN) {
                                popUpTo(0) { inclusive = true }
                            }
                        }
                    )
                }
                composable(Routes.STOCK) {
                    StockScreen(
                        viewModel = viewModel,
                        onBackClick = { navController.navigate(Routes.DASHBOARD) },
                        onAddStockClick = { navController.navigate(Routes.ADD_STOCK) }
                    )
                }
                composable(Routes.ADD_STOCK) {
                    AddStockScreen(
                        onBackClick = { navController.popBackStack() },
                        onAddProduct = { name, price, stock, reorder ->
                            viewModel.addProduct(name, price, stock, reorder)
                            navController.popBackStack()
                        }
                    )
                }
                composable(Routes.SALES) {
                    SalesScreen(
                        viewModel = viewModel,
                        onBackClick = { navController.navigate(Routes.DASHBOARD) }
                    )
                }
                composable(Routes.REPORTS) {
                    SalesReportScreen(
                        viewModel = viewModel,
                        onBackClick = { navController.navigate(Routes.DASHBOARD) }
                    )
                }
                composable(Routes.SETTINGS) {
                    SettingsScreen(
                        userName = currentUser?.name?.takeIf { it.isNotBlank() } ?: "John Molefe",
                        userEmail = currentUser?.email?.takeIf { it.isNotBlank() } ?: "john.doe@bisos.co.za",
                        userRole = "Store Owner / Manager",
                        profilePictureUri = profilePictureUri,
                        onProfilePictureChange = { uri -> viewModel.setProfilePicture(context, uri) },
                        storeName = currentStoreName,
                        storeAddress = currentStoreAddress,
                        storePhone = currentStorePhone,
                        allStores = allStores,
                        onStoreSelect = { st ->
                            com.yourcompany.informalsupermarket.data.StoreRepository.setActiveStore(st.storeId)
                            viewModel.setStoreDetails(st.storeName, st.storeAddress, st.storePhone)
                        },
                        onStoreInfoSave = { sName, sAddress, sPhone ->
                            viewModel.setStoreDetails(sName, sAddress, sPhone)
                            val activeId = com.yourcompany.informalsupermarket.data.StoreRepository.activeStore.value.storeId
                            com.yourcompany.informalsupermarket.data.StoreRepository.updateStore(
                                com.yourcompany.informalsupermarket.data.Store(activeId, sName, sAddress, sPhone)
                            )
                        },
                        serverUrl = ServerConfig.baseUrl,
                        currentLanguage = currentLanguage,
                        isDarkMode = isDarkMode,
                        onDarkModeChange = { isDark -> viewModel.toggleDarkMode(context, isDark) },
                        onLanguageChange = { lang -> viewModel.setLanguage(context, lang) },
                        onServerUrlSave = { ServerConfig.save(it) },
                        onTestConnection = { viewModel.testConnection() },
                        onBackClick = { navController.navigate(Routes.DASHBOARD) },
                        onLogout = {
                            viewModel.logout()
                            navController.navigate(Routes.LOGIN) {
                                popUpTo(0) { inclusive = true }
                            }
                        }
                    )
                }
            }
        }

        if (showLowStockDialog) {
            LowStockAlertDialog(
                viewModel = viewModel,
                onDismiss = { showLowStockDialog = false }
            )
        }
    }
}
}

