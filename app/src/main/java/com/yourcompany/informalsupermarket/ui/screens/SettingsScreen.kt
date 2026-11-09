package com.yourcompany.informalsupermarket.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.res.stringResource
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import coil.compose.AsyncImage
import com.example.informalsupermarket.R
import com.yourcompany.informalsupermarket.data.Store
import com.yourcompany.informalsupermarket.ui.theme.*

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun SettingsScreen(
    modifier: Modifier = Modifier,
    userName: String = "John Molefe",
    userEmail: String = "john.doe@bisos.co.za",
    userRole: String = "Manager",
    profilePictureUri: String? = null,
    onProfilePictureChange: (String?) -> Unit = {},
    storeName: String = "Soweto Supermarket",
    storeAddress: String = "123 Soweto Street, Johannesburg",
    storePhone: String = "011-123-4567",
    allStores: List<Store> = emptyList(),
    onStoreSelect: (Store) -> Unit = {},
    onStoreInfoSave: (String, String, String) -> Unit = { _, _, _ -> },
    serverUrl: String = "",
    currentLanguage: String = "English",
    isDarkMode: Boolean = false,
    onDarkModeChange: (Boolean) -> Unit = {},
    onLanguageChange: (String) -> Unit = {},
    onServerUrlSave: (String) -> String = { it },
    onTestConnection: () -> Unit = {},
    onBackClick: () -> Unit = {},
    onLogout: () -> Unit = {}
) {
    var showProfilePhotoDialog by remember { mutableStateOf(false) }
    var showProfileDialog by remember { mutableStateOf(false) }
    var showNotificationDialog by remember { mutableStateOf(false) }
    var showStoreInfoDialog by remember { mutableStateOf(false) }
    var showLanguageDialog by remember { mutableStateOf(false) }
    var showServerDialog by remember { mutableStateOf(false) }
    var showLogoutDialog by remember { mutableStateOf(false) }

    // State for notifications
    var lowStockAlerts by remember { mutableStateOf(true) }
    var dailySalesSummary by remember { mutableStateOf(true) }
    var pushNotifications by remember { mutableStateOf(true) }

    // State for language
    var selectedLanguage by remember { mutableStateOf(currentLanguage) }
    val languages = listOf("English", "Afrikaans", "isiZulu", "Sesotho")

    // Editable profile state
    var editName by remember { mutableStateOf(userName) }
    var editPhone by remember { mutableStateOf("011-123-4567") }

    // Editable store info state
    var editStoreName by remember { mutableStateOf(storeName) }
    var editStoreAddress by remember { mutableStateOf(storeAddress) }
    var editStorePhone by remember { mutableStateOf(storePhone) }

    // Server URL state
    var serverUrlText by remember { mutableStateOf(serverUrl) }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Settings", color = White, fontWeight = FontWeight.Bold) },
                navigationIcon = {
                    IconButton(onClick = onBackClick) {
                        Icon(painterResource(id = R.drawable.ic_arrow_back), contentDescription = null, tint = White)
                    }
                },
                colors = TopAppBarDefaults.topAppBarColors(containerColor = Green900)
            )
        },
        modifier = modifier
    ) { padding ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(padding)
                .background(MaterialTheme.colorScheme.background)
                .verticalScroll(rememberScrollState())
                .padding(16.dp),
            verticalArrangement = Arrangement.spacedBy(12.dp)
        ) {
            // User Brief Header Card
            Card(
                colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.surface),
                elevation = CardDefaults.cardElevation(defaultElevation = 2.dp),
                shape = RoundedCornerShape(12.dp),
                modifier = Modifier.fillMaxWidth()
            ) {
                Row(
                    modifier = Modifier.padding(16.dp),
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Box(
                        modifier = Modifier
                            .size(56.dp)
                            .clip(RoundedCornerShape(50))
                            .clickable { showProfilePhotoDialog = true }
                    ) {
                        Surface(
                            shape = RoundedCornerShape(50),
                            color = Blue50,
                            modifier = Modifier.fillMaxSize()
                        ) {
                            Box(contentAlignment = Alignment.Center) {
                                if (!profilePictureUri.isNullOrBlank()) {
                                    AsyncImage(
                                        model = profilePictureUri,
                                        contentDescription = "Profile Picture",
                                        modifier = Modifier.fillMaxSize().clip(RoundedCornerShape(50)),
                                        contentScale = ContentScale.Crop,
                                        placeholder = painterResource(id = R.drawable.ic_account),
                                        error = painterResource(id = R.drawable.ic_account)
                                    )
                                } else {
                                    Icon(
                                        painter = painterResource(id = R.drawable.ic_account),
                                        contentDescription = null,
                                        tint = Blue900,
                                        modifier = Modifier.size(30.dp)
                                    )
                                }
                            }
                        }
                    }
                    Spacer(modifier = Modifier.width(16.dp))
                    Column(
                        modifier = Modifier
                            .weight(1f)
                            .clickable { showProfilePhotoDialog = true }
                    ) {
                        Text(text = userName, fontWeight = FontWeight.Bold, fontSize = 18.sp, color = MaterialTheme.colorScheme.onSurface)
                        Text(text = userEmail, fontSize = 13.sp, color = Gray600)
                        Text(text = "Tap avatar to change photo • Role: $userRole", fontSize = 11.sp, color = Blue500, fontWeight = FontWeight.Medium)
                    }
                }
            }

            Spacer(modifier = Modifier.height(4.dp))
            Text("PREFERENCES & CONFIGURATION", fontSize = 12.sp, fontWeight = FontWeight.Bold, color = Gray600)

            // Setting Cards
            Card(
                colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.surface),
                elevation = CardDefaults.cardElevation(defaultElevation = 2.dp),
                shape = RoundedCornerShape(12.dp),
                modifier = Modifier.fillMaxWidth()
            ) {
                Column {
                    SettingRow(
                        title = stringResource(R.string.profile_settings),
                        subtitle = "Manage personal details & contact",
                        iconRes = R.drawable.ic_account,
                        onClick = { showProfileDialog = true }
                    )
                    HorizontalDivider(color = Gray200)
                    SettingRow(
                        title = stringResource(R.string.notification_prefs),
                        subtitle = "Stock alerts & daily summaries",
                        iconRes = R.drawable.ic_notifications,
                        badge = if (lowStockAlerts) "Enabled" else "Disabled",
                        onClick = { showNotificationDialog = true }
                    )
                    HorizontalDivider(color = Gray200)
                    SettingRow(
                        title = stringResource(R.string.store_information),
                        subtitle = storeName,
                        iconRes = R.drawable.ic_store,
                        onClick = { showStoreInfoDialog = true }
                    )
                    HorizontalDivider(color = Gray200)
                    SettingRow(
                        title = stringResource(R.string.language),
                        subtitle = selectedLanguage,
                        iconRes = R.drawable.ic_settings,
                        badge = selectedLanguage,
                        onClick = { showLanguageDialog = true }
                    )
                    HorizontalDivider(color = Gray200)
                    SettingRow(
                        title = stringResource(R.string.app_theme),
                        subtitle = if (isDarkMode) "Dark Theme Enabled" else "Light Theme Enabled",
                        iconRes = R.drawable.ic_settings,
                        badge = if (isDarkMode) "Dark" else "Light",
                        onClick = { onDarkModeChange(!isDarkMode) }
                    )
                    HorizontalDivider(color = Gray200)
                    SettingRow(
                        title = stringResource(R.string.server_connection),
                        subtitle = "Configure backend API URL",
                        iconRes = R.drawable.ic_trending_up,
                        onClick = { showServerDialog = true }
                    )
                }
            }

            Spacer(modifier = Modifier.height(16.dp))

            // Logout Button
            Button(
                onClick = { showLogoutDialog = true },
                modifier = Modifier
                    .fillMaxWidth()
                    .height(50.dp),
                colors = ButtonDefaults.buttonColors(containerColor = Red700),
                shape = RoundedCornerShape(10.dp)
            ) {
                Text("Logout", fontSize = 16.sp, fontWeight = FontWeight.Bold, color = White)
            }
        }
    }

    // Profile Photo Avatar Control Dialog
    if (showProfilePhotoDialog) {
        AlertDialog(
            onDismissRequest = { showProfilePhotoDialog = false },
            title = { Text("Profile Photo", fontWeight = FontWeight.Bold) },
            text = {
                Column(
                    horizontalAlignment = Alignment.CenterHorizontally,
                    verticalArrangement = Arrangement.spacedBy(12.dp),
                    modifier = Modifier.fillMaxWidth()
                ) {
                    Surface(
                        shape = RoundedCornerShape(50),
                        color = Blue50,
                        modifier = Modifier.size(72.dp)
                    ) {
                        Box(contentAlignment = Alignment.Center) {
                            if (!profilePictureUri.isNullOrBlank()) {
                                AsyncImage(
                                    model = profilePictureUri,
                                    contentDescription = "Avatar Preview",
                                    modifier = Modifier.fillMaxSize().clip(RoundedCornerShape(50)),
                                    contentScale = ContentScale.Crop,
                                    placeholder = painterResource(id = R.drawable.ic_account),
                                    error = painterResource(id = R.drawable.ic_account)
                                )
                            } else {
                                Icon(
                                    painter = painterResource(id = R.drawable.ic_account),
                                    contentDescription = null,
                                    tint = Blue900,
                                    modifier = Modifier.size(40.dp)
                                )
                            }
                        }
                    }

                    Text("Choose how you want to set your profile picture:", fontSize = 12.sp, color = Gray600)

                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.spacedBy(8.dp)
                    ) {
                        Button(
                            onClick = {
                                onProfilePictureChange("https://loremflickr.com/300/300/portrait,face?random=${(1..999).random()}")
                                showProfilePhotoDialog = false
                            },
                            modifier = Modifier.weight(1f),
                            colors = ButtonDefaults.buttonColors(containerColor = Blue900),
                            shape = RoundedCornerShape(8.dp)
                        ) {
                            Text("📷 Camera / Photo", fontSize = 11.sp, fontWeight = FontWeight.Bold)
                        }

                        Button(
                            onClick = {
                                onProfilePictureChange("https://loremflickr.com/300/300/avatar,person?random=${(1..999).random()}")
                                showProfilePhotoDialog = false
                            },
                            modifier = Modifier.weight(1f),
                            colors = ButtonDefaults.buttonColors(containerColor = Blue900),
                            shape = RoundedCornerShape(8.dp)
                        ) {
                            Text("✨ AI Avatar", fontSize = 11.sp, fontWeight = FontWeight.Bold)
                        }
                    }

                    if (!profilePictureUri.isNullOrBlank()) {
                        OutlinedButton(
                            onClick = {
                                onProfilePictureChange(null)
                                showProfilePhotoDialog = false
                            },
                            modifier = Modifier.fillMaxWidth(),
                            colors = ButtonDefaults.outlinedButtonColors(contentColor = Red700),
                            shape = RoundedCornerShape(8.dp)
                        ) {
                            Text("🗑️ Remove Profile Photo", fontSize = 12.sp, fontWeight = FontWeight.Bold)
                        }
                    }
                }
            },
            confirmButton = {},
            dismissButton = {
                TextButton(onClick = { showProfilePhotoDialog = false }) {
                    Text("Close", color = Gray600)
                }
            }
        )
    }

    // 0. Logout Confirmation Dialog
    if (showLogoutDialog) {
        AlertDialog(
            onDismissRequest = { showLogoutDialog = false },
            title = { Text("Confirm Logout", fontWeight = FontWeight.Bold) },
            text = { Text("Are you sure you want to log out of Informal Mart?") },
            confirmButton = {
                Button(
                    onClick = {
                        showLogoutDialog = false
                        onLogout()
                    },
                    colors = ButtonDefaults.buttonColors(containerColor = Red700)
                ) {
                    Text("Logout", color = White)
                }
            },
            dismissButton = {
                TextButton(onClick = { showLogoutDialog = false }) {
                    Text("Cancel", color = Gray600)
                }
            }
        )
    }

    // 1. Profile Dialog
    if (showProfileDialog) {
        AlertDialog(
            onDismissRequest = { showProfileDialog = false },
            title = { Text("Profile Settings", fontWeight = FontWeight.Bold) },
            text = {
                Column(verticalArrangement = Arrangement.spacedBy(12.dp)) {
                    OutlinedTextField(
                        value = editName,
                        onValueChange = { editName = it },
                        label = { Text("Full Name") },
                        singleLine = true,
                        modifier = Modifier.fillMaxWidth()
                    )
                    OutlinedTextField(
                        value = userEmail,
                        onValueChange = {},
                        enabled = false,
                        label = { Text("Email (Read Only)") },
                        singleLine = true,
                        modifier = Modifier.fillMaxWidth()
                    )
                    OutlinedTextField(
                        value = editPhone,
                        onValueChange = { editPhone = it },
                        label = { Text("Phone Number") },
                        singleLine = true,
                        modifier = Modifier.fillMaxWidth()
                    )
                }
            },
            confirmButton = {
                Button(
                    onClick = { showProfileDialog = false },
                    colors = ButtonDefaults.buttonColors(containerColor = Green900)
                ) {
                    Text("Save Changes")
                }
            },
            dismissButton = {
                TextButton(onClick = { showProfileDialog = false }) {
                    Text("Cancel", color = Gray600)
                }
            }
        )
    }

    // 2. Notification Preferences Dialog
    if (showNotificationDialog) {
        AlertDialog(
            onDismissRequest = { showNotificationDialog = false },
            title = { Text("Notification Preferences", fontWeight = FontWeight.Bold) },
            text = {
                Column(verticalArrangement = Arrangement.spacedBy(12.dp)) {
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.SpaceBetween,
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Column(modifier = Modifier.weight(1f)) {
                            Text("Low Stock Alerts", fontWeight = FontWeight.SemiBold)
                            Text("Receive alerts when stock drops", fontSize = 12.sp, color = Gray600)
                        }
                        Switch(
                            checked = lowStockAlerts,
                            onCheckedChange = { lowStockAlerts = it },
                            colors = SwitchDefaults.colors(checkedThumbColor = Green900, checkedTrackColor = Green50)
                        )
                    }
                    HorizontalDivider(color = Gray200)
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.SpaceBetween,
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Column(modifier = Modifier.weight(1f)) {
                            Text("Daily Sales Summary", fontWeight = FontWeight.SemiBold)
                            Text("Daily total sales report notification", fontSize = 12.sp, color = Gray600)
                        }
                        Switch(
                            checked = dailySalesSummary,
                            onCheckedChange = { dailySalesSummary = it },
                            colors = SwitchDefaults.colors(checkedThumbColor = Green900, checkedTrackColor = Green50)
                        )
                    }
                    HorizontalDivider(color = Gray200)
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.SpaceBetween,
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Column(modifier = Modifier.weight(1f)) {
                            Text("Push Notifications", fontWeight = FontWeight.SemiBold)
                            Text("System updates & messages", fontSize = 12.sp, color = Gray600)
                        }
                        Switch(
                            checked = pushNotifications,
                            onCheckedChange = { pushNotifications = it },
                            colors = SwitchDefaults.colors(checkedThumbColor = Green900, checkedTrackColor = Green50)
                        )
                    }
                }
            },
            confirmButton = {
                Button(
                    onClick = { showNotificationDialog = false },
                    colors = ButtonDefaults.buttonColors(containerColor = Green900)
                ) {
                    Text("Done")
                }
            }
        )
    }

    // 3. Store Information Dialog
    if (showStoreInfoDialog) {
        var isStoreSelectExpanded by remember { mutableStateOf(false) }

        AlertDialog(
            onDismissRequest = { showStoreInfoDialog = false },
            title = { Text("Store Information & Switcher", fontWeight = FontWeight.Bold) },
            text = {
                Column(verticalArrangement = Arrangement.spacedBy(12.dp)) {
                    if (allStores.isNotEmpty()) {
                        Text("Active Managed Store:", fontSize = 12.sp, fontWeight = FontWeight.SemiBold, color = Blue900)
                        Box(modifier = Modifier.fillMaxWidth()) {
                            OutlinedCard(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .clickable { isStoreSelectExpanded = true },
                                shape = RoundedCornerShape(8.dp)
                            ) {
                                Row(
                                    modifier = Modifier.padding(12.dp).fillMaxWidth(),
                                    horizontalArrangement = Arrangement.SpaceBetween,
                                    verticalAlignment = Alignment.CenterVertically
                                ) {
                                    Text(storeName, fontWeight = FontWeight.Bold, fontSize = 13.sp)
                                    Icon(painterResource(id = R.drawable.ic_expand_more), contentDescription = null, modifier = Modifier.size(16.dp))
                                }
                            }
                            DropdownMenu(
                                expanded = isStoreSelectExpanded,
                                onDismissRequest = { isStoreSelectExpanded = false }
                            ) {
                                allStores.forEach { st ->
                                    DropdownMenuItem(
                                        text = { Text("${st.storeName} (${st.storeAddress})") },
                                        onClick = {
                                            onStoreSelect(st)
                                            editStoreName = st.storeName
                                            editStoreAddress = st.storeAddress
                                            editStorePhone = st.storePhone
                                            isStoreSelectExpanded = false
                                        }
                                    )
                                }
                            }
                        }
                        HorizontalDivider(color = Gray200)
                    }

                    Text("Edit Store Details:", fontSize = 12.sp, fontWeight = FontWeight.SemiBold, color = Blue900)

                    OutlinedTextField(
                        value = editStoreName,
                        onValueChange = { editStoreName = it },
                        label = { Text("Store Name") },
                        singleLine = true,
                        modifier = Modifier.fillMaxWidth()
                    )
                    OutlinedTextField(
                        value = editStoreAddress,
                        onValueChange = { editStoreAddress = it },
                        label = { Text("Address") },
                        modifier = Modifier.fillMaxWidth()
                    )
                    OutlinedTextField(
                        value = editStorePhone,
                        onValueChange = { editStorePhone = it },
                        label = { Text("Contact Phone") },
                        singleLine = true,
                        modifier = Modifier.fillMaxWidth()
                    )
                }
            },
            confirmButton = {
                Button(
                    onClick = {
                        onStoreInfoSave(editStoreName, editStoreAddress, editStorePhone)
                        showStoreInfoDialog = false
                    },
                    colors = ButtonDefaults.buttonColors(containerColor = Blue900)
                ) {
                    Text("Save Store Info", color = White)
                }
            },
            dismissButton = {
                TextButton(onClick = { showStoreInfoDialog = false }) {
                    Text("Cancel", color = Gray600)
                }
            }
        )
    }

    // 4. Language Selection Dialog
    if (showLanguageDialog) {
        AlertDialog(
            onDismissRequest = { showLanguageDialog = false },
            title = { Text("Select Language", fontWeight = FontWeight.Bold) },
            text = {
                Column {
                    languages.forEach { language ->
                        Row(
                            modifier = Modifier
                                .fillMaxWidth()
                                .clickable {
                                    selectedLanguage = language
                                    onLanguageChange(language)
                                    showLanguageDialog = false
                                }
                                .padding(vertical = 12.dp),
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            RadioButton(
                                selected = (language == selectedLanguage),
                                onClick = {
                                    selectedLanguage = language
                                    onLanguageChange(language)
                                    showLanguageDialog = false
                                },
                                colors = RadioButtonDefaults.colors(selectedColor = Green900)
                            )
                            Spacer(modifier = Modifier.width(8.dp))
                            Text(text = language, fontSize = 16.sp, fontWeight = if (language == selectedLanguage) FontWeight.Bold else FontWeight.Normal)
                        }
                    }
                }
            },
            confirmButton = {}
        )
    }

    // 5. Server Settings Dialog
    if (showServerDialog) {
        AlertDialog(
            onDismissRequest = { showServerDialog = false },
            title = { Text("Server Connection (ngrok)", fontWeight = FontWeight.Bold) },
            text = {
                Column(verticalArrangement = Arrangement.spacedBy(12.dp)) {
                    Text("Enter your backend base URL below:", fontSize = 13.sp, color = Gray600)
                    OutlinedTextField(
                        value = serverUrlText,
                        onValueChange = { serverUrlText = it },
                        label = { Text("Server Base URL") },
                        placeholder = { Text("https://xxxx.ngrok-free.dev") },
                        singleLine = true,
                        modifier = Modifier.fillMaxWidth()
                    )
                }
            },
            confirmButton = {
                Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    OutlinedButton(
                        onClick = {
                            serverUrlText = onServerUrlSave(serverUrlText)
                            onTestConnection()
                            showServerDialog = false
                        }
                    ) {
                        Text("Save & Test", color = Green900)
                    }
                    Button(
                        onClick = {
                            serverUrlText = onServerUrlSave(serverUrlText)
                            showServerDialog = false
                        },
                        colors = ButtonDefaults.buttonColors(containerColor = Green900)
                    ) {
                        Text("Save")
                    }
                }
            },
            dismissButton = {
                TextButton(onClick = { showServerDialog = false }) {
                    Text("Cancel", color = Gray600)
                }
            }
        )
    }
}

