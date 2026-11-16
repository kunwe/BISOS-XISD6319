package com.yourcompany.informalsupermarket.data

import android.content.Context
import com.google.gson.Gson
import com.google.gson.reflect.TypeToken

/**
 * Manages persistent disk storage for products, stock levels, low-stock alerts, and sales reports on a per-store basis.
 */
object PerStoreDatabaseRepository {
    private const val PREFS = "per_store_database_prefs"
    private val gson = Gson()

    fun getProductsForStore(context: Context, storeId: Int): List<Product>? {
        val prefs = context.getSharedPreferences(PREFS, Context.MODE_PRIVATE)
        val json = prefs.getString("store_products_$storeId", null) ?: return null
        return try {
            val type = object : TypeToken<List<Product>>() {}.type
            gson.fromJson(json, type)
        } catch (_: Exception) {
            null
        }
    }

    fun saveProductsForStore(context: Context, storeId: Int, products: List<Product>) {
        val prefs = context.getSharedPreferences(PREFS, Context.MODE_PRIVATE)
        val json = gson.toJson(products)
        prefs.edit().putString("store_products_$storeId", json).apply()
    }

    fun getSalesReportForStore(context: Context, storeId: Int): SalesReport? {
        val prefs = context.getSharedPreferences(PREFS, Context.MODE_PRIVATE)
        val json = prefs.getString("store_sales_$storeId", null) ?: return null
        return try {
            gson.fromJson(json, SalesReport::class.java)
        } catch (_: Exception) {
            null
        }
    }

    fun saveSalesReportForStore(context: Context, storeId: Int, report: SalesReport) {
        val prefs = context.getSharedPreferences(PREFS, Context.MODE_PRIVATE)
        val json = gson.toJson(report)
        prefs.edit().putString("store_sales_$storeId", json).apply()
    }
}
