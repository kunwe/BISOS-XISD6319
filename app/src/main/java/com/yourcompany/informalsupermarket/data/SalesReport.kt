package com.yourcompany.informalsupermarket.data

import com.google.gson.annotations.SerializedName

/**
 * Data class representing a daily sales report.
 */
data class SalesReport(
    @SerializedName("date") val date: String = "March 2026",
    @SerializedName("branch") val branch: String = "Soweto Branch",
    @SerializedName("totalSales", alternate = ["total_sales"]) val totalSales: Double = 0.0,
    @SerializedName("transactionCount", alternate = ["transaction_count"]) val transactionCount: Int = 0,
    @SerializedName("averageSale", alternate = ["average_sale"]) val averageSale: Double = 0.0,
    @SerializedName("cashTotal", alternate = ["cash_total"]) val cashTotal: Double = 0.0,
    @SerializedName("cardTotal", alternate = ["card_total"]) val cardTotal: Double = 0.0,
    @SerializedName("mobileTotal", alternate = ["mobile_total"]) val mobileTotal: Double = 0.0,
    @SerializedName("items") val items: List<SaleItem> = emptyList()
)
