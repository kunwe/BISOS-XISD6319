// storeController.js

exports.getStores = async (req, res, next) => {
    try {
        // TODO: query all active stores from DB
        res.json({ stores: [] });
    } catch (err) { next(err); }
};

exports.createStore = async (req, res, next) => {
    try {
        const { name, address, phone, email, manager_name } = req.body;
        // TODO: insert new store into DB
        res.status(201).json({ message: 'Store created', store: { name } });
    } catch (err) { next(err); }
};

exports.updateStore = async (req, res, next) => {
    try {
        const { storeId } = req.params;
        // TODO: update store record in DB
        res.json({ message: 'Store updated', storeId });
    } catch (err) { next(err); }
};

exports.deleteStore = async (req, res, next) => {
    try {
        const { storeId } = req.params;
        // TODO: soft-delete (set is_active = false)
        res.json({ message: 'Store deactivated', storeId });
    } catch (err) { next(err); }
};
