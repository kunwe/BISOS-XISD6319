package com.yourcompany.informalsupermarket.data

import com.google.gson.annotations.SerializedName

/**
 * Data class representing an individual item in a sale.
 */
data class SaleItem(
    @SerializedName("transactionId") val transactionId: Int = 0,
    @SerializedName("transactionRef", alternate = ["transaction_ref"]) val transactionRef: String = "A-4521",
    @SerializedName("date") val date: String = "2026-03-01",
    @SerializedName("productName", alternate = ["product_name"]) val productName: String = "",
    @SerializedName("quantity") val quantity: Int = 0,
    @SerializedName("unitPrice", alternate = ["unit_price"]) val unitPrice: Double = 0.0,
    @SerializedName("total") val total: Double = 0.0,
    @SerializedName("paymentMethod", alternate = ["payment_method"]) val paymentMethod: String = "CASH",
    @SerializedName("storeName", alternate = ["store_name"]) val storeName: String = "Soweto"
)
