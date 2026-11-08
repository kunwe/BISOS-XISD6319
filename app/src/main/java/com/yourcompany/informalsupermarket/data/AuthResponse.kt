package com.yourcompany.informalsupermarket.data

import com.google.gson.annotations.SerializedName

/**
 * Response from /auth/login and /auth/register.
 * On failure the server sends {"error": "..."} with a non-2xx status instead.
 */
data class AuthResponse(
    @SerializedName("status") val status: String? = null,
    @SerializedName("token") val token: String? = null,
    @SerializedName("user") val user: User? = null,
    @SerializedName("error") val error: String? = null
)
