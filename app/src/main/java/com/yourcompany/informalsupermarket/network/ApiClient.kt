package com.yourcompany.informalsupermarket.network

import com.google.gson.Gson
import com.google.gson.JsonParser
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext
import java.io.IOException
import java.net.HttpURLConnection
import java.net.SocketTimeoutException
import java.net.URL
import java.net.UnknownHostException
import java.security.SecureRandom
import java.security.cert.X509Certificate
import javax.net.ssl.*

class ApiException(message: String, val httpCode: Int = -1) : Exception(message)

/**
 * Minimal JSON-over-HTTP client built on HttpURLConnection (no extra dependencies).
 * Always sends `ngrok-skip-browser-warning`, otherwise free ngrok tunnels can answer API
 * calls with an HTML "You are about to visit..." page instead of JSON.
 */
object ApiClient {
    private val gson = Gson()

    @Volatile
    var authToken: String? = null

    /** Trust manager that accepts all certificates for dev tunneling (e.g. ngrok, local dev servers). */
    private val devTrustManager = arrayOf<TrustManager>(
        @android.annotation.SuppressLint("TrustAllX509TrustManager")
        object : X509TrustManager {
            override fun checkClientTrusted(chain: Array<out X509Certificate>?, authType: String?) {}
            override fun checkServerTrusted(chain: Array<out X509Certificate>?, authType: String?) {}
            override fun getAcceptedIssuers(): Array<X509Certificate> = emptyArray()
        }
    )

    private val devSslSocketFactory: SSLSocketFactory by lazy {
        val sslContext = SSLContext.getInstance("SSL")
        sslContext.init(null, devTrustManager, SecureRandom())
        sslContext.socketFactory
    }

    @android.annotation.SuppressLint("BadHostnameVerifier")
    private val devHostnameVerifier = HostnameVerifier { _, _ -> true }

    /** Performs the request on the IO dispatcher and returns the raw JSON body. */
    suspend fun call(method: String, path: String, body: Any? = null): String =
        withContext(Dispatchers.IO) { execute(method, path, body) }

    private fun execute(method: String, path: String, body: Any?): String {
        if (!ServerConfig.isConfigured) {
            throw ApiException("Server URL not set. Tap \"Server settings\" on the login screen and paste your ngrok URL.")
        }

        val url = try {
            URL(ServerConfig.baseUrl + path)
        } catch (e: Exception) {
            throw ApiException("That server URL isn't valid: ${ServerConfig.baseUrl}")
        }

        val conn = url.openConnection() as HttpURLConnection
        if (conn is HttpsURLConnection) {
            runCatching {
                conn.sslSocketFactory = devSslSocketFactory
                conn.hostnameVerifier = devHostnameVerifier
            }
        }
        try {
            conn.requestMethod = method
            conn.connectTimeout = 15_000
            conn.readTimeout = 20_000
            conn.setRequestProperty("User-Agent", "InformalSupermarket/1.0 (Android)")
            conn.setRequestProperty("Accept", "application/json, text/plain, */*")
            conn.setRequestProperty("ngrok-skip-browser-warning", "true")
            authToken?.let { conn.setRequestProperty("Authorization", "Bearer $it") }

            if (body != null) {
                conn.doOutput = true
                conn.setRequestProperty("Content-Type", "application/json; charset=utf-8")
                conn.outputStream.use { it.write(gson.toJson(body).toByteArray(Charsets.UTF_8)) }
            }

            val code = conn.responseCode
            val stream = if (code in 200..299) conn.inputStream else conn.errorStream
            val text = stream?.bufferedReader(Charsets.UTF_8)?.use { it.readText() }.orEmpty()

            if (code !in 200..299) {
                throw ApiException(describeError(code, text), code)
            }
            val trimmed = text.trimStart()
            if (!trimmed.startsWith("{") && !trimmed.startsWith("[")) {
                throw ApiException(
                    "The server didn't return JSON. Check the ngrok URL and that WAMP/Apache is running.",
                    code
                )
            }
            return text
        } catch (e: ApiException) {
            throw e
        } catch (e: SSLException) {
            throw ApiException("SSL Connection Error: ${e.message ?: "Trust issue"}. Try using http:// or verify device date & time.")
        } catch (e: UnknownHostException) {
            throw ApiException("Can't find that server. Check the ngrok URL and your internet connection.")
        } catch (e: SocketTimeoutException) {
            throw ApiException("The server took too long to respond. Is the ngrok tunnel still running?")
        } catch (e: IOException) {
            val detail = e.message ?: "could not reach the server"
            throw ApiException("Network error: $detail")
        } finally {
            conn.disconnect()
        }
    }

    private fun describeError(code: Int, body: String): String {
        // Our PHP API replies {"error": "..."} on failures
        runCatching {
            JsonParser.parseString(body).asJsonObject.get("error")?.asString
        }.getOrNull()?.let { return it }

        // ngrok's own error pages (tunnel stopped / URL changed)
        if (body.contains("ngrok", ignoreCase = true) && (code == 404 || code == 502 || code == 503)) {
            return "ngrok tunnel is offline or the URL has changed. Restart ngrok and update the Server URL."
        }
        return when (code) {
            404 -> "Endpoint not found (404). Check the server URL ends with your PHP folder, e.g. /BISOS-PHP/api."
            406 -> "Server rejected request format (406). Check Apache .htaccess MultiViews configuration."
            500 -> "The server hit an error (500). Check that MySQL is running and the database is imported."
            else -> "Server error ($code)."
        }
    }
}
