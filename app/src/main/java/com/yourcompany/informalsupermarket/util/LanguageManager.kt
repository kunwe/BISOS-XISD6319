package com.yourcompany.informalsupermarket.util

import android.content.Context
import java.util.Locale

object LanguageManager {
    private const val PREFS = "language_prefs"
    private const val KEY_LANG = "selected_language"

    fun getSavedLanguage(context: Context): String {
        val prefs = context.getSharedPreferences(PREFS, Context.MODE_PRIVATE)
        return prefs.getString(KEY_LANG, "English") ?: "English"
    }

    fun getLocaleCode(languageName: String): String {
        return when (languageName) {
            "Afrikaans" -> "af"
            "isiZulu" -> "zu"
            "Sesotho" -> "st"
            else -> "en"
        }
    }

    fun applyLanguage(context: Context, languageName: String) {
        val prefs = context.getSharedPreferences(PREFS, Context.MODE_PRIVATE)
        prefs.edit().putString(KEY_LANG, languageName).apply()

        val code = getLocaleCode(languageName)
        val locale = Locale.forLanguageTag(code)
        Locale.setDefault(locale)

        val resources = context.resources
        val config = resources.configuration
        config.setLocale(locale)
        @Suppress("DEPRECATION")
        resources.updateConfiguration(config, resources.displayMetrics)
    }
}
