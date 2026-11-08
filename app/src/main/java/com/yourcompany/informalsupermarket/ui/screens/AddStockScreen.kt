package com.yourcompany.informalsupermarket.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.border
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
import androidx.compose.ui.draw.clip
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import coil.compose.AsyncImage
import com.example.informalsupermarket.R
import com.yourcompany.informalsupermarket.ui.theme.*
import com.yourcompany.informalsupermarket.util.AiImageGenerator

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun AddStockScreen(
    modifier: Modifier = Modifier,
    onBackClick: () -> Unit = {},
    onAddProduct: (String, Double, Int, Int) -> Unit = { _, _, _, _ -> }
) {
    var name by remember { mutableStateOf("") }
    var price by remember { mutableStateOf("") }
    var stock by remember { mutableStateOf("") }
    var reorder by remember { mutableStateOf("50") }
    var barcode by remember { mutableStateOf("") }

    var itemPhotoCaptured by remember { mutableStateOf(false) }
    var aiGeneratedPhotoUrl by remember { mutableStateOf<String?>(null) }
    var isGeneratingAiPhoto by remember { mutableStateOf(false) }
    var showCameraDialog by remember { mutableStateOf(false) }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Add New Product", color = White, fontWeight = FontWeight.Bold) },
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
                .background(SilverBackground)
                .verticalScroll(rememberScrollState())
                .padding(24.dp),
            verticalArrangement = Arrangement.spacedBy(16.dp)
        ) {
            // Item Photo Picker & AI Generator Box
            Card(
                colors = CardDefaults.cardColors(containerColor = White),
                shape = RoundedCornerShape(12.dp),
                elevation = CardDefaults.cardElevation(defaultElevation = 2.dp),
                modifier = Modifier.fillMaxWidth()
            ) {
                Box(
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(150.dp)
                        .clip(RoundedCornerShape(12.dp))
                        .background(Blue50),
                    contentAlignment = Alignment.Center
                ) {
                    if (aiGeneratedPhotoUrl != null) {
                        AsyncImage(
                            model = aiGeneratedPhotoUrl,
                            contentDescription = "AI Generated Stock Image",
                            modifier = Modifier.fillMaxSize(),
                            contentScale = ContentScale.Crop,
                            placeholder = painterResource(id = R.drawable.ic_store),
                            error = painterResource(id = R.drawable.ic_store)
                        )
                        Surface(
                            color = Blue900.copy(alpha = 0.85f),
                            shape = RoundedCornerShape(8.dp),
                            modifier = Modifier
                                .align(Alignment.TopStart)
                                .padding(8.dp)
                        ) {
                            Text(
                                text = "✨ AI Generated Image",
                                color = White,
                                fontSize = 11.sp,
                                fontWeight = FontWeight.Bold,
                                modifier = Modifier.padding(horizontal = 8.dp, vertical = 4.dp)
                            )
                        }
                    } else if (itemPhotoCaptured) {
                        Column(horizontalAlignment = Alignment.CenterHorizontally) {
                            Icon(painterResource(id = R.drawable.ic_stock), contentDescription = null, tint = Blue900, modifier = Modifier.size(48.dp))
                            Spacer(modifier = Modifier.height(8.dp))
                            Text("Item Camera Photo Captured ✓", color = Blue900, fontWeight = FontWeight.Bold, fontSize = 14.sp)
                        }
                    } else {
                        Column(
                            horizontalAlignment = Alignment.CenterHorizontally,
                            verticalArrangement = Arrangement.Center,
                            modifier = Modifier.padding(16.dp)
                        ) {
                            Icon(painterResource(id = R.drawable.ic_store), contentDescription = null, tint = Blue900, modifier = Modifier.size(40.dp))
                            Spacer(modifier = Modifier.height(6.dp))
                            Text("No Photo Uploaded", color = Gray600, fontSize = 13.sp)
                        }
                    }
                }

                Row(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(10.dp),
                    horizontalArrangement = Arrangement.spacedBy(6.dp)
                ) {
                    Button(
                        onClick = {
                            val promptName = name.ifBlank { "Supermarket Product" }
                            isGeneratingAiPhoto = true
                            itemPhotoCaptured = false
                            aiGeneratedPhotoUrl = AiImageGenerator.generateAiImageUrl(promptName, (1..99999).random())
                            isGeneratingAiPhoto = false
                        },
                        modifier = Modifier.weight(1.2f),
                        colors = ButtonDefaults.buttonColors(containerColor = Blue900),
                        shape = RoundedCornerShape(8.dp),
                        contentPadding = PaddingValues(horizontal = 6.dp, vertical = 6.dp)
                    ) {
                        if (isGeneratingAiPhoto) {
                            CircularProgressIndicator(modifier = Modifier.size(16.dp), color = White, strokeWidth = 2.dp)
                        } else {
                            Text("✨ Regenerate AI", fontSize = 11.sp, fontWeight = FontWeight.Bold)
                        }
                    }

                    OutlinedButton(
                        onClick = { showCameraDialog = true },
                        modifier = Modifier.weight(1f),
                        shape = RoundedCornerShape(8.dp),
                        contentPadding = PaddingValues(horizontal = 6.dp, vertical = 6.dp)
                    ) {
                        Text("📷 Replace", fontSize = 11.sp)
                    }

                    OutlinedButton(
                        onClick = {
                            aiGeneratedPhotoUrl = null
                            itemPhotoCaptured = false
                        },
                        modifier = Modifier.weight(1f),
                        shape = RoundedCornerShape(8.dp),
                        colors = ButtonDefaults.outlinedButtonColors(contentColor = Red700),
                        contentPadding = PaddingValues(horizontal = 6.dp, vertical = 6.dp)
                    ) {
                        Text("🗑️ Remove", fontSize = 11.sp, fontWeight = FontWeight.Bold)
                    }
                }
            }

            OutlinedTextField(
                value = name,
                onValueChange = { name = it },
                label = { Text("Product Name") },
                placeholder = { Text("e.g. Milk 2L") },
                singleLine = true,
                modifier = Modifier.fillMaxWidth(),
                shape = RoundedCornerShape(12.dp),
                colors = OutlinedTextFieldDefaults.colors(unfocusedContainerColor = White, focusedContainerColor = White)
            )

            // Barcode Field with Scan Button
            OutlinedTextField(
                value = barcode,
                onValueChange = { barcode = it },
                label = { Text("Barcode Number") },
                placeholder = { Text("6001234567890") },
                trailingIcon = {
                    IconButton(onClick = { barcode = "600" + (1000000000..9999999999).random() }) {
                        Icon(painterResource(id = R.drawable.ic_filter), contentDescription = "Scan Barcode", tint = Blue900)
                    }
                },
                singleLine = true,
                keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Number),
                modifier = Modifier.fillMaxWidth(),
                shape = RoundedCornerShape(12.dp),
                colors = OutlinedTextFieldDefaults.colors(unfocusedContainerColor = White, focusedContainerColor = White)
            )

            OutlinedTextField(
                value = price,
                onValueChange = { price = it },
                label = { Text("Selling Price (R)") },
                placeholder = { Text("24.99") },
                singleLine = true,
                modifier = Modifier.fillMaxWidth(),
                keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Decimal),
                shape = RoundedCornerShape(12.dp),
                colors = OutlinedTextFieldDefaults.colors(unfocusedContainerColor = White, focusedContainerColor = White)
            )

            OutlinedTextField(
                value = stock,
                onValueChange = { stock = it },
                label = { Text("Initial Stock Quantity") },
                placeholder = { Text("50") },
                singleLine = true,
                modifier = Modifier.fillMaxWidth(),
                keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Number),
                shape = RoundedCornerShape(12.dp),
                colors = OutlinedTextFieldDefaults.colors(unfocusedContainerColor = White, focusedContainerColor = White)
            )

            OutlinedTextField(
                value = reorder,
                onValueChange = { reorder = it },
                label = { Text("Low Stock Threshold Level") },
                supportingText = { Text("Alerts trigger when stock drops below 50") },
                singleLine = true,
                modifier = Modifier.fillMaxWidth(),
                keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Number),
                shape = RoundedCornerShape(12.dp),
                colors = OutlinedTextFieldDefaults.colors(unfocusedContainerColor = White, focusedContainerColor = White)
            )

            Spacer(modifier = Modifier.height(16.dp))

            Button(
                onClick = {
                    val p = price.toDoubleOrNull() ?: 0.0
                    val s = stock.toIntOrNull() ?: 0
                    val r = reorder.toIntOrNull() ?: 50
                    if (name.isNotEmpty()) {
                        onAddProduct(name, p, s, r)
                    }
                },
                modifier = Modifier
                    .fillMaxWidth()
                    .height(52.dp),
                colors = ButtonDefaults.buttonColors(containerColor = Blue900),
                shape = RoundedCornerShape(10.dp)
            ) {
                Text("Save Product to Inventory", fontSize = 16.sp, fontWeight = FontWeight.Bold, color = White)
            }
        }
    }

    // Camera Capture Simulation Dialog
    if (showCameraDialog) {
        AlertDialog(
            onDismissRequest = { showCameraDialog = false },
            title = { Text("Camera & Photo Capture", fontWeight = FontWeight.Bold) },
            text = {
                Column(
                    horizontalAlignment = Alignment.CenterHorizontally,
                    verticalArrangement = Arrangement.spacedBy(12.dp)
                ) {
                    Icon(painterResource(id = R.drawable.ic_add), contentDescription = null, tint = Blue900, modifier = Modifier.size(48.dp))
                    Text("Capture item photo or select from device gallery:", fontSize = 13.sp, color = Gray600)
                }
            },
            confirmButton = {
                Button(
                    onClick = {
                        itemPhotoCaptured = true
                        aiGeneratedPhotoUrl = null
                        showCameraDialog = false
                    },
                    colors = ButtonDefaults.buttonColors(containerColor = Blue900)
                ) {
                    Text("Take Photo", color = White)
                }
            },
            dismissButton = {
                TextButton(onClick = { showCameraDialog = false }) {
                    Text("Cancel", color = Gray600)
                }
            }
        )
    }
}
