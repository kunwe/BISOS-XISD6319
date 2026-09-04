// stockController.js

exports.getStocks = async (req, res, next) => {
    try {
        const { storeId, category, status } = req.query;
        // TODO: query DB with filters
        res.json({ stocks: [], total: 0 });
    } catch (err) { next(err); }
};

exports.updateStock = async (req, res, next) => {
    try {
        const { productId } = req.params;
        const { quantity } = req.body;
        // TODO: update stock quantity in DB, trigger alert if below reorder level
        res.json({ message: 'Stock updated', productId, quantity });
    } catch (err) { next(err); }
};

exports.getLowStockAlerts = async (req, res, next) => {
    try {
        const { storeId } = req.query;
        // TODO: query LowStockAlert table where status = PENDING
        res.json({ alerts: [] });
    } catch (err) { next(err); }
};

exports.acknowledgeAlert = async (req, res, next) => {
    try {
        const { alertId } = req.params;
        // TODO: update alert status to ACKNOWLEDGED, set acknowledged_by and acknowledged_at
        res.json({ message: 'Alert acknowledged', alertId });
    } catch (err) { next(err); }
};

exports.resolveAlert = async (req, res, next) => {
    try {
        const { alertId } = req.params;
        // TODO: update alert status to RESOLVED, set resolved_at
        res.json({ message: 'Alert resolved', alertId });
    } catch (err) { next(err); }
};
