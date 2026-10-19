<?php
// api.php - Simple REST API for BISOS
require_once __DIR__ . '/config/config.php';
require_login();

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'get_stocks':
            $stmt = $pdo->query("
                SELECT p.product_id, p.name, p.category, s.quantity as stock, p.reorder_level, s.location_code as location 
                FROM Product p 
                JOIN Stock s ON p.product_id = s.product_id
            ");
            echo json_encode($stmt->fetchAll());
            break;

        case 'get_dashboard_stats':
            // Get Total Products
            $products = $pdo->query("SELECT COUNT(*) FROM Product")->fetchColumn();
            // Get Low Stock Alerts
            $alerts = $pdo->query("SELECT COUNT(*) FROM LowStockAlert WHERE status = 'PENDING'")->fetchColumn();
            // Get Today's Sales
            $sales = $pdo->query("SELECT SUM(total_amount) FROM SalesTransaction WHERE DATE(transaction_date) = CURDATE()")->fetchColumn() ?: 0;
            
            echo json_encode([
                'totalProducts' => $products,
                'lowStockAlerts' => $alerts,
                'todaySales' => $sales
            ]);
            break;

        default:
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}