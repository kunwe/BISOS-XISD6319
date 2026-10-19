// reportController.js

exports.getSalesReport = async (req, res, next) => {
    try {
        const { storeId, from, to, paymentMethod } = req.query;
        // TODO: query SalesTransaction JOIN SalesItem with date range filters
        res.json({ transactions: [], total: 0, count: 0 });
    } catch (err) { next(err); }
};

exports.getDailySales = async (req, res, next) => {
    try {
        // TODO: aggregate today's SalesTransaction totals
        res.json({ total: 4560, count: 23, date: new Date().toISOString().split('T')[0] });
    } catch (err) { next(err); }
};

exports.getWeeklySales = async (req, res, next) => {
    try {
        // TODO: aggregate last 7 days grouped by date
        res.json({ data: [], total: 0 });
    } catch (err) { next(err); }
};

exports.getMonthlySales = async (req, res, next) => {
    try {
        // TODO: aggregate current month grouped by week
        res.json({ data: [], total: 124800 });
    } catch (err) { next(err); }
};
