package com.yourcompany.informalsupermarket.data

import com.google.gson.annotations.SerializedName

/**
 * Data class representing a User Account in the online & offline database.
 */
data class Account(
    @SerializedName("id") val id: Int = 0,
    @SerializedName("name") val name: String = "",
    @SerializedName("email") val email: String = "",
    @SerializedName("password") val password: String = "",
    @SerializedName("role") val role: String = "Manager",
    @SerializedName("storeName") val storeName: String = "Soweto Supermarket",
    @SerializedName("isPendingOnlineSync") val isPendingOnlineSync: Boolean = false
)
