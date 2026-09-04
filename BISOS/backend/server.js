// server.js - BISOS Express API Entry Point

const express = require('express');
const cors = require('cors');
const helmet = require('helmet');
const morgan = require('morgan');
require('dotenv').config();

const authRoutes = require('./src/routes/authRoutes');
const stockRoutes = require('./src/routes/stockRoutes');
const reportRoutes = require('./src/routes/reportRoutes');
const storeRoutes = require('./src/routes/storeRoutes');
const errorHandler = require('./src/middleware/errorHandler');

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(helmet());
app.use(cors({ origin: process.env.ALLOWED_ORIGINS?.split(',') || '*' }));
app.use(express.json());
app.use(morgan('combined'));

// Health check
app.get('/health', (req, res) => res.json({ status: 'ok', timestamp: new Date().toISOString() }));

// API Routes
app.use('/v1/auth', authRoutes);
app.use('/v1/stocks', stockRoutes);
app.use('/v1/reports', reportRoutes);
app.use('/v1/stores', storeRoutes);

// 404 handler
app.use((req, res) => res.status(404).json({ error: 'Route not found' }));

// Error handler
app.use(errorHandler);

app.listen(PORT, () => console.log(`BISOS API running on port ${PORT}`));

module.exports = app;
