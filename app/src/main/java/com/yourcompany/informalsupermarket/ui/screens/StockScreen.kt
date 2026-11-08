package com.yourcompany.informalsupermarket.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.grid.GridCells
import androidx.compose.foundation.lazy.grid.LazyVerticalGrid
import androidx.compose.foundation.lazy.grid.items
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
import com.yourcompany.informalsupermarket.data.Product
import com.yourcompany.informalsupermarket.ui.InformalMartViewModel
import com.yourcompany.informalsupermarket.ui.theme.*
import com.yourcompany.informalsupermarket.util.AiImageGenerator

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun StockScreen(
    viewModel: InformalMartViewModel,
    modifier: Modifier = Modifier,
    onBackClick: () -> Unit = {},
    onAddStockClick: () -> Unit = {}
) {
    val products by viewModel.products.collectAsState()
    var searchQuery by remember { mutableStateOf("") }
    var showBarcodeScannerDialog by remember { mutableStateOf(false) }

    var selectedProductForDelete by remember { mutableStateOf<Product?>(null) }
    var selectedProductForEdit by remember { mutableStateOf<Product?>(null) }

    val filteredProducts = remember(products, searchQuery) {
        if (searchQuery.isBlank()) products
        else products.filter {
            it.name.contains(searchQuery, ignoreCase = true) ||
                    (it.barcode?.contains(searchQuery, ignoreCase = true) == true)
        }
    }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Inventory & Stock", color = White, fontWeight = FontWeight.Bold) },
                navigationIcon = {
                    IconButton(onClick = onBackClick) {
                        Icon(painterResource(id = R.drawable.ic_arrow_back), contentDescription = null, tint = White)
                    }
                },
                actions = {
                    IconButton(onClick = { showBarcodeScannerDialog = true }) {
                        Icon(painterResource(id = R.drawable.ic_filter), contentDescription = "Scan Barcode", tint = White)
                    }
                    IconButton(onClick = onAddStockClick) {
                        Icon(painterResource(id = R.drawable.ic_add), contentDescription = "Add Product", tint = White)
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
                .padding(12.dp)
        ) {
            // Search and Barcode Bar
            OutlinedTextField(
                value = searchQuery,
                onValueChange = { searchQuery = it },
                placeholder = { Text("Search by name or barcode...") },
                leadingIcon = {
                    Icon(painterResource(id = R.drawable.ic_filter), contentDescription = null, tint = Gray600, modifier = Modifier.size(20.dp))
                },
                trailingIcon = {
                    IconButton(onClick = { showBarcodeScannerDialog = true }) {
                        Icon(painterResource(id = R.drawable.ic_filter), contentDescription = "Scan", tint = Blue900, modifier = Modifier.size(20.dp))
                    }
                },
                singleLine = true,
                modifier = Modifier.fillMaxWidth(),
                shape = RoundedCornerShape(12.dp),
                colors = OutlinedTextFieldDefaults.colors(unfocusedContainerColor = White, focusedContainerColor = White)
            )

            Spacer(modifier = Modifier.height(12.dp))

            LazyVerticalGrid(
                columns = GridCells.Fixed(2),
                modifier = Modifier.fillMaxSize(),
                horizontalArrangement = Arrangement.spacedBy(12.dp),
                verticalArrangement = Arrangement.spacedBy(12.dp)
            ) {
                items(filteredProducts) { product ->
                    ProductCard(
                        product = product,
                        onCardClick = { selectedProductForEdit = product },
                        onDeleteClick = { selectedProductForDelete = product }
                    )
                }
            }
        }
    }

    // View & Edit Product Dialog
    selectedProductForEdit?.let { prod ->
        var editName by remember { mutableStateOf(prod.name) }
        var editPrice by remember { mutableStateOf(prod.unitPrice.toString()) }
        var editStock by remember { mutableStateOf((prod.stock?.quantityOnHand ?: 0).toString()) }
        var editReorder by remember { mutableStateOf((prod.reorderLevel).toString()) }
        var editBarcode by remember { mutableStateOf(prod.barcode ?: "") }
        var seedOffset by remember { mutableStateOf(prod.id) }

        var isImageRemoved by remember { mutableStateOf(false) }
        var isCustomPhotoCaptured by remember { mutableStateOf(false) }
        var showCameraDialogForEdit by remember { mutableStateOf(false) }

        AlertDialog(
            onDismissRequest = { selectedProductForEdit = null },
            title = { Text("View & Edit Product", fontWeight = FontWeight.Bold) },
            text = {
                Column(
                    modifier = Modifier
                        .verticalScroll(rememberScrollState())
                        .padding(vertical = 4.dp),
                    verticalArrangement = Arrangement.spacedBy(12.dp)
                ) {
                    // Product Photo Box
                    Card(
                        colors = CardDefaults.cardColors(containerColor = White),
                        shape = RoundedCornerShape(10.dp),
                        elevation = CardDefaults.cardElevation(defaultElevation = 1.dp),
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Column {
                            Box(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .height(130.dp)
                                    .clip(RoundedCornerShape(topStart = 10.dp, topEnd = 10.dp))
                                    .background(Blue50),
                                contentAlignment = Alignment.Center
                            ) {
                                if (isImageRemoved) {
                                    Column(horizontalAlignment = Alignment.CenterHorizontally) {
                                        Icon(painterResource(id = R.drawable.ic_store), contentDescription = null, tint = Blue900, modifier = Modifier.size(40.dp))
                                        Spacer(modifier = Modifier.height(4.dp))
                                        Text("No Image (Default Icon)", fontSize = 12.sp, color = Gray600)
                                    }
                                } else if (isCustomPhotoCaptured) {
                                    Column(horizontalAlignment = Alignment.CenterHorizontally) {
                                        Icon(painterResource(id = R.drawable.ic_stock), contentDescription = null, tint = Blue900, modifier = Modifier.size(40.dp))
                                        Spacer(modifier = Modifier.height(4.dp))
                                        Text("Custom Photo Captured ✓", fontSize = 12.sp, fontWeight = FontWeight.Bold, color = Blue900)
                                    }
                                } else {
                                    AsyncImage(
                                        model = AiImageGenerator.generateAiImageUrl(editName.ifBlank { prod.name }, seedOffset),
                                        contentDescription = null,
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
                                            .padding(6.dp)
                                    ) {
                                        Text(
                                            text = "✨ AI Image",
                                            color = White,
                                            fontSize = 9.sp,
                                            fontWeight = FontWeight.Bold,
                                            modifier = Modifier.padding(horizontal = 6.dp, vertical = 2.dp)
                                        )
                                    }
                                }
                            }

                            // 3 Image Action Options: Regenerate AI, Replace with Photo, Remove Image
                            Row(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .padding(8.dp),
                                horizontalArrangement = Arrangement.spacedBy(4.dp)
                            ) {
                                OutlinedButton(
                                    onClick = {
                                        isImageRemoved = false
                                        isCustomPhotoCaptured = false
                                        seedOffset = (1..99999).random()
                                    },
                                    modifier = Modifier.weight(1f),
                                    shape = RoundedCornerShape(6.dp),
                                    contentPadding = PaddingValues(horizontal = 4.dp, vertical = 4.dp)
                                ) {
                                    Text("✨ Regenerate AI", fontSize = 10.sp, fontWeight = FontWeight.Bold)
                                }

                                OutlinedButton(
                                    onClick = { showCameraDialogForEdit = true },
                                    modifier = Modifier.weight(1f),
                                    shape = RoundedCornerShape(6.dp),
                                    contentPadding = PaddingValues(horizontal = 4.dp, vertical = 4.dp)
                                ) {
                                    Text("📷 Replace Photo", fontSize = 10.sp)
                                }

                                OutlinedButton(
                                    onClick = {
                                        isImageRemoved = true
                                        isCustomPhotoCaptured = false
                                    },
                                    modifier = Modifier.weight(1f),
                                    shape = RoundedCornerShape(6.dp),
                                    colors = ButtonDefaults.outlinedButtonColors(contentColor = Red700),
                                    contentPadding = PaddingValues(horizontal = 4.dp, vertical = 4.dp)
                                ) {
                                    Text("🗑️ Remove", fontSize = 10.sp, fontWeight = FontWeight.Bold)
                                }
                            }
                        }
                    }

                    if (showCameraDialogForEdit) {
                        AlertDialog(
                            onDismissRequest = { showCameraDialogForEdit = false },
                            title = { Text("Capture Custom Photo", fontWeight = FontWeight.Bold) },
                            text = { Text("Take custom item photo with camera or upload from gallery:") },
                            confirmButton = {
                                Button(
                                    onClick = {
                                        isCustomPhotoCaptured = true
                                        isImageRemoved = false
                                        showCameraDialogForEdit = false
                                    },
                                    colors = ButtonDefaults.buttonColors(containerColor = Blue900)
                                ) {
                                    Text("Take Photo", color = White)
                                }
                            },
                            dismissButton = {
                                TextButton(onClick = { showCameraDialogForEdit = false }) {
                                    Text("Cancel", color = Gray600)
                                }
                            }
                        )
                    }

                    OutlinedTextField(
                        value = editName,
                        onValueChange = { editName = it },
                        label = { Text("Product Name") },
                        singleLine = true,
                        modifier = Modifier.fillMaxWidth()
                    )

                    OutlinedTextField(
                        value = editPrice,
                        onValueChange = { editPrice = it },
                        label = { Text("Selling Price (R)") },
                        keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Decimal),
                        singleLine = true,
                        modifier = Modifier.fillMaxWidth()
                    )

                    OutlinedTextField(
                        value = editStock,
                        onValueChange = { editStock = it },
                        label = { Text("Stock Quantity") },
                        keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Number),
                        singleLine = true,
                        modifier = Modifier.fillMaxWidth()
                    )

                    OutlinedTextField(
                        value = editReorder,
                        onValueChange = { editReorder = it },
                        label = { Text("Low Stock Threshold Level") },
                        keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Number),
                        singleLine = true,
                        modifier = Modifier.fillMaxWidth()
                    )

                    OutlinedTextField(
                        value = editBarcode,
                        onValueChange = { editBarcode = it },
                        label = { Text("Barcode Number") },
                        trailingIcon = {
                            IconButton(onClick = { editBarcode = "600" + (1000000000..9999999999).random() }) {
                                Icon(painterResource(id = R.drawable.ic_filter), contentDescription = "Generate Barcode", tint = Blue900)
                            }
                        },
                        singleLine = true,
                        modifier = Modifier.fillMaxWidth()
                    )
                }
            },
            confirmButton = {
                Button(
                    onClick = {
                        val p = editPrice.toDoubleOrNull() ?: prod.unitPrice
                        val s = editStock.toIntOrNull() ?: (prod.stock?.quantityOnHand ?: 0)
                        val r = editReorder.toIntOrNull() ?: prod.reorderLevel
                        val nameToSave = editName.trim().ifBlank { prod.name }

                        viewModel.updateProduct(prod.id, nameToSave, p, s, r, editBarcode)
                        selectedProductForEdit = null
                    },
                    colors = ButtonDefaults.buttonColors(containerColor = Blue900)
                ) {
                    Text("Save Changes", color = White)
                }
            },
            dismissButton = {
                TextButton(onClick = { selectedProductForEdit = null }) {
                    Text("Cancel", color = Gray600)
                }
            }
        )
    }

    // Barcode Scanner Dialog
    if (showBarcodeScannerDialog) {
        AlertDialog(
            onDismissRequest = { showBarcodeScannerDialog = false },
            title = { Text("Barcode Scanner", fontWeight = FontWeight.Bold) },
            text = {
                Column(
                    horizontalAlignment = Alignment.CenterHorizontally,
                    verticalArrangement = Arrangement.spacedBy(12.dp)
                ) {
                    Icon(painterResource(id = R.drawable.ic_filter), contentDescription = null, tint = Blue900, modifier = Modifier.size(48.dp))
                    Text("Align barcode inside camera view finder or select sample barcode below:", fontSize = 13.sp, color = Gray600)

                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.spacedBy(8.dp)
                    ) {
                        Button(
                            onClick = {
                                searchQuery = "6001234567890"
                                showBarcodeScannerDialog = false
                            },
                            modifier = Modifier.weight(1f),
                            colors = ButtonDefaults.buttonColors(containerColor = Blue900)
                        ) {
                            Text("Milk (600123...)", fontSize = 11.sp)
                        }
                        Button(
                            onClick = {
                                searchQuery = "6001234567891"
                                showBarcodeScannerDialog = false
                            },
                            modifier = Modifier.weight(1f),
                            colors = ButtonDefaults.buttonColors(containerColor = Blue900)
                        ) {
                            Text("Bread (600123...)", fontSize = 11.sp)
                        }
                    }
                }
            },
            confirmButton = {
                TextButton(onClick = { showBarcodeScannerDialog = false }) {
                    Text("Close")
                }
            }
        )
    }

    // Delete Product Confirmation Dialog
    selectedProductForDelete?.let { prod ->
        AlertDialog(
            onDismissRequest = { selectedProductForDelete = null },
            title = { Text("Delete Product", fontWeight = FontWeight.Bold, color = Red700) },
            text = { Text("Are you sure you want to delete \"${prod.name}\" from stock?") },
            confirmButton = {
                Button(
                    onClick = {
                        viewModel.deleteProduct(prod.id)
                        selectedProductForDelete = null
                    },
                    colors = ButtonDefaults.buttonColors(containerColor = Red700)
                ) {
                    Text("Delete", color = White)
                }
            },
            dismissButton = {
                TextButton(onClick = { selectedProductForDelete = null }) {
                    Text("Cancel", color = Gray600)
                }
            }
        )
    }
}

