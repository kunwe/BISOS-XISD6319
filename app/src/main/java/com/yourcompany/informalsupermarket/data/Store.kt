package com.yourcompany.informalsupermarket.data

import com.google.gson.annotations.SerializedName

/**
 * Entity & Data class representing a Store in the multi-store system.
 */
data class Store(
    @SerializedName("storeId") val storeId: Int = 1,
    @SerializedName("storeName") val storeName: String = "Soweto Supermarket",
    @SerializedName("storeAddress") val storeAddress: String = "123 Soweto Street, Johannesburg",
    @SerializedName("storePhone") val storePhone: String = "011-123-4567",
    @SerializedName("ownerId") val ownerId: Int = 1
)
