package com.yourcompany.informalsupermarket.data

import com.google.gson.annotations.SerializedName

/**
 * Data class representing a User.
 * Defaults on every field let Gson parse partial server responses safely.
 */
data class User(
    @SerializedName("id") val id: Int = 0,
    @SerializedName("name") val name: String = "",
    @SerializedName("email") val email: String = "",
    @SerializedName("storeId") val storeId: Int = 1
)
