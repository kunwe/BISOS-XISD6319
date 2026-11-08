package com.yourcompany.informalsupermarket.data

import kotlinx.coroutines.flow.Flow

/**
 * Data Access Object (DAO) interface for Store management.
 */
interface StoreDao {
    fun insertStore(store: Store)
    fun updateStore(store: Store)
    fun deleteStore(store: Store)
    fun getAllStores(): Flow<List<Store>>
    fun getStoreById(storeId: Int): Store?
}
