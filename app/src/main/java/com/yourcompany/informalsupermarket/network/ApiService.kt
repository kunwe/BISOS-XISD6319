package com.yourcompany.informalsupermarket.network

import com.google.gson.Gson
import com.google.gson.JsonParser
import com.google.gson.annotations.SerializedName
import com.yourcompany.informalsupermarket.data.*

// ---- Wire formats (match the PHP API's JSON) --------------------------------------------
// All fields have defaults so Gson falls back to the no-arg constructor and a missing field
// never turns into a null in a non-null Kotlin property.

private data class StockDto(
    @SerializedName("product_id") val productId: Int = 0,
    @SerializedName("store_id") val storeId: Int = 1,
    @SerializedName("name") val name: String = "",
    @SerializedName("category") val category: String? = null,
    @SerializedName("barcode") val barcode: String? = null,
    @SerializedName("cost_price") val costPrice: Double = 0.0,
    @SerializedName("selling_price") val sellingPrice: Double = 0.0,
    @SerializedName("reorder_level") val reorderLevel: Int = 0,
    @SerializedName("quantity") val quantity: Int = 0,
    @SerializedName("minimum_level") val minimumLevel: Int = 0,
    @SerializedName("maximum_level") val maximumLevel: Int = 999
)

private data class AlertDto(
    @SerializedName("alert_id") val alertId: Int = 0,
    @SerializedName("product_id") val productId: Int = 0,
    @SerializedName("name") val name: String = "",
    @SerializedName("current_quantity") val currentQuantity: Int = 0,
    @SerializedName("reorder_level") val reorderLevel: Int = 0,
    @SerializedName("status") val status: String = ""
)

private data class RegisterRequest(val name: String, val email: String, val password: String)

private data class NewProductRequest(
    val name: String,
    val price: Double,
    val stock: Int,
    val reorder: Int,
    val storeId: Int
)

/** Every endpoint the app uses. All calls are suspend functions and throw [ApiException]. */
object ApiService {
    private val gson = Gson()

    /** Health check. Returns the server's message. */
    suspend fun ping(): String {
        val json = JsonParser.parseString(ApiClient.call("GET", "/ping")).asJsonObject
        return json.get("message")?.asString ?: "OK"
    }

    suspend fun login(email: String, password: String): AuthResponse {
        val text = ApiClient.call("POST", "/auth/login", LoginRequest(email, password))
        return gson.fromJson(text, AuthResponse::class.java)
    }

    suspend fun register(name: String, email: String, password: String): AuthResponse {
        val text = ApiClient.call("POST", "/auth/register", RegisterRequest(name, email, password))
        return gson.fromJson(text, AuthResponse::class.java)
    }

    suspend fun getProducts(storeId: Int): List<Product> {
        val text = ApiClient.call("GET", "/stocks?store_id=$storeId")
        val rows = JsonParser.parseString(text).asJsonObject.getAsJsonArray("data")
        return gson.fromJson(rows, Array<StockDto>::class.java).map {
            Product(
                id = it.productId,
                name = it.name,
                barcode = it.barcode,
                unitPrice = it.sellingPrice,
                costPrice = it.costPrice,
                reorderLevel = it.reorderLevel,
                categoryId = 0,
                storeId = it.storeId,
                category = it.category?.let { c -> Category(0, c) },
                stock = Stock(it.quantity, it.minimumLevel, it.maximumLevel)
            )
        }
    }

    suspend fun getAlerts(storeId: Int = 1): List<LowStockAlert> {
        val text = ApiClient.call("GET", "/alerts?store_id=$storeId&status=PENDING")
        val rows = JsonParser.parseString(text).asJsonObject.getAsJsonArray("data")
        return gson.fromJson(rows, Array<AlertDto>::class.java).map {
            LowStockAlert(
                productId = it.productId,
                productName = it.name,
                currentQuantity = it.currentQuantity,
                threshold = it.reorderLevel,
                status = it.status
            )
        }
    }

    suspend fun getSalesReport(storeId: Int): SalesReport {
        val text = ApiClient.call("GET", "/sales/report?store_id=$storeId")
        val data = JsonParser.parseString(text).asJsonObject.getAsJsonObject("data")
        return gson.fromJson(data, SalesReport::class.java)
    }

    suspend fun addProduct(name: String, price: Double, stock: Int, reorder: Int, storeId: Int) {
        ApiClient.call("POST", "/products", NewProductRequest(name, price, stock, reorder, storeId))
    }

    suspend fun recordSale(productId: Int, quantity: Int, paymentMethod: String, storeId: Int) {
        ApiClient.call("POST", "/sales", mapOf(
            "productId" to productId,
            "quantity" to quantity,
            "paymentMethod" to paymentMethod,
            "storeId" to storeId
        ))
    }

    suspend fun voidSale(transactionId: Int) {
        ApiClient.call("POST", "/sales/void", mapOf("transactionId" to transactionId))
    }

    suspend fun deleteProduct(productId: Int) {
        ApiClient.call("POST", "/products/delete", mapOf("productId" to productId))
    }

    suspend fun updateProduct(id: Int, name: String, price: Double, stock: Int, reorder: Int, barcode: String = "") {
        ApiClient.call("POST", "/products/update", mapOf(
            "id" to id,
            "name" to name,
            "price" to price,
            "stock" to stock,
            "reorder" to reorder,
            "barcode" to barcode
        ))
    }
}
