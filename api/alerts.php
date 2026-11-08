<?php
// api/alerts.php

function handleGetAlerts($pdo) {
    $store_id = intval($_GET['store_id'] ?? 1);

    // Return all stock items with quantity < 50 for the specified store
    $query = "SELECT
                p.product_id,
                p.name,
                s.quantity AS current_quantity,
                50 AS reorder_level,
                CASE
                    WHEN s.quantity <= 10 THEN 'CRITICAL'
                    WHEN s.quantity <= 25 THEN 'HIGH'
                    ELSE 'MEDIUM'
                END AS severity,
                'PENDING' AS status
              FROM Product p
              JOIN Stock s ON p.product_id = s.product_id
              WHERE p.store_id = ? AND s.quantity < 50
              ORDER BY s.quantity ASC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$store_id]);
    $alerts = $stmt->fetchAll();

    echo json_encode([
        'status' => 'success',
        'data' => $alerts
    ]);
}
