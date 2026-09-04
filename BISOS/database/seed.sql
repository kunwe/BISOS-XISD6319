-- BISOS Sample Data (seed.sql)
USE bisos_db;

INSERT INTO Store (name, address, phone, email, manager_name) VALUES
    ('Soweto Supermarket', '123 Soweto Street, Johannesburg, 1804', '011-123-4567', 'soweto@bisos.co.za', 'John Molefe'),
    ('Sandton Branch', '456 Sandton Drive, Sandton, 2196', '011-234-5678', 'sandton@bisos.co.za', 'Sarah Dlamini'),
    ('Pretoria Branch', '789 Church Street, Pretoria, 0002', '012-345-6789', 'pretoria@bisos.co.za', 'David Nkosi');

INSERT INTO User (store_id, role_id, username, email, password_hash, first_name, last_name, phone) VALUES
    (1, 1, 'admin', 'admin@bisos.co.za', '$2b$10$placeholder_hash', 'Admin', 'User', '011-000-0000'),
    (1, 2, 'jmolefe', 'john.doe@bisos.co.za', '$2b$10$placeholder_hash', 'John', 'Doe', '011-123-4567'),
    (2, 2, 'sdlamini', 'sarah.d@bisos.co.za', '$2b$10$placeholder_hash', 'Sarah', 'Dlamini', '011-234-5678');

INSERT INTO Product (store_id, name, description, category, barcode, cost_price, selling_price, reorder_level, is_perishable) VALUES
    (1, 'Milk 2L', 'Fresh full cream milk 2L', 'Dairy', '6001234567890', 18.50, 24.99, 20, TRUE),
    (1, 'Bread 700g', 'White bread 700g', 'Bakery', '6001234567891', 12.00, 18.50, 10, TRUE),
    (1, 'Sugar 1kg', 'White sugar 1kg', 'Pantry', '6001234567892', 22.00, 32.00, 15, FALSE),
    (1, 'Cooking Oil 750ml', 'Sunflower oil 750ml', 'Pantry', '6001234567893', 45.00, 58.99, 20, FALSE),
    (1, 'Chicken 1kg', 'Fresh chicken portions 1kg', 'Meat', '6001234567894', 55.00, 72.99, 10, TRUE),
    (1, 'Rice 5kg', 'White rice 5kg', 'Grains', '6001234567895', 65.00, 85.00, 5, FALSE),
    (1, 'Pasta 500g', 'Spaghetti 500g', 'Pantry', '6001234567896', 12.00, 16.99, 20, FALSE),
    (1, 'Canned Beans 410g', 'Baked beans 410g', 'Canned Goods', '6001234567897', 8.50, 12.99, 30, FALSE),
    (1, 'Maize Meal 5kg', 'White maize meal 5kg', 'Grains', '6001234567898', 35.00, 48.99, 10, FALSE),
    (1, 'Eggs 30-pack', 'Large eggs 30-pack', 'Dairy', '6001234567899', 42.00, 56.50, 15, TRUE);

INSERT INTO Stock (product_id, quantity, minimum_level, maximum_level, location_code) VALUES
    (1, 15, 10, 50, 'A-01'), (2, 45, 15, 60, 'A-02'), (3, 8, 5, 40, 'B-01'),
    (4, 32, 12, 45, 'B-02'), (5, 5, 8, 30, 'C-01'), (6, 20, 5, 50, 'C-02'),
    (7, 55, 20, 80, 'D-01'), (8, 80, 30, 120, 'D-02'), (9, 18, 10, 40, 'E-01'),
    (10, 12, 15, 35, 'E-02');

INSERT INTO Supplier (name, contact_name, phone, email, address, payment_terms, rating) VALUES
    ('Fresh Foods SA', 'Peter Ndlovu', '011-555-1234', 'peter@freshfoods.co.za', '123 Supplier Road, Johannesburg', 'Net 30', 4),
    ('General Goods Supply', 'Mary Zulu', '011-555-5678', 'mary@ggsupply.co.za', '456 Commerce Street, Pretoria', 'Net 15', 3),
    ('Quality Meat Wholesalers', 'James Smith', '011-555-9012', 'james@qmeat.co.za', '789 Meat Avenue, Cape Town', 'Net 30', 5);

INSERT INTO SalesTransaction (store_id, transaction_ref, transaction_date, payment_method, total_amount, processed_by, status) VALUES
    (1, 'A-4521', '2026-03-26 09:15:00', 'CASH', 299.88, 2, 'COMPLETED'),
    (1, 'A-4522', '2026-03-26 10:30:00', 'CARD', 148.00, 2, 'COMPLETED'),
    (1, 'A-4523', '2026-03-26 11:45:00', 'MOBILE', 160.00, 2, 'COMPLETED'),
    (1, 'A-4524', '2026-03-25 16:20:00', 'CASH', 245.50, 2, 'COMPLETED'),
    (1, 'A-4525', '2026-03-25 14:00:00', 'CARD', 89.50, 2, 'COMPLETED');

INSERT INTO SalesItem (transaction_id, product_id, quantity, unit_price, total_price) VALUES
    (1, 1, 12, 24.99, 299.88), (2, 2, 8, 18.50, 148.00), (3, 3, 5, 32.00, 160.00),
    (4, 4, 3, 58.99, 176.97), (4, 5, 1, 68.53, 68.53), (5, 6, 1, 85.00, 85.00);

INSERT INTO LowStockAlert (product_id, current_quantity, reorder_level, status, severity) VALUES
    (1, 15, 20, 'PENDING', 'HIGH'), (3, 8, 15, 'PENDING', 'HIGH'),
    (5, 5, 10, 'PENDING', 'CRITICAL'), (10, 12, 15, 'PENDING', 'MEDIUM');
