package com.yourcompany.informalsupermarket.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.res.stringResource
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.informalsupermarket.R
import com.yourcompany.informalsupermarket.ui.InformalMartViewModel
import com.yourcompany.informalsupermarket.ui.theme.*

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun DashboardScreen(
    viewModel: InformalMartViewModel,
    modifier: Modifier = Modifier,
    onStockClick: () -> Unit = {},
    onAddStockClick: () -> Unit = {},
    onSalesClick: () -> Unit = {},
    onAlertsClick: () -> Unit = {},
    onSettingsClick: () -> Unit = {},
    onLogout: () -> Unit = {}
) {
    val products by viewModel.products.collectAsState()
    val alerts by viewModel.alerts.collectAsState()
    val report by viewModel.salesReport.collectAsState()

    var showUserMenu by remember { mutableStateOf(false) }
    var showDashboardLogoutDialog by remember { mutableStateOf(false) }

    val totalStock = products.sumOf { it.stock?.quantityOnHand ?: 0 }
    val todaySales = report?.totalSales ?: 0.0

    Scaffold(
        topBar = {
            TopAppBar(
                title = {
                    Row(verticalAlignment = Alignment.CenterVertically) {
                        Icon(
                            painter = painterResource(id = R.drawable.ic_store),
                            contentDescription = null,
                            tint = White,
                            modifier = Modifier.size(24.dp)
                        )
                        Spacer(modifier = Modifier.width(8.dp))
                        Text(
                            text = stringResource(R.string.app_name),
                            color = White,
                            fontWeight = FontWeight.Bold
                        )
                    }
                },
                actions = {
                    Box(modifier = Modifier.padding(end = 16.dp)) {
                        Icon(
                            painter = painterResource(id = R.drawable.ic_notifications),
                            contentDescription = null,
                            tint = White
                        )
                        if (alerts.isNotEmpty()) {
                            Box(
                                modifier = Modifier
                                    .size(12.dp)
                                    .background(Color.Red, CircleShape)
                                    .align(Alignment.TopEnd)
                                    .border(1.dp, Green900, CircleShape),
                                contentAlignment = Alignment.Center
                            ) {
                                Text(
                                    text = "${alerts.size}",
                                    color = White,
                                    fontSize = 8.sp,
                                    fontWeight = FontWeight.Bold
                                )
                            }
                        }
                    }
                    Box(modifier = Modifier.padding(end = 12.dp)) {
                        IconButton(onClick = { showUserMenu = true }) {
                            Icon(
                                painter = painterResource(id = R.drawable.ic_account),
                                contentDescription = "Account & Settings",
                                tint = White
                            )
                        }
                        DropdownMenu(
                            expanded = showUserMenu,
                            onDismissRequest = { showUserMenu = false }
                        ) {
                            DropdownMenuItem(
                                text = { Text("Settings") },
                                onClick = {
                                    showUserMenu = false
                                    onSettingsClick()
                                },
                                leadingIcon = {
                                    Icon(painterResource(id = R.drawable.ic_settings), contentDescription = null)
                                }
                            )
                            HorizontalDivider()
                            DropdownMenuItem(
                                text = { Text("Logout", color = Red700, fontWeight = FontWeight.Bold) },
                                onClick = {
                                    showUserMenu = false
                                    showDashboardLogoutDialog = true
                                },
                                leadingIcon = {
                                    Icon(painterResource(id = R.drawable.ic_close), contentDescription = null, tint = Red700)
                                }
                            )
                        }
                    }
                },
                colors = TopAppBarDefaults.topAppBarColors(containerColor = Green900)
            )
        },
        modifier = modifier
    ) { paddingValues ->
        if (showDashboardLogoutDialog) {
            AlertDialog(
                onDismissRequest = { showDashboardLogoutDialog = false },
                title = { Text("Confirm Logout", fontWeight = FontWeight.Bold) },
                text = { Text("Are you sure you want to log out of Informal Mart?") },
                confirmButton = {
                    Button(
                        onClick = {
                            showDashboardLogoutDialog = false
                            onLogout()
                        },
                        colors = ButtonDefaults.buttonColors(containerColor = Red700)
                    ) {
                        Text("Logout", color = White)
                    }
                },
                dismissButton = {
                    TextButton(onClick = { showDashboardLogoutDialog = false }) {
                        Text("Cancel", color = Gray600)
                    }
                }
            )
        }
        LazyColumn(
            modifier = Modifier
                .fillMaxSize()
                .padding(paddingValues)
                .background(MaterialTheme.colorScheme.background)
                .padding(16.dp),
            verticalArrangement = Arrangement.spacedBy(16.dp)
        ) {
            item {
                Text(
                    text = stringResource(R.string.dashboard_title),
                    style = MaterialTheme.typography.titleMedium,
                    color = MaterialTheme.colorScheme.onBackground,
                    fontWeight = FontWeight.Bold
                )
            }

            item {
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.spacedBy(16.dp)
                ) {
                    StatCircle(
                        modifier = Modifier.weight(1f),
                        value = "$totalStock",
                        label = stringResource(R.string.total_stock),
                        iconRes = R.drawable.ic_stock,
                        iconTint = Green900
                    )
                    StatCircle(
                        modifier = Modifier.weight(1f),
                        value = "${alerts.size}",
                        label = stringResource(R.string.low_stock_alerts),
                        iconRes = R.drawable.ic_warning,
                        iconTint = Red900
                    )
                    StatCircle(
                        modifier = Modifier.weight(1f),
                        value = "R ${String.format("%.0f", todaySales)}",
                        label = stringResource(R.string.todays_sales),
                        iconRes = R.drawable.ic_trending_up,
                        iconTint = Green900
                    )
                }
            }

            item {
                Column(verticalArrangement = Arrangement.spacedBy(8.dp)) {
                    Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                        ActionCard(
                            modifier = Modifier.weight(1f),
                            label = stringResource(R.string.view_stock),
                            iconRes = R.drawable.ic_stock,
                            onClick = onStockClick
                        )
                        ActionCard(
                            modifier = Modifier.weight(1f),
                            label = stringResource(R.string.add_stock),
                            iconRes = R.drawable.ic_add,
                            iconColor = Green900,
                            onClick = onAddStockClick
                        )
                    }
                    Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                        ActionCard(
                            modifier = Modifier.weight(1f),
                            label = stringResource(R.string.view_sales),
                            iconRes = R.drawable.ic_bar_chart,
                            onClick = onSalesClick
                        )
                        ActionCard(
                            modifier = Modifier.weight(1f),
                            label = stringResource(R.string.view_alerts),
                            iconRes = R.drawable.ic_notifications,
                            onClick = onAlertsClick
                        )
                    }
                }
            }

            item {
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Row(verticalAlignment = Alignment.CenterVertically) {
                        Icon(
                            painter = painterResource(id = R.drawable.ic_warning),
                            contentDescription = null,
                            tint = Red900,
                            modifier = Modifier.size(20.dp)
                        )
                        Spacer(modifier = Modifier.width(8.dp))
                        Text(
                            text = "LOW STOCK ALERTS",
                            fontWeight = FontWeight.Bold,
                            fontSize = 14.sp
                        )
                    }
                    TextButton(onClick = onAlertsClick) {
                        Text(
                            text = stringResource(R.string.view_all),
                            color = Green900,
                            fontSize = 12.sp,
                            textDecoration = androidx.compose.ui.text.style.TextDecoration.Underline
                        )
                    }
                }
            }

            items(alerts.take(3)) { alert ->
                AlertItem(alert.productName, alert.currentQuantity, alert.threshold)
                HorizontalDivider(color = Gray200)
            }
        }
    }
}