@Composable
fun SettingRow(
    title: String,
    subtitle: String,
    iconRes: Int,
    badge: String? = null,
    onClick: () -> Unit
) {
    Row(
        modifier = Modifier
            .fillMaxWidth()
            .clickable(onClick = onClick)
            .padding(16.dp),
        verticalAlignment = Alignment.CenterVertically
    ) {
        Icon(
            painter = painterResource(id = iconRes),
            contentDescription = null,
            tint = Blue500,
            modifier = Modifier.size(24.dp)
        )
        Spacer(modifier = Modifier.width(16.dp))
        Column(modifier = Modifier.weight(1f)) {
            Text(text = title, fontWeight = FontWeight.SemiBold, fontSize = 15.sp, color = MaterialTheme.colorScheme.onSurface)
            Text(text = subtitle, fontSize = 12.sp, color = Gray600)
        }
        if (badge != null) {
            Surface(
                color = Blue50,
                shape = RoundedCornerShape(12.dp)
            ) {
                Text(
                    text = badge,
                    color = Blue900,
                    fontSize = 11.sp,
                    fontWeight = FontWeight.Bold,
                    modifier = Modifier.padding(horizontal = 8.dp, vertical = 4.dp)
                )
            }
            Spacer(modifier = Modifier.width(8.dp))
        }
        Icon(
            painter = painterResource(id = R.drawable.ic_chevron_right),
            contentDescription = null,
            tint = Gray400,
            modifier = Modifier.size(20.dp)
        )
    }
}
