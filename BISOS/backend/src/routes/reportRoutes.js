// reportRoutes.js
const express = require('express');
const router = express.Router();
const { getSalesReport, getDailySales, getWeeklySales, getMonthlySales } = require('../controllers/reportController');
const { authenticate } = require('../middleware/auth');

router.get('/sales', authenticate, getSalesReport);
router.get('/daily', authenticate, getDailySales);
router.get('/weekly', authenticate, getWeeklySales);
router.get('/monthly', authenticate, getMonthlySales);

module.exports = router;
