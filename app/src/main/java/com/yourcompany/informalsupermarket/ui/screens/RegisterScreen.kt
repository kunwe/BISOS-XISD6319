package com.yourcompany.informalsupermarket.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.foundation.verticalScroll
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.text.input.VisualTransformation
import androidx.compose.ui.text.style.TextDecoration
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.informalsupermarket.R
import com.yourcompany.informalsupermarket.data.Store
import com.yourcompany.informalsupermarket.data.StoreRepository
import com.yourcompany.informalsupermarket.ui.theme.*

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun RegisterScreen(
    modifier: Modifier = Modifier,
    isLoading: Boolean = false,
    onBackClick: () -> Unit = {},
    onRegisterClick: (name: String, email: String, password: String, storeName: String, storeAddress: String, storeTel: String) -> Unit = { _, _, _, _, _, _ -> }
) {
    var firstName by remember { mutableStateOf("") }
    var lastName by remember { mutableStateOf("") }
    var email by remember { mutableStateOf("") }
    var phone by remember { mutableStateOf("") }
    var selectedRole by remember { mutableStateOf("Manager") }

    // Store Managing Mode: "CREATE" vs "JOIN"
    var storeMode by remember { mutableStateOf("CREATE") }

    // Available existing stores
    val allStores by StoreRepository.stores.collectAsState()
    var selectedExistingStore by remember { mutableStateOf(allStores.firstOrNull() ?: Store()) }
    var isStoreDropdownExpanded by remember { mutableStateOf(false) }

    // Store Managing Input Fields (for Create New Store)
    var storeNameInput by remember { mutableStateOf("") }
    var storeAddressInput by remember { mutableStateOf("") }
    var storeTelInput by remember { mutableStateOf("") }

    var password by remember { mutableStateOf("") }
    var confirmPassword by remember { mutableStateOf("") }
    var isPasswordVisible by remember { mutableStateOf(false) }
    var isConfirmPasswordVisible by remember { mutableStateOf(false) }

    var agreeTerms by remember { mutableStateOf(false) }
    val roles = listOf("Manager", "Store Owner", "Clerk", "Cashier")

    Box(
        modifier = modifier
            .fillMaxSize()
            .background(SilverBackground)
    ) {
        Column(
            modifier = Modifier
                .fillMaxSize()
                .verticalScroll(rememberScrollState())
                .padding(horizontal = 20.dp, vertical = 24.dp),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            // Top B Emblem Box
            Surface(
                modifier = Modifier.size(56.dp),
                shape = RoundedCornerShape(14.dp),
                color = Blue900
            ) {
                Box(contentAlignment = Alignment.Center) {
                    Text(
                        text = "B",
                        color = White,
                        fontSize = 28.sp,
                        fontWeight = FontWeight.Bold
                    )
                }
            }

            Spacer(modifier = Modifier.height(12.dp))

            // Title & Subtitle
            Text(
                text = "Create Account",
                fontSize = 24.sp,
                fontWeight = FontWeight.Bold,
                color = Blue900
            )
            Spacer(modifier = Modifier.height(2.dp))
            Text(
                text = "Join BISOS to manage your store",
                fontSize = 12.sp,
                color = Gray600
            )

            Spacer(modifier = Modifier.height(20.dp))

            // 1. First Name & Last Name (Side by side)
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.spacedBy(10.dp)
            ) {
                Column(modifier = Modifier.weight(1f)) {
                    Text("First Name", fontSize = 12.sp, fontWeight = FontWeight.SemiBold, color = Blue900)
                    Spacer(modifier = Modifier.height(4.dp))
                    OutlinedTextField(
                        value = firstName,
                        onValueChange = { firstName = it },
                        placeholder = { Text("First name", fontSize = 13.sp) },
                        leadingIcon = {
                            Icon(painterResource(id = R.drawable.ic_account), contentDescription = null, modifier = Modifier.size(18.dp), tint = Gray600)
                        },
                        singleLine = true,
                        modifier = Modifier.fillMaxWidth(),
                        shape = RoundedCornerShape(10.dp)
                    )
                }
                Column(modifier = Modifier.weight(1f)) {
                    Text("Last Name", fontSize = 12.sp, fontWeight = FontWeight.SemiBold, color = Blue900)
                    Spacer(modifier = Modifier.height(4.dp))
                    OutlinedTextField(
                        value = lastName,
                        onValueChange = { lastName = it },
                        placeholder = { Text("Last name", fontSize = 13.sp) },
                        leadingIcon = {
                            Icon(painterResource(id = R.drawable.ic_account), contentDescription = null, modifier = Modifier.size(18.dp), tint = Gray600)
                        },
                        singleLine = true,
                        modifier = Modifier.fillMaxWidth(),
                        shape = RoundedCornerShape(10.dp)
                    )
                }
            }

            Spacer(modifier = Modifier.height(12.dp))

            // 2. Email Address
            Column(modifier = Modifier.fillMaxWidth()) {
                Text("Email Address", fontSize = 12.sp, fontWeight = FontWeight.SemiBold, color = Blue900)
                Spacer(modifier = Modifier.height(4.dp))
                OutlinedTextField(
                    value = email,
                    onValueChange = { email = it },
                    placeholder = { Text("name@example.com", fontSize = 13.sp) },
                    leadingIcon = {
                        Icon(painterResource(id = R.drawable.ic_mail), contentDescription = null, modifier = Modifier.size(18.dp), tint = Gray600)
                    },
                    singleLine = true,
                    keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Email),
                    modifier = Modifier.fillMaxWidth(),
                    shape = RoundedCornerShape(10.dp)
                )
            }

            Spacer(modifier = Modifier.height(12.dp))

            // 3. Phone Number
            Column(modifier = Modifier.fillMaxWidth()) {
                Text("Phone Number", fontSize = 12.sp, fontWeight = FontWeight.SemiBold, color = Blue900)
                Spacer(modifier = Modifier.height(4.dp))
                OutlinedTextField(
                    value = phone,
                    onValueChange = { phone = it },
                    placeholder = { Text("011 123 4567", fontSize = 13.sp) },
                    leadingIcon = {
                        Icon(painterResource(id = R.drawable.ic_account), contentDescription = null, modifier = Modifier.size(18.dp), tint = Gray600)
                    },
                    singleLine = true,
                    keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Phone),
                    modifier = Modifier.fillMaxWidth(),
                    shape = RoundedCornerShape(10.dp)
                )
            }

            Spacer(modifier = Modifier.height(12.dp))

            // 4. Role Selection FilterChips
            Column(modifier = Modifier.fillMaxWidth()) {
                Text("Role", fontSize = 12.sp, fontWeight = FontWeight.SemiBold, color = Blue900)
                Spacer(modifier = Modifier.height(4.dp))
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.spacedBy(4.dp)
                ) {
                    roles.forEach { role ->
                        FilterChip(
                            selected = (selectedRole == role),
                            onClick = { selectedRole = role },
                            label = { Text(role, fontSize = 11.sp) },
                            colors = FilterChipDefaults.filterChipColors(
                                selectedContainerColor = Blue900,
                                selectedLabelColor = White
                            )
                        )
                    }
                }
            }

            Spacer(modifier = Modifier.height(12.dp))

            // 4b. Store Managing Details (Right Under Role): Join Existing vs Create New
            Column(
                modifier = Modifier.fillMaxWidth(),
                verticalArrangement = Arrangement.spacedBy(8.dp)
            ) {
                Text("Store Managing", fontSize = 12.sp, fontWeight = FontWeight.SemiBold, color = Blue900)

                // Store Mode Selector Chips
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.spacedBy(8.dp)
                ) {
                    FilterChip(
                        selected = (storeMode == "CREATE"),
                        onClick = { storeMode = "CREATE" },
                        label = { Text("Create New Store", fontSize = 11.sp, fontWeight = FontWeight.Bold) },
                        colors = FilterChipDefaults.filterChipColors(
                            selectedContainerColor = Blue900,
                            selectedLabelColor = White
                        )
                    )
                    FilterChip(
                        selected = (storeMode == "JOIN"),
                        onClick = { storeMode = "JOIN" },
                        label = { Text("Join Existing Store", fontSize = 11.sp, fontWeight = FontWeight.Bold) },
                        colors = FilterChipDefaults.filterChipColors(
                            selectedContainerColor = Blue900,
                            selectedLabelColor = White
                        )
                    )
                }

                if (storeMode == "JOIN") {
                    // Dropdown for Existing Stores
                    Box(modifier = Modifier.fillMaxWidth()) {
                        OutlinedCard(
                            modifier = Modifier
                                .fillMaxWidth()
                                .clickable { isStoreDropdownExpanded = true },
                            shape = RoundedCornerShape(10.dp),
                            colors = CardDefaults.outlinedCardColors(containerColor = White)
                        ) {
                            Row(
                                modifier = Modifier
                                    .padding(horizontal = 14.dp, vertical = 14.dp)
                                    .fillMaxWidth(),
                                verticalAlignment = Alignment.CenterVertically,
                                horizontalArrangement = Arrangement.SpaceBetween
                            ) {
                                Row(verticalAlignment = Alignment.CenterVertically) {
                                    Icon(painterResource(id = R.drawable.ic_store), contentDescription = null, modifier = Modifier.size(18.dp), tint = Gray600)
                                    Spacer(modifier = Modifier.width(10.dp))
                                    Text(
                                        text = selectedExistingStore.storeName,
                                        fontSize = 14.sp,
                                        fontWeight = FontWeight.SemiBold,
                                        color = Black
                                    )
                                }
                                Icon(painterResource(id = R.drawable.ic_expand_more), contentDescription = null, modifier = Modifier.size(18.dp), tint = Gray600)
                            }
                        }
                        DropdownMenu(
                            expanded = isStoreDropdownExpanded,
                            onDismissRequest = { isStoreDropdownExpanded = false },
                            modifier = Modifier.fillMaxWidth(0.85f)
                        ) {
                            allStores.forEach { store ->
                                DropdownMenuItem(
                                    text = { Text("${store.storeName} (${store.storeAddress})") },
                                    onClick = {
                                        selectedExistingStore = store
                                        isStoreDropdownExpanded = false
                                    }
                                )
                            }
                        }
                    }
                } else {
                    // Create New Store Fields
                    OutlinedTextField(
                        value = storeNameInput,
                        onValueChange = { storeNameInput = it },
                        placeholder = { Text("Store Name (e.g. Soweto Supermarket)", fontSize = 13.sp) },
                        leadingIcon = {
                            Icon(painterResource(id = R.drawable.ic_store), contentDescription = null, modifier = Modifier.size(18.dp), tint = Gray600)
                        },
                        singleLine = true,
                        modifier = Modifier.fillMaxWidth(),
                        shape = RoundedCornerShape(10.dp)
                    )

                    OutlinedTextField(
                        value = storeAddressInput,
                        onValueChange = { storeAddressInput = it },
                        placeholder = { Text("Store Address (e.g. 123 Soweto St)", fontSize = 13.sp) },
                        leadingIcon = {
                            Icon(painterResource(id = R.drawable.ic_store), contentDescription = null, modifier = Modifier.size(18.dp), tint = Gray600)
                        },
                        singleLine = true,
                        modifier = Modifier.fillMaxWidth(),
                        shape = RoundedCornerShape(10.dp)
                    )

                    OutlinedTextField(
                        value = storeTelInput,
                        onValueChange = { storeTelInput = it },
                        placeholder = { Text("Company Tel Number (e.g. 011 123 4567)", fontSize = 13.sp) },
                        leadingIcon = {
                            Icon(painterResource(id = R.drawable.ic_account), contentDescription = null, modifier = Modifier.size(18.dp), tint = Gray600)
                        },
                        singleLine = true,
                        keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Phone),
                        modifier = Modifier.fillMaxWidth(),
                        shape = RoundedCornerShape(10.dp)
                    )
                }
            }

            Spacer(modifier = Modifier.height(12.dp))

            // 5. Password
            Column(modifier = Modifier.fillMaxWidth()) {
                Text("Password", fontSize = 12.sp, fontWeight = FontWeight.SemiBold, color = Blue900)
                Spacer(modifier = Modifier.height(4.dp))
                OutlinedTextField(
                    value = password,
                    onValueChange = { password = it },
                    placeholder = { Text("Min. 8 characters", fontSize = 13.sp) },
                    leadingIcon = {
                        Icon(painterResource(id = R.drawable.ic_lock), contentDescription = null, modifier = Modifier.size(18.dp), tint = Gray600)
                    },
                    trailingIcon = {
                        IconButton(onClick = { isPasswordVisible = !isPasswordVisible }) {
                            Icon(
                                painter = painterResource(id = R.drawable.ic_visibility),
                                contentDescription = null,
                                modifier = Modifier.size(18.dp),
                                tint = Gray600
                            )
                        }
                    },
                    singleLine = true,
                    visualTransformation = if (isPasswordVisible) VisualTransformation.None else PasswordVisualTransformation(),
                    modifier = Modifier.fillMaxWidth(),
                    shape = RoundedCornerShape(10.dp)
                )
            }

            Spacer(modifier = Modifier.height(12.dp))

            // 6. Confirm Password
            Column(modifier = Modifier.fillMaxWidth()) {
                Text("Confirm Password", fontSize = 12.sp, fontWeight = FontWeight.SemiBold, color = Blue900)
                Spacer(modifier = Modifier.height(4.dp))
                OutlinedTextField(
                    value = confirmPassword,
                    onValueChange = { confirmPassword = it },
                    placeholder = { Text("Re-enter password", fontSize = 13.sp) },
                    leadingIcon = {
                        Icon(painterResource(id = R.drawable.ic_lock), contentDescription = null, modifier = Modifier.size(18.dp), tint = Gray600)
                    },
                    trailingIcon = {
                        IconButton(onClick = { isConfirmPasswordVisible = !isConfirmPasswordVisible }) {
                            Icon(
                                painter = painterResource(id = R.drawable.ic_visibility),
                                contentDescription = null,
                                modifier = Modifier.size(18.dp),
                                tint = Gray600
                            )
                        }
                    },
                    singleLine = true,
                    visualTransformation = if (isConfirmPasswordVisible) VisualTransformation.None else PasswordVisualTransformation(),
                    modifier = Modifier.fillMaxWidth(),
                    shape = RoundedCornerShape(10.dp)
                )
            }

            Spacer(modifier = Modifier.height(12.dp))

            // 7. Terms Checkbox
            Row(
                modifier = Modifier.fillMaxWidth(),
                verticalAlignment = Alignment.CenterVertically
            ) {
                Checkbox(
                    checked = agreeTerms,
                    onCheckedChange = { agreeTerms = it },
                    colors = CheckboxDefaults.colors(checkedColor = Blue900)
                )
                Text(
                    text = "I agree to the ",
                    fontSize = 12.sp,
                    color = Black
                )
                Text(
                    text = "Terms of Service",
                    fontSize = 12.sp,
                    color = Blue500,
                    fontWeight = FontWeight.SemiBold,
                    textDecoration = TextDecoration.Underline,
                    modifier = Modifier.clickable { }
                )
            }

            Spacer(modifier = Modifier.height(16.dp))

            // Primary Button: Create Account
            Button(
                onClick = {
                    val fullName = "$firstName $lastName".trim().ifBlank { "New User" }
                    val sName = if (storeMode == "JOIN") selectedExistingStore.storeName else storeNameInput.trim().ifBlank { "Soweto Supermarket" }
                    val sAddress = if (storeMode == "JOIN") selectedExistingStore.storeAddress else storeAddressInput.trim().ifBlank { "123 Soweto Street, Johannesburg" }
                    val sPhone = if (storeMode == "JOIN") selectedExistingStore.storePhone else storeTelInput.trim().ifBlank { "011-123-4567" }

                    if (storeMode == "CREATE" && storeNameInput.isNotBlank()) {
                        StoreRepository.addStore(
                            Store(
                                storeId = 0,
                                storeName = sName,
                                storeAddress = sAddress,
                                storePhone = sPhone
                            )
                        )
                    } else if (storeMode == "JOIN") {
                        StoreRepository.setActiveStore(selectedExistingStore.storeId)
                    }

                    onRegisterClick(fullName, email.trim(), password.trim(), sName, sAddress, sPhone)
                },
                enabled = !isLoading,
                modifier = Modifier
                    .fillMaxWidth()
                    .height(50.dp),
                colors = ButtonDefaults.buttonColors(containerColor = Blue900),
                shape = RoundedCornerShape(10.dp)
            ) {
                if (isLoading) {
                    CircularProgressIndicator(modifier = Modifier.size(24.dp), color = White, strokeWidth = 2.dp)
                } else {
                    Row(
                        verticalAlignment = Alignment.CenterVertically,
                        horizontalArrangement = Arrangement.Center
                    ) {
                        Icon(painterResource(id = R.drawable.ic_account), contentDescription = null, tint = White, modifier = Modifier.size(18.dp))
                        Spacer(modifier = Modifier.width(8.dp))
                        Text("Create Account", fontSize = 15.sp, fontWeight = FontWeight.Bold, color = White)
                    }
                }
            }

            Spacer(modifier = Modifier.height(18.dp))

            // Footer Link to Sign In
            Row(verticalAlignment = Alignment.CenterVertically) {
                Text("Already have an account? ", fontSize = 12.sp, color = Gray600)
                Text(
                    text = "Sign in",
                    fontSize = 12.sp,
                    color = Blue500,
                    fontWeight = FontWeight.Bold,
                    modifier = Modifier.clickable(onClick = onBackClick)
                )
            }
        }
    }
}
