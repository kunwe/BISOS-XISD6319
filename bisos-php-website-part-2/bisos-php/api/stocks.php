<?php
// api/stocks.php

function handleGetStocks($pdo) {
    $store_id = $_GET['store_id'] ?? 1; // Default to store 1

    $query = "SELECT p.product_id, p.name, p.category, s.quantity, p.reorder_level, s.location_code 
              FROM Product p 
              JOIN Stock s ON p.product_id = s.product_id 
              WHERE p.store_id = ?";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$store_id]);
    $stocks = $stmt->fetchAll();

    echo json_encode([
        'status' => 'success',
        'data' => $stocks
    ]);
}