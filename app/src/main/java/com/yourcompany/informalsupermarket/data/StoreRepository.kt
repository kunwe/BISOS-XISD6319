package com.yourcompany.informalsupermarket.data

import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow

/**
 * Repository managing stores and active store selection across the application.
 */
object StoreRepository {
    private val defaultStores = listOf(
        Store(1, "Soweto Supermarket", "123 Soweto Street, Johannesburg", "011-123-4567", 1),
        Store(2, "Sandton Branch", "456 Sandton Drive, Sandton", "011-234-5678", 1),
        Store(3, "Pretoria Branch", "789 Church Street, Pretoria", "012-345-6789", 1)
    )

    private val _stores = MutableStateFlow(defaultStores)
    val stores: StateFlow<List<Store>> = _stores.asStateFlow()

    private val _activeStore = MutableStateFlow(defaultStores.first())
    val activeStore: StateFlow<Store> = _activeStore.asStateFlow()

    fun setActiveStore(storeId: Int) {
        val found = _stores.value.find { it.storeId == storeId } ?: _stores.value.first()
        _activeStore.value = found
    }

    fun setActiveStoreByName(storeName: String) {
        val found = _stores.value.find { it.storeName.equals(storeName, ignoreCase = true) }
        if (found != null) {
            _activeStore.value = found
        } else {
            val newStore = Store(
                storeId = _stores.value.size + 1,
                storeName = storeName,
                storeAddress = "123 Main Street",
                storePhone = "011-000-0000"
            )
            addStore(newStore)
        }
    }

    fun addStore(store: Store): Store {
        val nextId = if (store.storeId > 0 && _stores.value.none { it.storeId == store.storeId }) {
            store.storeId
        } else {
            (_stores.value.maxOfOrNull { it.storeId } ?: 0) + 1
        }
        val finalStore = store.copy(storeId = nextId)
        _stores.value = _stores.value + finalStore
        _activeStore.value = finalStore
        return finalStore
    }

    fun updateStore(updatedStore: Store) {
        _stores.value = _stores.value.map {
            if (it.storeId == updatedStore.storeId) updatedStore else it
        }
        if (_activeStore.value.storeId == updatedStore.storeId) {
            _activeStore.value = updatedStore
        }
    }
}