@Composable
fun StatCircle(
    value: String,
    label: String,
    iconRes: Int,
    iconTint: Color,
    modifier: Modifier = Modifier
) {
    Column(
        modifier = modifier,
        horizontalAlignment = Alignment.CenterHorizontally
    ) {
        Box(
            modifier = Modifier
                .size(80.dp)
                .border(2.dp, Green900, CircleShape)
                .padding(8.dp),
            contentAlignment = Alignment.Center
        ) {
            Column(horizontalAlignment = Alignment.CenterHorizontally) {
                Icon(
                    painter = painterResource(id = iconRes),
                    contentDescription = null,
                    tint = iconTint,
                    modifier = Modifier.size(24.dp)
                )
                Text(text = value, fontWeight = FontWeight.Bold, fontSize = 16.sp)
            }
        }
        Spacer(modifier = Modifier.height(4.dp))
        Text(
            text = label,
            fontSize = 10.sp,
            textAlign = TextAlign.Center,
            color = Gray700
        )
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ActionCard(
    label: String,
    iconRes: Int,
    modifier: Modifier = Modifier,
    iconColor: Color = Green900,
    onClick: () -> Unit = {}
) {
    Card(
        modifier = modifier.height(80.dp),
        colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.surface),
        elevation = CardDefaults.cardElevation(defaultElevation = 2.dp),
        shape = RoundedCornerShape(8.dp),
        onClick = onClick
    ) {
        Column(
            modifier = Modifier.fillMaxSize(),
            horizontalAlignment = Alignment.CenterHorizontally,
            verticalArrangement = Arrangement.Center
        ) {
            Icon(
                painter = painterResource(id = iconRes),
                contentDescription = null,
                tint = iconColor,
                modifier = Modifier.size(32.dp)
            )
            Text(text = label, fontSize = 12.sp, color = MaterialTheme.colorScheme.onSurface)
        }
    }
}

@Composable
fun AlertItem(name: String, current: Int, threshold: Int) {
    Row(
        modifier = Modifier
            .fillMaxWidth()
            .padding(vertical = 8.dp),
        verticalAlignment = Alignment.CenterVertically
    ) {
        Icon(
            painter = painterResource(id = R.drawable.ic_warning),
            contentDescription = null,
            tint = Red900,
            modifier = Modifier.size(20.dp)
        )
        Spacer(modifier = Modifier.width(12.dp))
        Text(text = name, modifier = Modifier.weight(1f), fontSize = 14.sp)
        Text(
            text = "$current / $threshold",
            color = Red900,
            fontWeight = FontWeight.Bold,
            fontSize = 14.sp
        )
        Spacer(modifier = Modifier.width(8.dp))
        Icon(
            painter = painterResource(id = R.drawable.ic_chevron_right),
            contentDescription = null,
            tint = Gray400
        )
    }
}
