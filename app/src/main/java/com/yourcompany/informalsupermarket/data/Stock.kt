package com.yourcompany.informalsupermarket.data

import com.google.gson.annotations.SerializedName

/**
 * Data class representing the stock level details of a product.
 */
data class Stock(
    @SerializedName("quantityOnHand") val quantityOnHand: Int,
    @SerializedName("minStockLevel") val minStockLevel: Int,
    @SerializedName("maxStockLevel") val maxStockLevel: Int
)
