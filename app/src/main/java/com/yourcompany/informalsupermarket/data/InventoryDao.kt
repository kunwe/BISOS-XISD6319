package com.yourcompany.informalsupermarket.data

import kotlinx.coroutines.flow.Flow

/**
 * Data Access Object (DAO) interface demonstrating inventory filtering by currentStoreId.
 */
interface InventoryDao {
    /** Returns inventory items filtered specifically by the active store ID. */
    fun getInventoryByStore(activeStoreId: Int): Flow<List<Product>>
    fun insertProduct(product: Product)
    fun updateProduct(product: Product)
    fun deleteProduct(productId: Int)
}
