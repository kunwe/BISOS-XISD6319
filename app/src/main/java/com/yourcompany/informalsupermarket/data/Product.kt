package com.yourcompany.informalsupermarket.data

import com.google.gson.annotations.SerializedName

/**
 * Data class representing a Product in the inventory.
 */
data class Product(
    @SerializedName("id") val id: Int,
    @SerializedName("name") val name: String,
    @SerializedName("barcode") val barcode: String?,
    @SerializedName("unitPrice") val unitPrice: Double,
    @SerializedName("costPrice") val costPrice: Double,
    @SerializedName("reorderLevel") val reorderLevel: Int,
    @SerializedName("categoryId") val categoryId: Int,
    @SerializedName("storeId") val storeId: Int,
    @SerializedName("category") val category: Category?,
    @SerializedName("stock") val stock: Stock?
)