@Composable
fun ProductCard(
    product: Product,
    onCardClick: () -> Unit,
    onDeleteClick: () -> Unit
) {
    Card(
        onClick = onCardClick,
        modifier = Modifier.fillMaxWidth(),
        shape = RoundedCornerShape(12.dp),
        colors = CardDefaults.cardColors(containerColor = White),
        elevation = CardDefaults.cardElevation(2.dp)
    ) {
        Column(modifier = Modifier.padding(8.dp)) {
            Box(
                modifier = Modifier
                    .fillMaxWidth()
                    .height(110.dp)
                    .clip(RoundedCornerShape(8.dp))
                    .background(Blue50),
                contentAlignment = Alignment.Center
            ) {
                // Default Stock Icon fallback when offline
                Icon(
                    painter = painterResource(id = R.drawable.ic_store),
                    contentDescription = "Default Stock Icon",
                    tint = Blue900,
                    modifier = Modifier.size(48.dp)
                )
                AsyncImage(
                    model = AiImageGenerator.generateAiImageUrl(product.name, product.id),
                    contentDescription = null,
                    modifier = Modifier.fillMaxSize(),
                    contentScale = ContentScale.Crop,
                    placeholder = painterResource(id = R.drawable.ic_store),
                    error = painterResource(id = R.drawable.ic_store)
                )

                // AI Image Badge
                Surface(
                    color = Blue900.copy(alpha = 0.85f),
                    shape = RoundedCornerShape(8.dp),
                    modifier = Modifier
                        .align(Alignment.TopStart)
                        .padding(6.dp)
                ) {
                    Text(
                        text = "✨ AI Image",
                        color = White,
                        fontSize = 9.sp,
                        fontWeight = FontWeight.Bold,
                        modifier = Modifier.padding(horizontal = 6.dp, vertical = 2.dp)
                    )
                }

                IconButton(
                    onClick = onDeleteClick,
                    modifier = Modifier
                        .align(Alignment.TopEnd)
                        .size(32.dp)
                        .background(White.copy(alpha = 0.85f), RoundedCornerShape(16.dp))
                ) {
                    Icon(
                        painter = painterResource(id = R.drawable.ic_close),
                        contentDescription = "Delete Product",
                        tint = Red700,
                        modifier = Modifier.size(16.dp)
                    )
                }
            }
            Spacer(modifier = Modifier.height(8.dp))
            Text(text = product.name, fontWeight = FontWeight.Bold, fontSize = 14.sp, maxLines = 1)
            Text(text = "R ${String.format("%.2f", product.unitPrice)}", color = Green700, fontSize = 12.sp)
            Spacer(modifier = Modifier.height(4.dp))
            val stockLevel = product.stock?.quantityOnHand ?: 0
            val isLow = stockLevel < 50
            Text(
                text = "Stock: $stockLevel",
                color = if (isLow) Red700 else Black,
                fontWeight = if (isLow) FontWeight.Bold else FontWeight.Normal,
                fontSize = 12.sp
            )
        }
    }
}
