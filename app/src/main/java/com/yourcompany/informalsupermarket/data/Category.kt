package com.yourcompany.informalsupermarket.data

import com.google.gson.annotations.SerializedName

/**
 * Data class representing a product category.
 */
data class Category(
    @SerializedName("id") val id: Int,
    @SerializedName("name") val name: String
)
