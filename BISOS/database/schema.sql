-- BISOS Database Schema v1.0
-- Author: BISOS Development Team (XISD6319)

CREATE DATABASE IF NOT EXISTS bisos_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bisos_db;

CREATE TABLE Store (
    store_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    address TEXT,
    phone VARCHAR(20),
    email VARCHAR(100),
    manager_name VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE Role (
    role_id INT PRIMARY KEY AUTO_INCREMENT,
    role_name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE User (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT,
    role_id INT NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    phone VARCHAR(20),
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES Store(store_id),
    FOREIGN KEY (role_id) REFERENCES Role(role_id),
    INDEX idx_user_email (email),
    INDEX idx_user_store (store_id)
);

CREATE TABLE Product (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    category VARCHAR(50),
    barcode VARCHAR(50),
    cost_price DECIMAL(10,2) NOT NULL,
    selling_price DECIMAL(10,2) NOT NULL,
    reorder_level INT DEFAULT 0,
    is_perishable BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES Store(store_id),
    INDEX idx_product_store (store_id),
    INDEX idx_product_category (category),
    INDEX idx_product_barcode (barcode)
);

CREATE TABLE Stock (
    stock_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    minimum_level INT DEFAULT 0,
    maximum_level INT DEFAULT 999,
    location_code VARCHAR(20),
    last_count_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES Product(product_id),
    INDEX idx_stock_product (product_id)
);

CREATE TABLE LowStockAlert (
    alert_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    current_quantity INT NOT NULL,
    reorder_level INT NOT NULL,
    alert_type VARCHAR(20) DEFAULT 'LOW_STOCK',
    status ENUM('PENDING','ACKNOWLEDGED','RESOLVED') DEFAULT 'PENDING',
    severity ENUM('LOW','MEDIUM','HIGH','CRITICAL') DEFAULT 'MEDIUM',
    acknowledged_by INT,
    acknowledged_at TIMESTAMP NULL,
    resolved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES Product(product_id),
    FOREIGN KEY (acknowledged_by) REFERENCES User(user_id),
    INDEX idx_alert_status (status),
    INDEX idx_alert_product (product_id)
);

CREATE TABLE Supplier (
    supplier_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    contact_name VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    payment_terms VARCHAR(100),
    rating TINYINT DEFAULT 3 CHECK (rating BETWEEN 1 AND 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE ReorderOrder (
    reorder_id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    supplier_id INT NOT NULL,
    order_date DATE NOT NULL,
    expected_delivery_date DATE,
    actual_delivery_date DATE,
    status ENUM('PENDING','ORDERED','SHIPPED','DELIVERED','CANCELLED') DEFAULT 'PENDING',
    total_amount DECIMAL(10,2),
    notes TEXT,
    placed_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES Store(store_id),
    FOREIGN KEY (supplier_id) REFERENCES Supplier(supplier_id),
    FOREIGN KEY (placed_by) REFERENCES User(user_id),
    INDEX idx_reorder_status (status),
    INDEX idx_reorder_store (store_id)
);

CREATE TABLE ReorderItem (
    reorder_item_id INT PRIMARY KEY AUTO_INCREMENT,
    reorder_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_cost DECIMAL(10,2) NOT NULL,
    total_cost DECIMAL(10,2) NOT NULL,
    received_quantity INT DEFAULT 0,
    FOREIGN KEY (reorder_id) REFERENCES ReorderOrder(reorder_id),
    FOREIGN KEY (product_id) REFERENCES Product(product_id)
);

CREATE TABLE SalesTransaction (
    transaction_id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    transaction_ref VARCHAR(20) UNIQUE,
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    customer_name VARCHAR(100),
    payment_method ENUM('CASH','CARD','MOBILE','CREDIT') NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    tax_amount DECIMAL(10,2) DEFAULT 0,
    discount_amount DECIMAL(10,2) DEFAULT 0,
    processed_by INT,
    status ENUM('COMPLETED','REFUNDED','VOIDED') DEFAULT 'COMPLETED',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES Store(store_id),
    FOREIGN KEY (processed_by) REFERENCES User(user_id),
    INDEX idx_sales_store (store_id),
    INDEX idx_sales_date (transaction_date)
);

CREATE TABLE SalesItem (
    sales_item_id INT PRIMARY KEY AUTO_INCREMENT,
    transaction_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (transaction_id) REFERENCES SalesTransaction(transaction_id),
    FOREIGN KEY (product_id) REFERENCES Product(product_id)
);

CREATE TABLE ReportLog (
    report_id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT,
    report_type VARCHAR(50) NOT NULL,
    format VARCHAR(20) DEFAULT 'PDF',
    generated_by INT NOT NULL,
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    file_path VARCHAR(255),
    parameters JSON,
    FOREIGN KEY (store_id) REFERENCES Store(store_id),
    FOREIGN KEY (generated_by) REFERENCES User(user_id),
    INDEX idx_report_type (report_type),
    INDEX idx_report_date (generated_at)
);

-- Default roles
INSERT INTO Role (role_name, description) VALUES
    ('Store Owner', 'Complete system access and management'),
    ('Manager', 'Full operational access except system configuration'),
    ('Clerk', 'Inventory management and sales operations'),
    ('Cashier', 'Sales transactions only');
