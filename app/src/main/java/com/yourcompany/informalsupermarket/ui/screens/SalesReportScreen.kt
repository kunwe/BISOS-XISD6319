package com.yourcompany.informalsupermarket.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.horizontalScroll
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.informalsupermarket.R
import com.yourcompany.informalsupermarket.data.SaleItem
import com.yourcompany.informalsupermarket.ui.InformalMartViewModel
import com.yourcompany.informalsupermarket.ui.theme.*

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun SalesReportScreen(
    viewModel: InformalMartViewModel,
    modifier: Modifier = Modifier,
    onBackClick: () -> Unit = {}
) {
    val report by viewModel.salesReport.collectAsState()

    var selectedPeriodTab by remember { mutableStateOf("Monthly") }
    val periodTabs = listOf("Daily", "Weekly", "Monthly", "Custom Range")

    var fromDate by remember { mutableStateOf("03/01/2026") }
    var toDate by remember { mutableStateOf("03/31/2026") }

    var selectedStoreFilter by remember { mutableStateOf("All Stores") }
    var isStoreDropdownExpanded by remember { mutableStateOf(false) }
    val stores = listOf("All Stores", "Soweto Branch", "Sandton Branch", "Pretoria Branch")

    var selectedPaymentFilter by remember { mutableStateOf("All Payment Methods") }
    var isPaymentDropdownExpanded by remember { mutableStateOf(false) }
    val paymentMethods = listOf("All Payment Methods", "CASH", "CARD", "MOBILE")

    Scaffold(
        topBar = {
            TopAppBar(
                title = {
                    Column {
                        Text("Sales Report", color = White, fontWeight = FontWeight.Bold, fontSize = 20.sp)
                        Text("${report?.date ?: "March 2026"} — ${report?.branch ?: "Soweto Branch"}", color = Gray200, fontSize = 12.sp)
                    }
                },
                navigationIcon = {
                    IconButton(onClick = onBackClick) {
                        Icon(painterResource(id = R.drawable.ic_arrow_back), contentDescription = null, tint = White)
                    }
                },
                colors = TopAppBarDefaults.topAppBarColors(containerColor = Blue900)
            )
        },
        modifier = modifier
    ) { paddingValues ->
        LazyColumn(
            modifier = Modifier
                .fillMaxSize()
                .padding(paddingValues)
                .background(SilverBackground)
                .padding(16.dp),
            verticalArrangement = Arrangement.spacedBy(16.dp)
        ) {
            // 1. Export Actions Row (PDF & CSV)
            item {
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.End,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Button(
                        onClick = {},
                        colors = ButtonDefaults.buttonColors(containerColor = Red700),
                        shape = RoundedCornerShape(8.dp),
                        contentPadding = PaddingValues(horizontal = 14.dp, vertical = 8.dp)
                    ) {
                        Icon(painterResource(id = R.drawable.ic_reports), contentDescription = null, tint = White, modifier = Modifier.size(16.dp))
                        Spacer(modifier = Modifier.width(6.dp))
                        Text("Export PDF", fontSize = 13.sp, fontWeight = FontWeight.Bold)
                    }

                    Spacer(modifier = Modifier.width(10.dp))

                    Button(
                        onClick = {},
                        colors = ButtonDefaults.buttonColors(containerColor = Color(0xFF2E7D32)),
                        shape = RoundedCornerShape(8.dp),
                        contentPadding = PaddingValues(horizontal = 14.dp, vertical = 8.dp)
                    ) {
                        Icon(painterResource(id = R.drawable.ic_reports), contentDescription = null, tint = White, modifier = Modifier.size(16.dp))
                        Spacer(modifier = Modifier.width(6.dp))
                        Text("Export CSV", fontSize = 13.sp, fontWeight = FontWeight.Bold)
                    }
                }
            }

            // 2. Period Tabs
            item {
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.spacedBy(8.dp)
                ) {
                    periodTabs.forEach { tab ->
                        val isSelected = (selectedPeriodTab == tab)
                        Surface(
                            onClick = { selectedPeriodTab = tab },
                            shape = RoundedCornerShape(8.dp),
                            color = if (isSelected) White else Color.Transparent,
                            shadowElevation = if (isSelected) 2.dp else 0.dp
                        ) {
                            Text(
                                text = tab,
                                color = if (isSelected) Blue900 else Gray700,
                                fontWeight = if (isSelected) FontWeight.Bold else FontWeight.Normal,
                                fontSize = 13.sp,
                                modifier = Modifier.padding(horizontal = 16.dp, vertical = 10.dp)
                            )
                        }
                    }
                }
            }

            // 3. Filters Control Card (Dates, Store, Payment Method)
            item {
                Card(
                    colors = CardDefaults.cardColors(containerColor = White),
                    shape = RoundedCornerShape(12.dp),
                    elevation = CardDefaults.cardElevation(2.dp),
                    modifier = Modifier.fillMaxWidth()
                ) {
                    Column(
                        modifier = Modifier.padding(16.dp),
                        verticalArrangement = Arrangement.spacedBy(12.dp)
                    ) {
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            verticalAlignment = Alignment.CenterVertically,
                            horizontalArrangement = Arrangement.spacedBy(10.dp)
                        ) {
                            Text("From:", fontSize = 13.sp, fontWeight = FontWeight.Bold, color = Gray700)
                            OutlinedCard(
                                shape = RoundedCornerShape(8.dp),
                                modifier = Modifier.weight(1f)
                            ) {
                                Row(
                                    modifier = Modifier.padding(10.dp),
                                    verticalAlignment = Alignment.CenterVertically
                                ) {
                                    Text(fromDate, fontSize = 13.sp, modifier = Modifier.weight(1f))
                                    Icon(painterResource(id = R.drawable.ic_calendar), contentDescription = null, tint = Gray600, modifier = Modifier.size(16.dp))
                                }
                            }

                            Text("To:", fontSize = 13.sp, fontWeight = FontWeight.Bold, color = Gray700)
                            OutlinedCard(
                                shape = RoundedCornerShape(8.dp),
                                modifier = Modifier.weight(1f)
                            ) {
                                Row(
                                    modifier = Modifier.padding(10.dp),
                                    verticalAlignment = Alignment.CenterVertically
                                ) {
                                    Text(toDate, fontSize = 13.sp, modifier = Modifier.weight(1f))
                                    Icon(painterResource(id = R.drawable.ic_calendar), contentDescription = null, tint = Gray600, modifier = Modifier.size(16.dp))
                                }
                            }
                        }

                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.spacedBy(10.dp)
                        ) {
                            // Store Dropdown
                            Box(modifier = Modifier.weight(1f)) {
                                OutlinedCard(
                                    modifier = Modifier
                                        .fillMaxWidth()
                                        .clickable { isStoreDropdownExpanded = true },
                                    shape = RoundedCornerShape(8.dp)
                                ) {
                                    Row(
                                        modifier = Modifier.padding(10.dp),
                                        horizontalArrangement = Arrangement.SpaceBetween,
                                        verticalAlignment = Alignment.CenterVertically
                                    ) {
                                        Text(selectedStoreFilter, fontSize = 12.sp, maxLines = 1)
                                        Icon(painterResource(id = R.drawable.ic_expand_more), contentDescription = null, modifier = Modifier.size(16.dp))
                                    }
                                }
                                DropdownMenu(
                                    expanded = isStoreDropdownExpanded,
                                    onDismissRequest = { isStoreDropdownExpanded = false }
                                ) {
                                    stores.forEach { store ->
                                        DropdownMenuItem(
                                            text = { Text(store, fontSize = 13.sp) },
                                            onClick = {
                                                selectedStoreFilter = store
                                                isStoreDropdownExpanded = false
                                            }
                                        )
                                    }
                                }
                            }

                            // Payment Dropdown
                            Box(modifier = Modifier.weight(1f)) {
                                OutlinedCard(
                                    modifier = Modifier
                                        .fillMaxWidth()
                                        .clickable { isPaymentDropdownExpanded = true },
                                    shape = RoundedCornerShape(8.dp)
                                ) {
                                    Row(
                                        modifier = Modifier.padding(10.dp),
                                        horizontalArrangement = Arrangement.SpaceBetween,
                                        verticalAlignment = Alignment.CenterVertically
                                    ) {
                                        Text(selectedPaymentFilter, fontSize = 12.sp, maxLines = 1)
                                        Icon(painterResource(id = R.drawable.ic_expand_more), contentDescription = null, modifier = Modifier.size(16.dp))
                                    }
                                }
                                DropdownMenu(
                                    expanded = isPaymentDropdownExpanded,
                                    onDismissRequest = { isPaymentDropdownExpanded = false }
                                ) {
                                    paymentMethods.forEach { method ->
                                        DropdownMenuItem(
                                            text = { Text(method, fontSize = 13.sp) },
                                            onClick = {
                                                selectedPaymentFilter = method
                                                isPaymentDropdownExpanded = false
                                            }
                                        )
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // 4. Key Summary Cards Row 1 (Total Sales, Transactions, Avg Transaction)
            item {
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.spacedBy(10.dp)
                ) {
                    MetricCard(
                        title = "TOTAL SALES",
                        value = "R ${String.format("%.2f", report?.totalSales ?: 784.85)}",
                        subtitle = "All transactions",
                        modifier = Modifier.weight(1f)
                    )
                    MetricCard(
                        title = "TRANSACTIONS",
                        value = "${report?.transactionCount ?: 4}",
                        subtitle = "This period",
                        modifier = Modifier.weight(1f)
                    )
                    MetricCard(
                        title = "AVG. TRANSACTION",
                        value = "R ${String.format("%.2f", report?.averageSale ?: 196.21)}",
                        subtitle = "Per sale",
                        modifier = Modifier.weight(1f)
                    )
                }
            }

            // 5. Payment Method Breakdown Cards Row 2 (Cash, Card, Mobile)
            item {
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.spacedBy(10.dp)
                ) {
                    PaymentMethodCard(
                        title = "CASH",
                        amount = "R ${String.format("%.2f", report?.cashTotal ?: 459.88)}",
                        iconRes = R.drawable.ic_stock,
                        iconBg = Color(0xFFE8F5E9),
                        iconTint = Color(0xFF2E7D32),
                        modifier = Modifier.weight(1f)
                    )
                    PaymentMethodCard(
                        title = "CARD",
                        amount = "R ${String.format("%.2f", report?.cardTotal ?: 148.00)}",
                        iconRes = R.drawable.ic_sales,
                        iconBg = Color(0xFFE3F2FD),
                        iconTint = Color(0xFF1565C0),
                        modifier = Modifier.weight(1f)
                    )
                    PaymentMethodCard(
                        title = "MOBILE",
                        amount = "R ${String.format("%.2f", report?.mobileTotal ?: 160.00)}",
                        iconRes = R.drawable.ic_notifications,
                        iconBg = Color(0xFFFFF3E0),
                        iconTint = Color(0xFFE65100),
                        modifier = Modifier.weight(1f)
                    )
                }
            }

            // 6. Detailed Transaction Table Card
            item {
                Card(
                    colors = CardDefaults.cardColors(containerColor = White),
                    shape = RoundedCornerShape(12.dp),
                    elevation = CardDefaults.cardElevation(2.dp),
                    modifier = Modifier.fillMaxWidth()
                ) {
                    Column {
                        // Scrollable Table Header
                        Row(
                            modifier = Modifier
                                .fillMaxWidth()
                                .background(Gray100)
                                .padding(horizontal = 12.dp, vertical = 10.dp),
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Text("TXN ID", modifier = Modifier.weight(1.1f), fontWeight = FontWeight.Bold, fontSize = 11.sp, color = Gray700)
                            Text("DATE", modifier = Modifier.weight(1.2f), fontWeight = FontWeight.Bold, fontSize = 11.sp, color = Gray700)
                            Text("PRODUCT", modifier = Modifier.weight(1.5f), fontWeight = FontWeight.Bold, fontSize = 11.sp, color = Gray700)
                            Text("QTY", modifier = Modifier.weight(0.6f), fontWeight = FontWeight.Bold, fontSize = 11.sp, color = Gray700, textAlign = TextAlign.Center)
                            Text("PRICE", modifier = Modifier.weight(1f), fontWeight = FontWeight.Bold, fontSize = 11.sp, color = Gray700, textAlign = TextAlign.End)
                            Text("TOTAL", modifier = Modifier.weight(1.1f), fontWeight = FontWeight.Bold, fontSize = 11.sp, color = Gray700, textAlign = TextAlign.End)
                            Text("PAYMENT", modifier = Modifier.weight(1.1f), fontWeight = FontWeight.Bold, fontSize = 11.sp, color = Gray700, textAlign = TextAlign.Center)
                        }

                        HorizontalDivider(color = Gray200)

                        val rawItems = report?.items ?: emptyList()
                        val displayItems = if (rawItems.isNotEmpty()) rawItems else listOf(
                            SaleItem(1, "A-4521", "2026-03-01", "Milk 2L", 12, 24.99, 299.88, "CASH", "Soweto"),
                            SaleItem(2, "A-4522", "2026-03-01", "Bread 700g", 8, 18.50, 148.00, "CARD", "Soweto"),
                            SaleItem(3, "A-4523", "2026-03-02", "Sugar 1kg", 5, 32.00, 160.00, "CASH", "Soweto"),
                            SaleItem(4, "A-4524", "2026-03-02", "Cooking Oil", 3, 58.99, 176.97, "CARD", "Soweto")
                        )

                        displayItems.forEach { item ->
                            Row(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .padding(horizontal = 12.dp, vertical = 10.dp),
                                verticalAlignment = Alignment.CenterVertically
                            ) {
                                Text(item.transactionRef, modifier = Modifier.weight(1.1f), fontSize = 11.sp, fontWeight = FontWeight.Medium)
                                Text(item.date, modifier = Modifier.weight(1.2f), fontSize = 11.sp, color = Gray600)
                                Text(item.productName, modifier = Modifier.weight(1.5f), fontSize = 11.sp, fontWeight = FontWeight.SemiBold)
                                Text("${item.quantity}", modifier = Modifier.weight(0.6f), fontSize = 11.sp, textAlign = TextAlign.Center)
                                Text("R${String.format("%.2f", item.unitPrice)}", modifier = Modifier.weight(1f), fontSize = 11.sp, textAlign = TextAlign.End)
                                Text("R${String.format("%.2f", item.total)}", modifier = Modifier.weight(1.1f), fontSize = 11.sp, fontWeight = FontWeight.Bold, textAlign = TextAlign.End)

                                // Payment Pill Badge
                                Box(
                                    modifier = Modifier.weight(1.1f),
                                    contentAlignment = Alignment.Center
                                ) {
                                    val pm = item.paymentMethod.uppercase()
                                    val (badgeBg, badgeFg) = when (pm) {
                                        "CARD" -> Pair(Color(0xFFE3F2FD), Color(0xFF1565C0))
                                        "MOBILE" -> Pair(Color(0xFFFFF3E0), Color(0xFFE65100))
                                        else -> Pair(Color(0xFFE8F5E9), Color(0xFF2E7D32))
                                    }
                                    Surface(
                                        color = badgeBg,
                                        shape = RoundedCornerShape(12.dp)
                                    ) {
                                        Text(
                                            text = pm.lowercase().replaceFirstChar { it.uppercase() },
                                            color = badgeFg,
                                            fontSize = 10.sp,
                                            fontWeight = FontWeight.Bold,
                                            modifier = Modifier.padding(horizontal = 8.dp, vertical = 2.dp)
                                        )
                                    }
                                }
                            }
                            HorizontalDivider(color = Gray200)
                        }

                        // Table Footer
                        Row(
                            modifier = Modifier
                                .fillMaxWidth()
                                .padding(14.dp),
                            horizontalArrangement = Arrangement.End,
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Text("Total Sales:  ", fontWeight = FontWeight.Bold, fontSize = 14.sp, color = Gray700)
                            Text(
                                text = "R ${String.format("%.2f", report?.totalSales ?: 784.85)}",
                                fontWeight = FontWeight.Bold,
                                fontSize = 16.sp,
                                color = Blue900
                            )
                        }
                    }
                }
            }
        }
    }
}

@Composable
fun MetricCard(
    title: String,
    value: String,
    subtitle: String,
    modifier: Modifier = Modifier
) {
    Card(
        colors = CardDefaults.cardColors(containerColor = White),
        shape = RoundedCornerShape(10.dp),
        elevation = CardDefaults.cardElevation(2.dp),
        modifier = modifier
    ) {
        Column(
            modifier = Modifier
                .padding(12.dp)
                .fillMaxWidth(),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            Text(title, fontSize = 10.sp, fontWeight = FontWeight.Bold, color = Gray600)
            Spacer(modifier = Modifier.height(4.dp))
            Text(value, fontSize = 16.sp, fontWeight = FontWeight.Bold, color = Blue900)
            Spacer(modifier = Modifier.height(2.dp))
            Text(subtitle, fontSize = 10.sp, color = Gray500)
        }
    }
}

@Composable
fun PaymentMethodCard(
    title: String,
    amount: String,
    iconRes: Int,
    iconBg: Color,
    iconTint: Color,
    modifier: Modifier = Modifier
) {
    Card(
        colors = CardDefaults.cardColors(containerColor = White),
        shape = RoundedCornerShape(10.dp),
        elevation = CardDefaults.cardElevation(2.dp),
        modifier = modifier
    ) {
        Row(
            modifier = Modifier.padding(12.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Surface(
                shape = RoundedCornerShape(50),
                color = iconBg,
                modifier = Modifier.size(36.dp)
            ) {
                Box(contentAlignment = Alignment.Center) {
                    Icon(painterResource(id = iconRes), contentDescription = null, tint = iconTint, modifier = Modifier.size(18.dp))
                }
            }
            Spacer(modifier = Modifier.width(8.dp))
            Column {
                Text(title, fontSize = 10.sp, fontWeight = FontWeight.Bold, color = Gray600)
                Text(amount, fontSize = 13.sp, fontWeight = FontWeight.Bold, color = Black)
            }
        }
    }
}
