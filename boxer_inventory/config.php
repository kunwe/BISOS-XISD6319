<?php
session_start();

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'boxer_inventory');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Helper functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserRole() {
    return $_SESSION['role'] ?? null;
}

function hasPermission($permission) {
    if (!isLoggedIn()) return false;
    $perms = explode(',', $_SESSION['permissions']);
    return in_array($permission, $perms) || in_array('full_access', $perms);
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function generateLowStockAlerts() {
    global $conn;
    // Check all products and create alerts if current_quantity <= reorder_level
    $sql = "SELECT p.product_id, p.product_name, p.reorder_level, s.current_quantity 
            FROM product p 
            JOIN stock s ON p.product_id = s.product_id
            WHERE s.current_quantity <= p.reorder_level AND p.is_active = 1";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        // Check if there is already a PENDING alert for this product
        $check = $conn->query("SELECT alert_id FROM low_stock_alert WHERE product_id = {$row['product_id']} AND status = 'PENDING'");
        if ($check->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO low_stock_alert (product_id, alert_date, alert_type, current_quantity, threshold_value, status) VALUES (?, NOW(), 'reorder_required', ?, ?, 'PENDING')");
            $stmt->bind_param("iii", $row['product_id'], $row['current_quantity'], $row['reorder_level']);
            $stmt->execute();
        }
    }
}
?>