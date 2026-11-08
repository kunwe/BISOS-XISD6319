package com.yourcompany.informalsupermarket.ui.dialogs

import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.painter.ColorPainter
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.res.stringResource
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.compose.ui.window.Dialog
import com.example.informalsupermarket.R
import com.yourcompany.informalsupermarket.data.LowStockAlert
import com.yourcompany.informalsupermarket.ui.InformalMartViewModel
import com.yourcompany.informalsupermarket.ui.theme.*

@Composable
fun LowStockAlertDialog(
    viewModel: InformalMartViewModel,
    modifier: Modifier = Modifier,
    onDismiss: () -> Unit = {}
) {
    val alerts by viewModel.alerts.collectAsState()

    Dialog(onDismissRequest = onDismiss) {
        Surface(
            modifier = modifier
                .fillMaxWidth()
                .wrapContentHeight(),
            shape = RoundedCornerShape(16.dp),
            color = White
        ) {
            Column(
                modifier = Modifier.padding(16.dp),
                horizontalAlignment = Alignment.CenterHorizontally
            ) {
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.End
                ) {
                    IconButton(onClick = onDismiss) {
                        Icon(
                            painter = painterResource(id = R.drawable.ic_close),
                            contentDescription = null,
                            modifier = Modifier.size(24.dp)
                        )
                    }
                }

                Icon(
                    painter = painterResource(id = R.drawable.ic_warning),
                    contentDescription = null,
                    tint = Red900,
                    modifier = Modifier.size(60.dp)
                )
                Spacer(modifier = Modifier.height(8.dp))
                Text(
                    text = stringResource(R.string.low_stock_alert_title),
                    color = Red900,
                    fontWeight = FontWeight.Bold,
                    fontSize = 20.sp
                )
                Spacer(modifier = Modifier.height(4.dp))
                Text(
                    text = stringResource(R.string.low_stock_alert_desc),
                    textAlign = TextAlign.Center,
                    fontSize = 14.sp,
                    color = Black
                )
                Spacer(modifier = Modifier.height(16.dp))

                LazyColumn(
                    modifier = Modifier.heightIn(max = 300.dp),
                    verticalArrangement = Arrangement.spacedBy(8.dp)
                ) {
                    items(alerts) { alert ->
                        AlertDetailItem(alert)
                    }
                }

                Spacer(modifier = Modifier.height(24.dp))
                Button(
                    onClick = onDismiss,
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(48.dp),
                    shape = RoundedCornerShape(8.dp),
                    colors = ButtonDefaults.buttonColors(containerColor = Green900)
                ) {
                    Text(text = stringResource(R.string.acknowledge_all), color = White)
                }
            }
        }
    }
}

@Composable
fun AlertDetailItem(alert: LowStockAlert) {
    Card(
        modifier = Modifier.fillMaxWidth(),
        colors = CardDefaults.cardColors(containerColor = White),
        border = androidx.compose.foundation.BorderStroke(1.dp, Gray200),
        shape = RoundedCornerShape(8.dp)
    ) {
        Row(
            modifier = Modifier.padding(8.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Image(
                painter = ColorPainter(Gray200),
                contentDescription = null,
                modifier = Modifier
                    .size(60.dp)
                    .background(Gray100, RoundedCornerShape(4.dp))
            )
            Spacer(modifier = Modifier.width(12.dp))
            Column(modifier = Modifier.weight(1f)) {
                Text(text = alert.productName, fontWeight = FontWeight.Bold, fontSize = 14.sp)
                Text(
                    text = stringResource(R.string.current) + " ${alert.currentQuantity}",
                    color = Red900,
                    fontSize = 12.sp
                )
                Text(
                    text = stringResource(R.string.threshold) + " ${alert.threshold}",
                    color = Red900,
                    fontSize = 12.sp
                )
            }
            Column(verticalArrangement = Arrangement.spacedBy(4.dp)) {
                OutlinedButton(
                    onClick = {},
                    modifier = Modifier.height(30.dp),
                    contentPadding = PaddingValues(horizontal = 8.dp, vertical = 0.dp),
                    shape = RoundedCornerShape(4.dp),
                    border = androidx.compose.foundation.BorderStroke(1.dp, Green900)
                ) {
                    Text(text = stringResource(R.string.acknowledge), fontSize = 10.sp, color = Green900)
                }
                Button(
                    onClick = {},
                    modifier = Modifier.height(30.dp),
                    contentPadding = PaddingValues(horizontal = 8.dp, vertical = 0.dp),
                    shape = RoundedCornerShape(4.dp),
                    colors = ButtonDefaults.buttonColors(containerColor = Green900)
                ) {
                    Text(text = stringResource(R.string.reorder_now), fontSize = 10.sp, color = White)
                }
            }
        }
    }
}
