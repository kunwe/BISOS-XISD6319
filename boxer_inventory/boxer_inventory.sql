-- --------------------------------------------------------
-- Table: role
-- --------------------------------------------------------
CREATE TABLE role (
    role_id INT PRIMARY KEY AUTO_INCREMENT,
    role_name VARCHAR(30) UNIQUE NOT NULL,
    permissions TEXT NOT NULL          -- comma separated list of actions
);

-- --------------------------------------------------------
-- Table: user
-- --------------------------------------------------------
CREATE TABLE user (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(30) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role_id INT NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    last_login DATETIME,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (role_id) REFERENCES role(role_id)
);

-- --------------------------------------------------------
-- Table: category
-- --------------------------------------------------------
CREATE TABLE category (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(50) UNIQUE NOT NULL,
    description VARCHAR(255),
    parent_category_id INT NULL,
    FOREIGN KEY (parent_category_id) REFERENCES category(category_id)
);

-- --------------------------------------------------------
-- Table: product
-- --------------------------------------------------------
CREATE TABLE product (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    SKU VARCHAR(30) UNIQUE NOT NULL,
    barcode VARCHAR(50) UNIQUE,
    product_name VARCHAR(100) NOT NULL,
    category_id INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    reorder_level INT NOT NULL DEFAULT 10,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (category_id) REFERENCES category(category_id)
);

-- --------------------------------------------------------
-- Table: stock
-- --------------------------------------------------------
CREATE TABLE stock (
    stock_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL UNIQUE,   -- one‑to‑one with product
    location_code VARCHAR(50) NOT NULL,
    current_quantity INT NOT NULL DEFAULT 0,
    minimum_quantity INT NOT NULL DEFAULT 5,
    maximum_quantity INT NOT NULL DEFAULT 100,
    last_updated DATETIME,
    updated_by VARCHAR(30),
    FOREIGN KEY (product_id) REFERENCES product(product_id)
);

-- --------------------------------------------------------
-- Table: sales_transaction
-- --------------------------------------------------------
CREATE TABLE sales_transaction (
    sale_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    transaction_date DATETIME NOT NULL,
    quantity_sold INT NOT NULL,
    unit_price_at_sale DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    vat_amount DECIMAL(10,2) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES product(product_id)
);

-- --------------------------------------------------------
-- Table: low_stock_alert
-- --------------------------------------------------------
CREATE TABLE low_stock_alert (
    alert_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    alert_date DATETIME NOT NULL,
    alert_type VARCHAR(20) NOT NULL,   -- 'reorder_required' or 'information'
    current_quantity INT NOT NULL,
    threshold_value INT NOT NULL,
    status VARCHAR(15) NOT NULL DEFAULT 'PENDING', -- PENDING, RESOLVED, CLEARED
    resolved_by VARCHAR(30) NULL,
    FOREIGN KEY (product_id) REFERENCES product(product_id)
);

-- --------------------------------------------------------
-- Table: report_log
-- --------------------------------------------------------
CREATE TABLE report_log (
    report_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    report_date DATETIME NOT NULL,
    report_type VARCHAR(30) NOT NULL, -- 'daily_sales', 'low_stock', 'inventory_value'
    date_range_start DATE,
    date_range_end DATE,
    format VARCHAR(10) NOT NULL,       -- PDF, EXCEL, SCREEN
    file_location VARCHAR(255) NULL,
    FOREIGN KEY (user_id) REFERENCES user(user_id)
);

-- Insert initial roles
INSERT INTO role (role_name, permissions) VALUES
('Stock Clerk', 'view_stock,update_stock,view_alerts,acknowledge_alert'),
('Branch Manager', 'view_stock,generate_reports,set_thresholds,approve_orders,view_alerts,acknowledge_alert'),
('System Admin', 'full_access,manage_users,manage_products,view_audit');

-- Insert a few categories and products (sample)
INSERT INTO category (category_name, description) VALUES
('Bakery', 'Fresh breads and baked goods'),
('Dairy', 'Milk, cheese, yogurt products'),
('Pantry', 'Rice, pasta, canned goods');

INSERT INTO product (SKU, barcode, product_name, category_id, unit_price, reorder_level) VALUES
('SKU-001', '6001234567890', 'White Bread (700g)', 1, 15.99, 20),
('SKU-002', '6001234567891', 'Fresh Milk (2L)', 2, 32.50, 15),
('SKU-003', '6001234567892', 'Rice (5kg)', 3, 89.99, 10);

INSERT INTO stock (product_id, location_code, current_quantity, minimum_quantity, maximum_quantity) VALUES
(1, 'A-01-SHELF1', 45, 5, 200),
(2, 'A-02-COOLER1', 12, 5, 100),
(3, 'B-03-PALLET', 18, 5, 150);

-- Insert sample users (passwords: "Password123!" hashed with password_hash)
-- We'll create users with plain password, later we'll use PHP to hash; for now insert with hashed.
-- In a real setup, use PHP to generate these. For demo, we'll insert hashed values using PHP later.
-- We'll handle this in the code. For testing, we'll create a default admin.