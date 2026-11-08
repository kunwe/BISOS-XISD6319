package com.example.informalsupermarket

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.activity.enableEdgeToEdge
import com.yourcompany.informalsupermarket.network.ServerConfig
import com.yourcompany.informalsupermarket.ui.InformalMartApp
import com.yourcompany.informalsupermarket.util.LanguageManager

class MainActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        ServerConfig.init(applicationContext)
        val currentLang = LanguageManager.getSavedLanguage(applicationContext)
        LanguageManager.applyLanguage(applicationContext, currentLang)
        enableEdgeToEdge()
        setContent {
            InformalMartApp()
        }
    }
}
