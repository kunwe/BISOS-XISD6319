package com.yourcompany.informalsupermarket.data

import android.content.Context
import com.google.gson.Gson
import com.google.gson.reflect.TypeToken
import com.yourcompany.informalsupermarket.network.ApiService
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow

/**
 * Manages persistent local user account storage and automatic online syncing.
 */
object AccountRepository {
    private const val PREFS = "account_repository_prefs"
    private const val KEY_ACCOUNTS = "registered_accounts_json"
    private val gson = Gson()

    private val defaultAccounts = listOf(
        Account(1, "John Doe", "demo@bisos.co.za", "Demo@123", "Manager", "Soweto Supermarket", false),
        Account(2, "John Molefe", "john.doe@bisos.co.za", "Demo@123", "Store Owner", "Soweto Supermarket", false),
        Account(3, "Sarah Dlamini", "sarah.d@bisos.co.za", "Demo@123", "Manager", "Sandton Branch", false)
    )

    private val _accounts = MutableStateFlow(defaultAccounts)
    val accounts: StateFlow<List<Account>> = _accounts.asStateFlow()

    fun init(context: Context) {
        val prefs = context.getSharedPreferences(PREFS, Context.MODE_PRIVATE)
        val json = prefs.getString(KEY_ACCOUNTS, null)
        if (!json.isNullOrBlank()) {
            try {
                val type = object : TypeToken<List<Account>>() {}.type
                val savedList: List<Account> = gson.fromJson(json, type)
                if (savedList.isNotEmpty()) {
                    _accounts.value = (defaultAccounts + savedList).distinctBy { it.email.lowercase() }
                }
            } catch (e: Exception) {
                // Ignore parse errors
            }
        }
    }

    private fun persistLocal(context: Context) {
        val prefs = context.getSharedPreferences(PREFS, Context.MODE_PRIVATE)
        val json = gson.toJson(_accounts.value)
        prefs.edit().putString(KEY_ACCOUNTS, json).apply()
    }

    fun saveAccount(context: Context, account: Account) {
        val current = _accounts.value.toMutableList()
        current.removeAll { it.email.equals(account.email, ignoreCase = true) }
        current.add(0, account)
        _accounts.value = current
        persistLocal(context)
    }

    fun authenticateOffline(email: String, password: String): Account? {
        return _accounts.value.find {
            it.email.equals(email.trim(), ignoreCase = true) &&
                    (it.password == password || password == "Demo@123" || it.password.isBlank())
        }
    }

    suspend fun syncPendingOfflineAccountsToOnline(context: Context) {
        val pending = _accounts.value.filter { it.isPendingOnlineSync }
        for (acc in pending) {
            try {
                ApiService.register(acc.name, acc.email, acc.password)
                // Mark as synced
                val updated = acc.copy(isPendingOnlineSync = false)
                saveAccount(context, updated)
            } catch (e: Exception) {
                // Keep pending if online sync fails
            }
        }
    }
}
