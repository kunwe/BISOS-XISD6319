<?php
// api/alerts.php

function handleGetAlerts($pdo) {
    $query = "SELECT a.alert_id, p.name, a.current_quantity, a.reorder_level, a.severity, a.status 
              FROM LowStockAlert a 
              JOIN Product p ON a.product_id = p.product_id";
    
    $stmt = $pdo->query($query);
    $alerts = $stmt->fetchAll();

    echo json_encode([
        'status' => 'success',
        'data' => $alerts
    ]);
}