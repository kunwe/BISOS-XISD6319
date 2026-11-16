<?php
// api.php - Simple REST API for BISOS
require_once __DIR__ . '/config/config.php';
require_login();

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$store_id = isset($_GET['store_id']) ? (int)$_GET['store_id'] : null;

try {
    switch ($action) {
        case 'get_stocks':
            if ($store_id) {
                $stmt = $pdo->prepare("
                    SELECT p.product_id, p.name, p.category, s.quantity as stock, p.reorder_level, s.location_code as location
                      FROM Product p
                      JOIN Stock s ON p.product_id = s.product_id
                     WHERE p.store_id = ?
                ");
                $stmt->execute([$store_id]);
                echo json_encode($stmt->fetchAll());
            } else {
                $stmt = $pdo->query("
                    SELECT p.product_id, p.name, p.category, s.quantity as stock, p.reorder_level, s.location_code as location
                      FROM Product p
                      JOIN Stock s ON p.product_id = s.product_id
                ");
                echo json_encode($stmt->fetchAll());
            }
            break;

        case 'get_dashboard_stats':
            $products = $store_id
                ? (function() use ($pdo, $store_id) { $q = $pdo->prepare("SELECT COUNT(*) FROM Product WHERE store_id = ?"); $q->execute([$store_id]); return $q->fetchColumn(); })()
                : $pdo->query("SELECT COUNT(*) FROM Product")->fetchColumn();

            // Count low-stock items directly from Stock vs reorder/minimum level,
            // so the dashboard always matches the Alerts and Stocks pages.
            if ($store_id) {
                $q = $pdo->prepare("
                    SELECT COUNT(*) FROM Product p
                      JOIN Stock s ON p.product_id = s.product_id
                     WHERE p.store_id = ? AND s.quantity <= GREATEST(p.reorder_level, s.minimum_level)
                ");
                $q->execute([$store_id]);
                $alerts = $q->fetchColumn();
            } else {
                $alerts = $pdo->query("
                    SELECT COUNT(*) FROM Product p
                      JOIN Stock s ON p.product_id = s.product_id
                     WHERE s.quantity <= GREATEST(p.reorder_level, s.minimum_level)
                ")->fetchColumn();
            }

            // Breakdown: how many are out of stock vs low.
            if ($store_id) {
                $q = $pdo->prepare("
                    SELECT COUNT(*) FROM Product p
                      JOIN Stock s ON p.product_id = s.product_id
                     WHERE p.store_id = ? AND s.quantity <= 0
                ");
                $q->execute([$store_id]);
                $outCount = $q->fetchColumn();
            } else {
                $outCount = $pdo->query("
                    SELECT COUNT(*) FROM Product p
                      JOIN Stock s ON p.product_id = s.product_id
                     WHERE s.quantity <= 0
                ")->fetchColumn();
            }

            $sales = $store_id
                ? (function() use ($pdo, $store_id) { $q = $pdo->prepare("SELECT COALESCE(SUM(total_amount),0) FROM SalesTransaction WHERE DATE(transaction_date) = CURDATE() AND store_id = ?"); $q->execute([$store_id]); return $q->fetchColumn(); })()
                : $pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM SalesTransaction WHERE DATE(transaction_date) = CURDATE()")->fetchColumn();
            $stores = $pdo->query("SELECT COUNT(*) FROM Store WHERE is_active = TRUE")->fetchColumn();

            // Fetch the actual low-stock items for the dashboard list.
            $listSql = "
                SELECT p.product_id, p.name, p.category,
                       GREATEST(p.reorder_level, s.minimum_level) AS reorder_level,
                       s.quantity, st.name AS store_name, st.store_id
                  FROM Product p
                  JOIN Stock s ON p.product_id = s.product_id
                  JOIN Store st ON p.store_id = st.store_id
                 WHERE s.quantity <= GREATEST(p.reorder_level, s.minimum_level)
            ";
            $listParams = [];
            if ($store_id) {
                $listSql .= " AND p.store_id = ?";
                $listParams[] = $store_id;
            }
            $listSql .= " ORDER BY FIELD(p.reorder_level, 0) DESC, s.quantity ASC, p.name ASC";
            $lq = $pdo->prepare($listSql);
            $lq->execute($listParams);
            $lowItems = $lq->fetchAll();

            echo json_encode([
                'totalProducts' => (int)$products,
                'lowStockAlerts' => (int)$alerts,
                'outOfStock' => (int)$outCount,
                'todaySales' => (float)$sales,
                'totalStores' => (int)$stores,
                'lowStockItems' => $lowItems,
            ]);
            break;

        // ---- Monthly revenue trend (last 6 months) -------------------------
        case 'get_revenue_trend':
            $trendSql = "
                SELECT DATE_FORMAT(transaction_date, '%Y-%m') AS month,
                       SUM(total_amount) AS total
                  FROM SalesTransaction
                 WHERE status = 'COMPLETED'
                   AND transaction_date >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
            ";
            $trendParams = [];
            if ($store_id) {
                $trendSql .= " AND store_id = ?";
                $trendParams[] = $store_id;
            }
            $trendSql .= " GROUP BY month ORDER BY month ASC";
            $tq = $pdo->prepare($trendSql);
            $tq->execute($trendParams);
            $raw = $tq->fetchAll();

            // Build a continuous 6-month series (fill gaps with 0).
            $months = [];
            $series = [];
            for ($i = 5; $i >= 0; $i--) {
                $d = new DateTime("first day of -$i month");
                $key = $d->format('Y-m');
                $label = $d->format('M');
                $months[] = $label;
                $series[$key] = 0.0;
            }
            foreach ($raw as $r) {
                if (isset($series[$r['month']])) {
                    $series[$r['month']] = (float)$r['total'];
                }
            }

            echo json_encode([
                'status' => 'success',
                'months' => $months,
                'values' => array_values($series),
            ]);
            break;

        // ---- Stock by category --------------------------------------------
        case 'get_stock_by_category':
            $catSql = "
                SELECT COALESCE(NULLIF(p.category, ''), 'Uncategorized') AS category,
                       SUM(COALESCE(s.quantity, 0)) AS total_qty,
                       COUNT(DISTINCT p.product_id) AS product_count
                  FROM Product p
                  LEFT JOIN Stock s ON p.product_id = s.product_id
                 WHERE 1=1
            ";
            $catParams = [];
            if ($store_id) {
                $catSql .= " AND p.store_id = ?";
                $catParams[] = $store_id;
            }
            $catSql .= " GROUP BY category ORDER BY total_qty DESC";
            $cq = $pdo->prepare($catSql);
            $cq->execute($catParams);
            $cats = $cq->fetchAll();

            // Assign a colour per category.
            $palette = ['#3498DB', '#27AE60', '#F39C12', '#E74C3C', '#9B59B6', '#1ABC9C', '#E67E22', '#95A5A6'];
            $out = [];
            $idx = 0;
            foreach ($cats as $c) {
                $out[] = [
                    'category' => $c['category'],
                    'total_qty' => (int)$c['total_qty'],
                    'product_count' => (int)$c['product_count'],
                    'color' => $palette[$idx % count($palette)],
                ];
                $idx++;
            }

            echo json_encode(['status' => 'success', 'data' => $out]);
            break;

        // ---- Recent activity (latest sales) -------------------------------
        case 'get_recent_activity':
            $actSql = "
                SELECT st.transaction_ref, st.total_amount, st.payment_method,
                       st.transaction_date, st.status, s.name AS store_name
                  FROM SalesTransaction st
                  JOIN Store s ON st.store_id = s.store_id
                 WHERE 1=1
            ";
            $actParams = [];
            if ($store_id) {
                $actSql .= " AND st.store_id = ?";
                $actParams[] = $store_id;
            }
            $actSql .= " ORDER BY st.transaction_date DESC LIMIT 8";
            $aq = $pdo->prepare($actSql);
            $aq->execute($actParams);
            $acts = $aq->fetchAll();

            // Also pull recent low-stock alerts as activity.
            $lowSql = "
                SELECT p.name AS product_name, s.quantity, p.reorder_level,
                       st.name AS store_name
                  FROM Product p
                  JOIN Stock s ON p.product_id = s.product_id
                  JOIN Store st ON p.store_id = st.store_id
                 WHERE s.quantity <= GREATEST(p.reorder_level, s.minimum_level)
            ";
            $lowParams = [];
            if ($store_id) {
                $lowSql .= " AND p.store_id = ?";
                $lowParams[] = $store_id;
            }
            $lowSql .= " ORDER BY s.quantity ASC LIMIT 5";
            $lq2 = $pdo->prepare($lowSql);
            $lq2->execute($lowParams);
            $lows = $lq2->fetchAll();

            echo json_encode([
                'status' => 'success',
                'sales' => $acts,
                'low_stock' => $lows,
            ]);
            break;

        default:
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}