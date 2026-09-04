<?php
require_once 'config.php';

// Insert categories (if empty)
$categories = [
    ['Bakery', 'Fresh breads and baked goods', null],
    ['Dairy', 'Milk, cheese, yogurt products', null],
    ['Pantry', 'Rice, pasta, canned goods', null],
    ['Beverages', 'Soft drinks, juices, water', null]
];
foreach ($categories as $cat) {
    $conn->query("INSERT INTO category (category_name, description) VALUES ('$cat[0]', '$cat[1]')");
}

// Insert sample products (if empty)
$products = [
    ['SKU-001', '6001234567890', 'White Bread (700g)', 1, 15.99, 20],
    ['SKU-002', '6001234567891', 'Fresh Milk (2L)', 2, 32.50, 15],
    ['SKU-003', '6001234567892', 'Rice (5kg)', 3, 89.99, 10],
    ['SKU-004', '6001234567893', 'Coca-Cola 2L', 4, 18.99, 20]
];
foreach ($products as $p) {
    $stmt = $conn->prepare("INSERT INTO product (SKU, barcode, product_name, category_id, unit_price, reorder_level) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssidi", $p[0], $p[1], $p[2], $p[3], $p[4], $p[5]);
    $stmt->execute();
    $pid = $conn->insert_id;
    $conn->query("INSERT INTO stock (product_id, location_code, current_quantity, minimum_quantity, maximum_quantity) VALUES ($pid, 'LOC-$pid', 50, 5, 200)");
}

// Insert roles already exist from schema, but we'll ensure.
// Insert admin user with password "Password123!"
$hash = password_hash('Password123!', PASSWORD_DEFAULT);
$conn->query("INSERT IGNORE INTO user (username, email, full_name, role_id, password_hash, is_active) VALUES ('admin', 'admin@boxer.co.za', 'System Admin', 3, '$hash', 1)");
$conn->query("INSERT IGNORE INTO user (username, email, full_name, role_id, password_hash, is_active) VALUES ('mgr001', 'mgr@boxer.co.za', 'Branch Manager', 2, '$hash', 1)");
$conn->query("INSERT IGNORE INTO user (username, email, full_name, role_id, password_hash, is_active) VALUES ('clerk001', 'clerk@boxer.co.za', 'Stock Clerk', 1, '$hash', 1)");

echo "Seeding completed. You can now log in with username 'admin' or 'mgr001' or 'clerk001' and password 'Password123!'";