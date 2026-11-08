package com.yourcompany.informalsupermarket.data

import com.google.gson.annotations.SerializedName

/**
 * Data class for low stock alerts.
 */
data class LowStockAlert(
    @SerializedName("productId") val productId: Int,
    @SerializedName("productName") val productName: String,
    @SerializedName("currentQuantity") val currentQuantity: Int,
    @SerializedName("threshold") val threshold: Int,
    @SerializedName("status") val status: String
)
