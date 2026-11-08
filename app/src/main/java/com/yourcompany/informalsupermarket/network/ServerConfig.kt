package com.yourcompany.informalsupermarket.network

import android.content.Context
import android.content.SharedPreferences
import com.example.informalsupermarket.BuildConfig

/**
 * Holds the backend URL (your ngrok tunnel).
 *
 * Free ngrok URLs change every time ngrok restarts, so the URL can be edited at runtime
 * (Login screen -> "Server settings") and is remembered. The build-time default comes from
 * `api.base.url` in local.properties.
 */
object ServerConfig {
    private const val PREFS = "server_config"
    private const val KEY_URL = "base_url"

    /** Folder the PHP project lives in under the web root, plus the API folder. */
    const val DEFAULT_API_PATH = "/BISOS-PHP/api"

    private var prefs: SharedPreferences? = null

    fun init(context: Context) {
        prefs = context.applicationContext.getSharedPreferences(PREFS, Context.MODE_PRIVATE)
    }

    /** Current API base URL, without a trailing slash. */
    val baseUrl: String
        get() = prefs?.getString(KEY_URL, null)?.takeIf { it.isNotBlank() }
            ?: normalize(BuildConfig.DEFAULT_API_BASE_URL)

    val isConfigured: Boolean
        get() = baseUrl.isNotBlank() && !baseUrl.contains("YOUR-SUBDOMAIN", ignoreCase = true)

    /** Saves the URL and returns the normalized form that was stored. */
    fun save(rawUrl: String): String {
        val normalized = normalize(rawUrl)
        prefs?.edit()?.putString(KEY_URL, normalized)?.apply()
        return normalized
    }

    /**
     * Accepts whatever gets pasted from the ngrok console and turns it into a usable base URL:
     *   abc.ngrok-free.app                       -> https://abc.ngrok-free.app/BISOS-PHP/api
     *   https://abc.ngrok-free.app/              -> https://abc.ngrok-free.app/BISOS-PHP/api
     *   https://abc.ngrok-free.app/BISOS-PHP     -> https://abc.ngrok-free.app/BISOS-PHP/api
     *   https://abc.ngrok-free.app/BISOS-PHP/api -> unchanged
     */
    fun normalize(raw: String): String {
        var url = raw.trim()
        if (url.isEmpty()) return ""
        if (!url.startsWith("http://", ignoreCase = true) && !url.startsWith("https://", ignoreCase = true)) {
            url = "https://$url"
        }
        url = url.trimEnd('/')
        val hasPath = url.substringAfter("://").contains('/')
        url = when {
            !hasPath -> url + DEFAULT_API_PATH
            !url.endsWith("/api", ignoreCase = true) -> "$url/api"
            else -> url
        }
        return url
    }
}
