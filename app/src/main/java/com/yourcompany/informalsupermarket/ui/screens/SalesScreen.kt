package com.yourcompany.informalsupermarket.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.informalsupermarket.R
import com.yourcompany.informalsupermarket.data.SaleItem
import com.yourcompany.informalsupermarket.ui.InformalMartViewModel
import com.yourcompany.informalsupermarket.ui.theme.*

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun SalesScreen(
    viewModel: InformalMartViewModel,
    modifier: Modifier = Modifier,
    onBackClick: () -> Unit = {}
) {
    val report by viewModel.salesReport.collectAsState()
    val products by viewModel.products.collectAsState()

    var showNewSaleDialog by remember { mutableStateOf(false) }
    var selectedItemForVoid by remember { mutableStateOf<SaleItem?>(null) }

    // New sale form state
    var selectedProductId by remember { mutableStateOf(products.firstOrNull()?.id ?: 0) }
    var quantityText by remember { mutableStateOf("1") }
    var paymentMethod by remember { mutableStateOf("CASH") }
    var isDropdownExpanded by remember { mutableStateOf(false) }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Sales & Transactions", color = White, fontWeight = FontWeight.Bold) },
                navigationIcon = {
                    IconButton(onClick = onBackClick) {
                        Icon(painterResource(id = R.drawable.ic_arrow_back), contentDescription = null, tint = White)
                    }
                },
                actions = {
                    IconButton(onClick = { showNewSaleDialog = true }) {
                        Icon(painterResource(id = R.drawable.ic_add), contentDescription = "New Sale", tint = White)
                    }
                },
                colors = TopAppBarDefaults.topAppBarColors(containerColor = Green900)
            )
        },
        floatingActionButton = {
            ExtendedFloatingActionButton(
                onClick = { showNewSaleDialog = true },
                containerColor = Green900,
                contentColor = White,
                icon = { Icon(painterResource(id = R.drawable.ic_add), contentDescription = null) },
                text = { Text("Record New Sale", fontWeight = FontWeight.Bold) }
            )
        },
        modifier = modifier
    ) { padding ->
        LazyColumn(
            modifier = Modifier
                .fillMaxSize()
                .padding(padding)
                .background(SilverBackground)
                .padding(16.dp),
            verticalArrangement = Arrangement.spacedBy(12.dp)
        ) {
            // Header stats
            item {
                Card(
                    colors = CardDefaults.cardColors(containerColor = White),
                    elevation = CardDefaults.cardElevation(defaultElevation = 2.dp),
                    shape = RoundedCornerShape(12.dp),
                    modifier = Modifier.fillMaxWidth()
                ) {
                    Row(
                        modifier = Modifier
                            .fillMaxWidth()
                            .padding(16.dp),
                        horizontalArrangement = Arrangement.SpaceBetween,
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Column {
                            Text("Today's Total Sales", fontSize = 12.sp, color = Gray600)
                            Text("R ${String.format("%.2f", report?.totalSales ?: 0.0)}", fontWeight = FontWeight.Bold, fontSize = 22.sp, color = Green900)
                        }
                        Surface(
                            color = Green50,
                            shape = RoundedCornerShape(8.dp)
                        ) {
                            Text(
                                text = "${report?.transactionCount ?: 0} Sales",
                                color = Green900,
                                fontWeight = FontWeight.Bold,
                                modifier = Modifier.padding(horizontal = 12.dp, vertical = 6.dp)
                            )
                        }
                    }
                }
            }

            item {
                Text("RECENT TRANSACTIONS", fontSize = 12.sp, fontWeight = FontWeight.Bold, color = Gray600)
            }

            val itemsList = report?.items ?: emptyList()
            if (itemsList.isEmpty()) {
                item {
                    Card(
                        colors = CardDefaults.cardColors(containerColor = White),
                        modifier = Modifier.fillMaxWidth().padding(vertical = 16.dp)
                    ) {
                        Box(modifier = Modifier.padding(24.dp).fillMaxWidth(), contentAlignment = Alignment.Center) {
                            Text("No transactions recorded yet.", color = Gray600)
                        }
                    }
                }
            } else {
                items(itemsList) { item ->
                    Card(
                        colors = CardDefaults.cardColors(containerColor = White),
                        elevation = CardDefaults.cardElevation(defaultElevation = 1.dp),
                        shape = RoundedCornerShape(10.dp)
                    ) {
                        SaleRecordRow(
                            item = item,
                            onVoidClick = { selectedItemForVoid = item }
                        )
                    }
                }
            }
        }
    }

    // 1. Record New Sale Dialog
    if (showNewSaleDialog) {
        AlertDialog(
            onDismissRequest = { showNewSaleDialog = false },
            title = { Text("Record New Sale", fontWeight = FontWeight.Bold) },
            text = {
                Column(verticalArrangement = Arrangement.spacedBy(12.dp)) {
                    Text("Select Product:", fontSize = 13.sp, color = Gray600)
                    Box {
                        val currentProduct = products.find { it.id == selectedProductId } ?: products.firstOrNull()
                        OutlinedCard(
                            modifier = Modifier
                                .fillMaxWidth()
                                .clickable { isDropdownExpanded = true },
                            shape = RoundedCornerShape(8.dp)
                        ) {
                            Row(
                                modifier = Modifier.padding(12.dp),
                                horizontalArrangement = Arrangement.SpaceBetween,
                                verticalAlignment = Alignment.CenterVertically
                            ) {
                                Text(
                                    text = currentProduct?.let { "${it.name} (R ${it.unitPrice}) [Stock: ${it.stock?.quantityOnHand ?: 0}]" } ?: "Select Product",
                                    fontSize = 14.sp
                                )
                                Icon(painterResource(id = R.drawable.ic_expand_more), contentDescription = null)
                            }
                        }
                        DropdownMenu(
                            expanded = isDropdownExpanded,
                            onDismissRequest = { isDropdownExpanded = false }
                        ) {
                            products.forEach { prod ->
                                DropdownMenuItem(
                                    text = { Text("${prod.name} - R ${prod.unitPrice} (Stock: ${prod.stock?.quantityOnHand ?: 0})") },
                                    onClick = {
                                        selectedProductId = prod.id
                                        isDropdownExpanded = false
                                    }
                                )
                            }
                        }
                    }

                    OutlinedTextField(
                        value = quantityText,
                        onValueChange = { quantityText = it.filter { c -> c.isDigit() } },
                        label = { Text("Quantity Sold") },
                        singleLine = true,
                        modifier = Modifier.fillMaxWidth()
                    )

                    Text("Payment Method:", fontSize = 13.sp, color = Gray600)
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.spacedBy(8.dp)
                    ) {
                        listOf("CASH", "CARD", "MOBILE").forEach { method ->
                            FilterChip(
                                selected = (paymentMethod == method),
                                onClick = { paymentMethod = method },
                                label = { Text(method) },
                                colors = FilterChipDefaults.filterChipColors(
                                    selectedContainerColor = Green900,
                                    selectedLabelColor = White
                                )
                            )
                        }
                    }
                }
            },
            confirmButton = {
                Button(
                    onClick = {
                        val qty = quantityText.toIntOrNull() ?: 1
                        val prodId = selectedProductId.takeIf { it > 0 } ?: (products.firstOrNull()?.id ?: 1)
                        viewModel.recordSale(prodId, qty, paymentMethod)
                        showNewSaleDialog = false
                    },
                    colors = ButtonDefaults.buttonColors(containerColor = Green900)
                ) {
                    Text("Complete Sale", color = White)
                }
            },
            dismissButton = {
                TextButton(onClick = { showNewSaleDialog = false }) {
                    Text("Cancel", color = Gray600)
                }
            }
        )
    }

    // 2. Void/Delete Sale Confirmation Dialog
    selectedItemForVoid?.let { item ->
        AlertDialog(
            onDismissRequest = { selectedItemForVoid = null },
            title = { Text("Void / Delete Sale", fontWeight = FontWeight.Bold, color = Red700) },
            text = { Text("Are you sure you want to void the sale for \"${item.productName}\" (Qty: ${item.quantity})? The sold items will be automatically restored to stock.") },
            confirmButton = {
                Button(
                    onClick = {
                        viewModel.voidSale(item.transactionId)
                        selectedItemForVoid = null
                    },
                    colors = ButtonDefaults.buttonColors(containerColor = Red700)
                ) {
                    Text("Void & Restore Stock", color = White)
                }
            },
            dismissButton = {
                TextButton(onClick = { selectedItemForVoid = null }) {
                    Text("Cancel", color = Gray600)
                }
            }
        )
    }
}

@Composable
fun SaleRecordRow(
    item: SaleItem,
    onVoidClick: () -> Unit
) {
    Row(
        modifier = Modifier
            .fillMaxWidth()
            .padding(12.dp),
        horizontalArrangement = Arrangement.SpaceBetween,
        verticalAlignment = Alignment.CenterVertically
    ) {
        Column(modifier = Modifier.weight(1f)) {
            Text(text = item.productName, fontWeight = FontWeight.Bold, fontSize = 15.sp, color = Black)
            Text(text = "Qty: ${item.quantity}  •  R ${item.unitPrice} each", color = Gray600, fontSize = 12.sp)
        }
        Text(
            text = "R ${String.format("%.2f", item.total)}",
            fontWeight = FontWeight.Bold,
            color = Green900,
            fontSize = 15.sp
        )
        Spacer(modifier = Modifier.width(8.dp))
        IconButton(onClick = onVoidClick) {
            Icon(
                painter = painterResource(id = R.drawable.ic_close),
                contentDescription = "Void Sale",
                tint = Red700,
                modifier = Modifier.size(20.dp)
            )
        }
    }
}
