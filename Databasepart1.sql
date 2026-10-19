-- ============================================================
-- DATABASE: inventory_system
-- Description: Branch Inventory & Sales Optimization System
-- for Informal Supermarket Stores
-- Version: XISD6319
-- ============================================================

DROP DATABASE IF EXISTS inventory_system;
CREATE DATABASE inventory_system
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE inventory_system;

-- ============================================================
-- TABLE: Category
-- ============================================================
DROP TABLE IF EXISTS Category;
CREATE TABLE Category (
    category_id          INT AUTO_INCREMENT PRIMARY KEY,
    category_name        VARCHAR(50) NOT NULL UNIQUE,
    description          VARCHAR(255),
    parent_category_id   INT,
    created_at           DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at           DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_parent (parent_category_id),
    CONSTRAINT fk_category_parent
        FOREIGN KEY (parent_category_id) REFERENCES Category(category_id)
            ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: Supplier
-- ============================================================
DROP TABLE IF EXISTS Supplier;
CREATE TABLE Supplier (
    supplier_id          INT AUTO_INCREMENT PRIMARY KEY,
    supplier_name        VARCHAR(100) NOT NULL,
    contact_person       VARCHAR(100),
    phone                VARCHAR(20),
    email                VARCHAR(100),
    address              VARCHAR(255),
    is_active            BOOLEAN DEFAULT TRUE,
    payment_terms        VARCHAR(30),            -- e.g., Net 15, Net 30
    rating               DECIMAL(3,2) CHECK (rating >= 0 AND rating <= 5),
    created_at           DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at           DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_supplier_name (supplier_name),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: Product
-- ============================================================
DROP TABLE IF EXISTS Product;
CREATE TABLE Product (
    product_id           INT AUTO_INCREMENT PRIMARY KEY,
    SKU                  VARCHAR(50) NOT NULL UNIQUE,
    barcode              VARCHAR(50) UNIQUE,
    product_name         VARCHAR(100) NOT NULL,
    category_id          INT,
    supplier_id          INT,
    unit_price           DECIMAL(10,2) NOT NULL,
    cost_price           DECIMAL(10,2) NOT NULL,
    reorder_level        INT NOT NULL DEFAULT 10,
    is_active            BOOLEAN DEFAULT TRUE,
    expiry_date          DATE,
    created_at           DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at           DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category_id),
    INDEX idx_supplier (supplier_id),
    INDEX idx_product_name (product_name),
    INDEX idx_sku (SKU),
    INDEX idx_barcode (barcode),
    CONSTRAINT fk_product_category
        FOREIGN KEY (category_id) REFERENCES Category(category_id)
            ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_product_supplier
        FOREIGN KEY (supplier_id) REFERENCES Supplier(supplier_id)
            ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: Stock
-- ============================================================
DROP TABLE IF EXISTS Stock;
CREATE TABLE Stock (
    stock_id             INT AUTO_INCREMENT PRIMARY KEY,
    product_id           INT NOT NULL UNIQUE,
    location_code        VARCHAR(50) NOT NULL,
    current_quantity     INT NOT NULL DEFAULT 0,
    minimum_quantity     INT NOT NULL DEFAULT 0,
    maximum_quantity     INT NOT NULL DEFAULT 9999,
    last_updated         DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by           VARCHAR(50) NOT NULL,
    INDEX idx_product (product_id),
    INDEX idx_location (location_code),
    CONSTRAINT fk_stock_product
        FOREIGN KEY (product_id) REFERENCES Product(product_id)
            ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: Sales Transaction
-- ============================================================
DROP TABLE IF EXISTS SalesTransaction;
CREATE TABLE SalesTransaction (
    sale_id              INT AUTO_INCREMENT PRIMARY KEY,
    product_id           INT NOT NULL,
    transaction_date     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    quantity_sold        INT NOT NULL CHECK (quantity_sold > 0),
    unit_price_at_sale   DECIMAL(10,2) NOT NULL,
    subtotal             DECIMAL(10,2) GENERATED ALWAYS AS (quantity_sold * unit_price_at_sale) STORED,
    vat_amount           DECIMAL(10,2) GENERATED ALWAYS AS (ROUND(quantity_sold * unit_price_at_sale * 0.15, 2)) STORED,
    total_amount         DECIMAL(10,2) GENERATED ALWAYS AS (quantity_sold * unit_price_at_sale * 1.15) STORED,
    payment_method       VARCHAR(20) NOT NULL,   -- Cash, Card, etc.
    cashier_id           INT,                   -- references User later
    customer_type        VARCHAR(20),           -- Retail, Wholesale
    INDEX idx_product (product_id),
    INDEX idx_transaction_date (transaction_date),
    CONSTRAINT fk_sales_product
        FOREIGN KEY (product_id) REFERENCES Product(product_id)
            ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: LowStockAlert
-- ============================================================
DROP TABLE IF EXISTS LowStockAlert;
CREATE TABLE LowStockAlert (
    alert_id             INT AUTO_INCREMENT PRIMARY KEY,
    product_id           INT NOT NULL,
    alert_date           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    alert_type           VARCHAR(20) NOT NULL,   -- reorder_required, informational, critical
    current_quantity     INT NOT NULL,
    threshold_value      INT NOT NULL,
    status               VARCHAR(15) NOT NULL DEFAULT 'PENDING', -- PENDING, RESOLVED, CLEARED
    resolved_by          VARCHAR(50),
    notification_sent    BOOLEAN DEFAULT FALSE,
    INDEX idx_product (product_id),
    INDEX idx_status (status),
    INDEX idx_alert_date (alert_date),
    CONSTRAINT fk_alert_product
        FOREIGN KEY (product_id) REFERENCES Product(product_id)
            ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: Role
-- ============================================================
DROP TABLE IF EXISTS Role;
CREATE TABLE Role (
    role_id              INT AUTO_INCREMENT PRIMARY KEY,
    role_name            VARCHAR(30) NOT NULL UNIQUE,
    permissions          TEXT,   -- JSON or comma-separated list of allowed actions
    created_at           DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at           DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: User
-- ============================================================
DROP TABLE IF EXISTS User;
CREATE TABLE User (
    user_id              INT AUTO_INCREMENT PRIMARY KEY,
    username             VARCHAR(30) NOT NULL UNIQUE,
    email                VARCHAR(100) NOT NULL UNIQUE,
    full_name            VARCHAR(100) NOT NULL,
    role_id              INT NOT NULL,
    password_hash        VARCHAR(255) NOT NULL,
    last_login           DATETIME,
    is_active            BOOLEAN DEFAULT TRUE,
    created_at           DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at           DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role_id),
    CONSTRAINT fk_user_role
        FOREIGN KEY (role_id) REFERENCES Role(role_id)
            ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add foreign key for cashier_id in SalesTransaction after User table exists
ALTER TABLE SalesTransaction
    ADD CONSTRAINT fk_sales_cashier
    FOREIGN KEY (cashier_id) REFERENCES User(user_id)
        ON DELETE SET NULL ON UPDATE CASCADE;

-- ============================================================
-- TABLE: PurchaseOrder
-- ============================================================
DROP TABLE IF EXISTS PurchaseOrder;
CREATE TABLE PurchaseOrder (
    po_id                INT AUTO_INCREMENT PRIMARY KEY,
    supplier_id          INT NOT NULL,
    created_by           INT NOT NULL,
    po_date              DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expected_delivery    DATE NOT NULL,
    status               VARCHAR(20) NOT NULL DEFAULT 'PENDING', -- PENDING, APPROVED, RECEIVED, CANCELLED
    total_amount         DECIMAL(10,2) NOT NULL DEFAULT 0,
    approved_by          INT,
    received_date        DATE,
    notes                TEXT,
    created_at           DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at           DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_supplier (supplier_id),
    INDEX idx_created_by (created_by),
    INDEX idx_approved_by (approved_by),
    INDEX idx_status (status),
    INDEX idx_po_date (po_date),
    CONSTRAINT fk_po_supplier
        FOREIGN KEY (supplier_id) REFERENCES Supplier(supplier_id)
            ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_po_created_by
        FOREIGN KEY (created_by) REFERENCES User(user_id)
            ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_po_approved_by
        FOREIGN KEY (approved_by) REFERENCES User(user_id)
            ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: PurchaseOrderItem
-- ============================================================
DROP TABLE IF EXISTS PurchaseOrderItem;
CREATE TABLE PurchaseOrderItem (
    po_item_id           INT AUTO_INCREMENT PRIMARY KEY,
    po_id                INT NOT NULL,
    product_id           INT NOT NULL,
    quantity             INT NOT NULL CHECK (quantity > 0),
    unit_cost            DECIMAL(10,2) NOT NULL,
    total_cost           DECIMAL(10,2) GENERATED ALWAYS AS (quantity * unit_cost) STORED,
    quantity_received    INT DEFAULT 0,
    INDEX idx_po (po_id),
    INDEX idx_product (product_id),
    CONSTRAINT fk_poi_po
        FOREIGN KEY (po_id) REFERENCES PurchaseOrder(po_id)
            ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_poi_product
        FOREIGN KEY (product_id) REFERENCES Product(product_id)
            ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: ReportLog
-- ============================================================
DROP TABLE IF EXISTS ReportLog;
CREATE TABLE ReportLog (
    report_id            INT AUTO_INCREMENT PRIMARY KEY,
    user_id              INT NOT NULL,
    report_date          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    report_type          VARCHAR(30) NOT NULL,   -- daily_sales, low_stock, inventory_value, supplier_performance
    date_range_start     DATE,
    date_range_end       DATE,
    format               VARCHAR(10) NOT NULL,   -- PDF, EXCEL, SCREEN
    file_location        VARCHAR(255),
    INDEX idx_user (user_id),
    INDEX idx_report_type (report_type),
    INDEX idx_report_date (report_date),
    CONSTRAINT fk_report_user
        FOREIGN KEY (user_id) REFERENCES User(user_id)
            ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: AuditLog
-- ============================================================
DROP TABLE IF EXISTS AuditLog;
CREATE TABLE AuditLog (
    audit_id             INT AUTO_INCREMENT PRIMARY KEY,
    user_id              INT NOT NULL,
    action_type          VARCHAR(30) NOT NULL,   -- INSERT, UPDATE, DELETE
    table_name           VARCHAR(50) NOT NULL,
    record_id            INT NOT NULL,
    old_value            TEXT,
    new_value            TEXT,
    timestamp            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ip_address           VARCHAR(45),
    INDEX idx_user (user_id),
    INDEX idx_table (table_name),
    INDEX idx_timestamp (timestamp),
    CONSTRAINT fk_audit_user
        FOREIGN KEY (user_id) REFERENCES User(user_id)
            ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- INSERT INITIAL DATA (Sample)
-- ============================================================

-- Categories
INSERT INTO Category (category_name, description) VALUES
('Bakery', 'Fresh breads and baked goods'),
('Dairy', 'Milk, cheese, yogurt products'),
('Pantry', 'Rice, pasta, canned goods'),
('Beverages', 'Soft drinks, juices, water');

-- Suppliers
INSERT INTO Supplier (supplier_name, contact_person, phone, email, address, payment_terms, rating) VALUES
('Fresh Foods SA', 'Peter Nkosi', '011-555-1234', 'peter@freshfoods.co.za', '123 Main St, JHB', 'Net 30', 4.5),
('Dairy Direct', 'Sarah Mokoena', '011-555-5678', 'sarah@dairydirect.co.za', '456 Oak Ave, PTA', 'Net 15', 4.8),
('Grain Supply Co', 'James Naidoo', '011-555-9012', 'james@grain.co.za', '789 Pine Rd, DBN', 'Net 30', 4.2);

-- Products
INSERT INTO Product (SKU, barcode, product_name, category_id, supplier_id, unit_price, cost_price, reorder_level, expiry_date) VALUES
('SKU-001', '6001234567890', 'White Bread (700g)', 1, 1, 15.99, 10.50, 20, '2026-05-30'),
('SKU-002', '6001234567891', 'Fresh Milk (2L)', 2, 2, 32.50, 22.00, 15, '2026-05-25'),
('SKU-003', '6001234567892', 'Rice (5kg)', 3, 3, 89.99, 65.00, 10, '2026-12-31');

-- Stock
INSERT INTO Stock (product_id, location_code, current_quantity, minimum_quantity, maximum_quantity, updated_by) VALUES
(1, 'A-01-SHELF', 145, 20, 200, 'clerk001'),
(2, 'A-02-COOLER', 12, 15, 100, 'clerk001'),
(3, 'B-03-PALLET', 18, 10, 150, 'clerk002');

-- Roles
INSERT INTO Role (role_name, permissions) VALUES
('Stock Clerk', 'view_stock, update_stock, view_alerts, create_po'),
('Branch Manager', 'view_stock, generate_reports, set_thresholds, approve_orders, manage_suppliers'),
('System Admin', 'full_access, manage_users, manage_products, view_audit');

-- Users (passwords are placeholders, use bcrypt in production)
INSERT INTO User (username, email, full_name, role_id, password_hash) VALUES
('clerk001', 'j.smith@store.co.za', 'John Smith', 1, '$2y$10$xyz...'),
('mgr001', 'm.jones@store.co.za', 'Mary Jones', 2, '$2y$10$abc...'),
('admin001', 's.admin@store.co.za', 'Sarah Admin', 3, '$2y$10$def...');

-- Sales Transactions
INSERT INTO SalesTransaction (product_id, transaction_date, quantity_sold, unit_price_at_sale, payment_method, cashier_id, customer_type) VALUES
(1, '2026-05-11 09:30:00', 23, 15.99, 'Cash', 1, 'Retail'),
(2, '2026-05-11 10:15:00', 8, 32.50, 'Card', 1, 'Retail'),
(3, '2026-05-11 14:45:00', 12, 15.99, 'Cash', 1, 'Wholesale');

-- Low Stock Alerts
INSERT INTO LowStockAlert (product_id, alert_type, current_quantity, threshold_value, status) VALUES
(2, 'reorder_required', 12, 15, 'PENDING'),
(3, 'reorder_required', 8, 10, 'RESOLVED'),
(1, 'informational', 45, 20, 'CLEARED');

-- Purchase Orders
INSERT INTO PurchaseOrder (supplier_id, created_by, expected_delivery, status, total_amount, approved_by) VALUES
(2, 1, '2026-05-15', 'PENDING', 975.00, NULL),
(1, 1, '2026-05-14', 'APPROVED', 1245.50, 2),
(3, 1, '2026-05-12', 'RECEIVED', 2100.00, 2);

-- Purchase Order Items
INSERT INTO PurchaseOrderItem (po_id, product_id, quantity, unit_cost) VALUES
(1, 2, 30, 32.50),
(2, 1, 50, 24.91),
(3, 3, 20, 105.00);

-- Report Log
INSERT INTO ReportLog (user_id, report_date, report_type, date_range_start, date_range_end, format, file_location) VALUES
(2, '2026-05-11 09:00:00', 'daily_sales', '2026-05-10', '2026-05-10', 'PDF', '/reports/sales_20260510.pdf'),
(2, '2026-05-11 09:30:00', 'low_stock', '2026-05-01', '2026-05-11', 'SCREEN', NULL),
(1, '2026-05-10 16:00:00', 'inventory_value', '2026-05-10', '2026-05-10', 'EXCEL', '/reports/inv_20260510.xlsx');

-- Audit Log
INSERT INTO AuditLog (user_id, action_type, table_name, record_id, old_value, new_value, ip_address) VALUES
(1, 'UPDATE', 'stock', 1, '{"quantity":150}', '{"quantity":145}', '192.168.1.100'),
(2, 'INSERT', 'purchase_order', 1, NULL, '{"po_id":1}', '192.168.1.101'),
(1, 'UPDATE', 'product', 2, '{"reorder_level":10}', '{"reorder_level":15}', '192.168.1.100');

-- ============================================================
-- VIEWS (Optional)
-- ============================================================

-- View to show product with stock and supplier details
CREATE OR REPLACE VIEW vw_product_stock AS
SELECT
    p.product_id,
    p.SKU,
    p.product_name,
    p.unit_price,
    p.cost_price,
    p.reorder_level,
    s.current_quantity,
    s.location_code,
    s.minimum_quantity,
    s.maximum_quantity,
    sup.supplier_name,
    sup.contact_person,
    c.category_name
FROM Product p
LEFT JOIN Stock s ON p.product_id = s.product_id
LEFT JOIN Supplier sup ON p.supplier_id = sup.supplier_id
LEFT JOIN Category c ON p.category_id = c.category_id
WHERE p.is_active = TRUE;

-- View for low stock items
CREATE OR REPLACE VIEW vw_low_stock AS
SELECT
    p.product_id,
    p.product_name,
    s.current_quantity,
    p.reorder_level,
    sup.supplier_name,
    sup.contact_person,
    sup.phone,
    (p.reorder_level * 2) AS recommended_order_qty  -- simple rule
FROM Product p
JOIN Stock s ON p.product_id = s.product_id
JOIN Supplier sup ON p.supplier_id = sup.supplier_id
WHERE s.current_quantity <= p.reorder_level
AND p.is_active = TRUE;

-- ============================================================
-- TRIGGERS (Example: auto-create low stock alert)
-- ============================================================
DELIMITER //

CREATE TRIGGER trg_after_stock_update
AFTER UPDATE ON Stock
FOR EACH ROW
BEGIN
    DECLARE threshold INT;
    -- Get the reorder level from Product table
    SELECT reorder_level INTO threshold
    FROM Product
    WHERE product_id = NEW.product_id;

    -- If current quantity falls below threshold and there's no pending alert, create one
    IF NEW.current_quantity <= threshold AND
       (SELECT COUNT(*) FROM LowStockAlert WHERE product_id = NEW.product_id AND status = 'PENDING') = 0
    THEN
        INSERT INTO LowStockAlert (product_id, alert_type, current_quantity, threshold_value, status)
        VALUES (NEW.product_id, 'reorder_required', NEW.current_quantity, threshold, 'PENDING');
    END IF;
END //

DELIMITER ;

-- ============================================================
-- INDEXES FOR PERFORMANCE (Additional)
-- ============================================================
CREATE INDEX idx_sales_cashier ON SalesTransaction(cashier_id);
CREATE INDEX idx_audit_user_timestamp ON AuditLog(user_id, timestamp);
CREATE INDEX idx_po_status_date ON PurchaseOrder(status, po_date);
CREATE INDEX idx_stock_quantity ON Stock(current_quantity);

-- ============================================================
-- END OF SCRIPT
-- ============================================================
