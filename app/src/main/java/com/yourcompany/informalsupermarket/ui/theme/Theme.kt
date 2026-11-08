package com.yourcompany.informalsupermarket.ui.theme

import android.app.Activity
import androidx.compose.foundation.isSystemInDarkTheme
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.darkColorScheme
import androidx.compose.material3.lightColorScheme
import androidx.compose.runtime.Composable
import androidx.compose.runtime.SideEffect
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.toArgb
import androidx.compose.ui.platform.LocalView
import androidx.core.view.WindowCompat

private val DarkColorScheme = darkColorScheme(
    primary = Blue500,
    secondary = Red500,
    tertiary = Blue500,
    background = Color(0xFF12181F), // Dark Navy/Black background
    surface = Color(0xFF1E2733),    // Dark Slate card surface
    onPrimary = White,
    onSecondary = White,
    onTertiary = White,
    onBackground = White,           // Words/Text are Pure White in Dark Mode
    onSurface = White,              // Words/Text are Pure White on surfaces
    surfaceVariant = Color(0xFF283445),
    onSurfaceVariant = Gray300
)

private val LightColorScheme = lightColorScheme(
    primary = Blue900,
    secondary = Red700,
    tertiary = Blue500,
    background = SilverBackground,  // Silver metallic background
    surface = White,                // White card surface
    onPrimary = White,
    onSecondary = White,
    onTertiary = White,
    onBackground = Black,           // Words/Text are dark in Light Mode
    onSurface = Black,
    surfaceVariant = Gray100,
    onSurfaceVariant = Gray700
)

@Composable
fun InformalSupermarketTheme(
    darkTheme: Boolean = isSystemInDarkTheme(),
    content: @Composable () -> Unit
) {
    val colorScheme = if (darkTheme) DarkColorScheme else LightColorScheme
    val view = LocalView.current
    if (!view.isInEditMode) {
        SideEffect {
            val window = (view.context as Activity).window
            window.statusBarColor = colorScheme.primary.toArgb()
            WindowCompat.getInsetsController(window, view).isAppearanceLightStatusBars = false
        }
    }

    MaterialTheme(
        colorScheme = colorScheme,
        typography = Typography,
        content = content
    )
}
