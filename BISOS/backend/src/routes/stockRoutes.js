// stockRoutes.js
const express = require('express');
const router = express.Router();
const { getStocks, updateStock, getLowStockAlerts, acknowledgeAlert, resolveAlert } = require('../controllers/stockController');
const { authenticate } = require('../middleware/auth');

router.get('/', authenticate, getStocks);
router.put('/:productId', authenticate, updateStock);
router.get('/low-stock', authenticate, getLowStockAlerts);
router.put('/alerts/:alertId/acknowledge', authenticate, acknowledgeAlert);
router.put('/alerts/:alertId/resolve', authenticate, resolveAlert);

module.exports = router;
