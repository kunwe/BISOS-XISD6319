<?php
require_once __DIR__ . '/../auth.php';
if (!hasPermission('manage_products')) redirect('../index.php');

$id = $_GET['id'] ?? 0;
if ($id) {
    // Soft delete: set is_active = 0
    $conn->query("UPDATE product SET is_active = 0 WHERE product_id = $id");
}
redirect('manage_products.php');