package com.yourcompany.informalsupermarket.ui.screens

import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.verticalScroll
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.res.stringResource
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.informalsupermarket.R
import com.yourcompany.informalsupermarket.ui.theme.*

@Composable
fun LoginScreen(
    modifier: Modifier = Modifier,
    isLoading: Boolean = false,
    serverUrl: String = "",
    onServerUrlSave: (String) -> String = { it },
    onTestConnection: () -> Unit = {},
    onLoginClick: (email: String, password: String) -> Unit = { _, _ -> },
    onLoginOffline: (email: String, password: String) -> Unit = { _, _ -> },
    onRegisterClick: () -> Unit = {}
) {
    var email by remember { mutableStateOf("") }
    var password by remember { mutableStateOf("") }
    var showServerSettings by remember { mutableStateOf(false) }
    var serverUrlText by remember { mutableStateOf(serverUrl) }

    Column(
        modifier = modifier
            .fillMaxSize()
            .background(SilverBackground)
            .verticalScroll(rememberScrollState())
            .padding(24.dp),
        horizontalAlignment = Alignment.CenterHorizontally,
        verticalArrangement = Arrangement.Center
    ) {
        Image(
            painter = painterResource(id = R.drawable.ic_bisos_full_logo),
            contentDescription = "BISOS Logo",
            modifier = Modifier.size(150.dp)
        )
        Spacer(modifier = Modifier.height(16.dp))
        Text(
            text = stringResource(R.string.app_name),
            fontSize = 28.sp,
            fontWeight = FontWeight.Bold,
            color = Blue900
        )
        Text(
            text = stringResource(R.string.login_slogan),
            fontSize = 14.sp,
            color = Gray600
        )
        Spacer(modifier = Modifier.height(32.dp))

        OutlinedTextField(
            value = email,
            onValueChange = { email = it },
            label = { Text(stringResource(R.string.email_address)) },
            modifier = Modifier.fillMaxWidth(),
            leadingIcon = {
                Icon(
                    painter = painterResource(id = R.drawable.ic_mail),
                    contentDescription = null,
                    modifier = Modifier.size(20.dp)
                )
            },
            singleLine = true,
            shape = RoundedCornerShape(12.dp)
        )
        Spacer(modifier = Modifier.height(16.dp))
        OutlinedTextField(
            value = password,
            onValueChange = { password = it },
            label = { Text(stringResource(R.string.password)) },
            modifier = Modifier.fillMaxWidth(),
            leadingIcon = {
                Icon(
                    painter = painterResource(id = R.drawable.ic_lock),
                    contentDescription = null,
                    modifier = Modifier.size(20.dp)
                )
            },
            trailingIcon = {
                Icon(
                    painter = painterResource(id = R.drawable.ic_visibility),
                    contentDescription = null,
                    modifier = Modifier.size(20.dp)
                )
            },
            visualTransformation = PasswordVisualTransformation(),
            shape = RoundedCornerShape(12.dp)
        )
        Spacer(modifier = Modifier.height(20.dp))

        // Primary Online Login Button
        Button(
            onClick = { onLoginClick(email, password) },
            enabled = !isLoading,
            modifier = Modifier
                .fillMaxWidth()
                .height(52.dp),
            shape = RoundedCornerShape(8.dp),
            colors = ButtonDefaults.buttonColors(containerColor = Blue900)
        ) {
            if (isLoading) {
                CircularProgressIndicator(modifier = Modifier.size(24.dp), color = White, strokeWidth = 2.dp)
            } else {
                Text(text = stringResource(R.string.login), fontSize = 16.sp, fontWeight = FontWeight.Bold, color = White)
            }
        }

        Spacer(modifier = Modifier.height(12.dp))

        // Secondary Offline Sign-In Button
        OutlinedButton(
            onClick = { onLoginOffline(email, password) },
            modifier = Modifier
                .fillMaxWidth()
                .height(52.dp),
            shape = RoundedCornerShape(8.dp),
            colors = ButtonDefaults.outlinedButtonColors(contentColor = Blue900),
            border = androidx.compose.foundation.BorderStroke(1.5.dp, Blue900)
        ) {
            Row(verticalAlignment = Alignment.CenterVertically) {
                Icon(
                    painter = painterResource(id = R.drawable.ic_account),
                    contentDescription = null,
                    tint = Blue900,
                    modifier = Modifier.size(20.dp)
                )
                Spacer(modifier = Modifier.width(8.dp))
                Text(text = "Sign In Offline", fontSize = 15.sp, fontWeight = FontWeight.Bold)
            }
        }

        Spacer(modifier = Modifier.height(20.dp))

        Row(
            modifier = Modifier.fillMaxWidth(),
            verticalAlignment = Alignment.CenterVertically
        ) {
            HorizontalDivider(modifier = Modifier.weight(1f), color = Gray300)
            Text(
                text = " " + stringResource(R.string.or_continue_with) + " ",
                fontSize = 12.sp,
                color = Gray500
            )
            HorizontalDivider(modifier = Modifier.weight(1f), color = Gray300)
        }
        Spacer(modifier = Modifier.height(20.dp))

        OutlinedButton(
            onClick = {},
            modifier = Modifier
                .fillMaxWidth()
                .height(50.dp),
            shape = RoundedCornerShape(8.dp),
            border = androidx.compose.foundation.BorderStroke(1.dp, Gray300)
        ) {
            Row(verticalAlignment = Alignment.CenterVertically) {
                Box(
                    modifier = Modifier
                        .size(24.dp)
                        .background(Color.LightGray, RoundedCornerShape(4.dp)),
                    contentAlignment = Alignment.Center
                ) {
                    Text("G", fontWeight = FontWeight.Bold, color = Color.Blue)
                }
                Spacer(modifier = Modifier.width(12.dp))
                Text(text = stringResource(R.string.sign_in_with_google), color = Black)
            }
        }
        Spacer(modifier = Modifier.height(28.dp))

        Row {
            Text(text = stringResource(R.string.dont_have_account), color = Black)
            Spacer(modifier = Modifier.width(4.dp))
            TextButton(onClick = onRegisterClick) {
                Text(
                    text = stringResource(R.string.register_here),
                    color = Blue900,
                    fontWeight = FontWeight.Bold,
                    textDecoration = androidx.compose.ui.text.style.TextDecoration.Underline
                )
            }
        }

        Spacer(modifier = Modifier.height(8.dp))
        TextButton(onClick = { showServerSettings = !showServerSettings }) {
            Text(
                text = if (showServerSettings) "Hide server settings" else "Server settings",
                color = Gray600,
                fontSize = 13.sp
            )
        }
        if (showServerSettings) {
            OutlinedTextField(
                value = serverUrlText,
                onValueChange = { serverUrlText = it },
                label = { Text("Server URL (ngrok)") },
                placeholder = { Text("https://xxxx.ngrok-free.app") },
                supportingText = { Text("Paste the https URL ngrok shows. /BISOS-PHP/api is added for you.") },
                singleLine = true,
                modifier = Modifier.fillMaxWidth(),
                shape = RoundedCornerShape(12.dp)
            )
            Spacer(modifier = Modifier.height(8.dp))
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.spacedBy(12.dp)
            ) {
                OutlinedButton(
                    onClick = { serverUrlText = onServerUrlSave(serverUrlText) },
                    modifier = Modifier.weight(1f),
                    shape = RoundedCornerShape(8.dp)
                ) {
                    Text("Save", color = Blue900)
                }
                OutlinedButton(
                    onClick = {
                        serverUrlText = onServerUrlSave(serverUrlText)
                        onTestConnection()
                    },
                    enabled = !isLoading,
                    modifier = Modifier.weight(1f),
                    shape = RoundedCornerShape(8.dp)
                ) {
                    Text("Save & test", color = Blue900)
                }
            }
        }
    }
}

@Preview(showBackground = true)
@Composable
fun LoginPreview() {
    InformalSupermarketTheme {
        LoginScreen()
    }
}
