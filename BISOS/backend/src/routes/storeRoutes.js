// storeRoutes.js
const express = require('express');
const router = express.Router();
const { getStores, createStore, updateStore, deleteStore } = require('../controllers/storeController');
const { authenticate, authorize } = require('../middleware/auth');

router.get('/', authenticate, getStores);
router.post('/', authenticate, authorize('Store Owner', 'Manager'), createStore);
router.put('/:storeId', authenticate, authorize('Store Owner', 'Manager'), updateStore);
router.delete('/:storeId', authenticate, authorize('Store Owner'), deleteStore);

module.exports = router;
