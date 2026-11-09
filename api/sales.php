<?php
// api/sales.php

function handleRecordSale($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);

    $product_id     = intval($input['productId'] ?? $input['product_id'] ?? 0);
    $quantity       = intval($input['quantity'] ?? 1);
    $payment_method = strtoupper(trim($input['paymentMethod'] ?? $input['payment_method'] ?? 'CASH'));
    $store_id       = intval($input['storeId'] ?? $input['store_id'] ?? 1);

    if ($product_id <= 0 || $quantity <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Valid product and quantity required']);
        return;
    }

    try {
        $pdo->beginTransaction();

        // 1. Get product price & details
        $stmtP = $pdo->prepare("SELECT product_id, name, selling_price, reorder_level FROM Product WHERE product_id = ?");
        $stmtP->execute([$product_id]);
        $product = $stmtP->fetch();

        if (!$product) {
            $pdo->rollBack();
            http_response_code(404);
            echo json_encode(['error' => 'Product not found']);
            return;
        }

        $unit_price  = floatval($product['selling_price']);
        $total_price = $unit_price * $quantity;
        $ref         = 'A-' . rand(1000, 9999);

        // 2. Insert SalesTransaction
        $stmtT = $pdo->prepare("INSERT INTO SalesTransaction (store_id, transaction_ref, payment_method, total_amount, status) VALUES (?, ?, ?, ?, 'COMPLETED')");
        $stmtT->execute([$store_id, $ref, $payment_method, $total_price]);
        $transaction_id = $pdo->lastInsertId();

        // 3. Insert SalesItem
        $stmtI = $pdo->prepare("INSERT INTO SalesItem (transaction_id, product_id, quantity, unit_price, total_price) VALUES (?, ?, ?, ?, ?)");
        $stmtI->execute([$transaction_id, $product_id, $quantity, $unit_price, $total_price]);

        // 4. Deduct Stock quantity
        $stmtS = $pdo->prepare("UPDATE Stock SET quantity = GREATEST(0, quantity - ?) WHERE product_id = ?");
        $stmtS->execute([$quantity, $product_id]);

        // 5. Check if low stock alert is needed
        $stmtCheck = $pdo->prepare("SELECT quantity FROM Stock WHERE product_id = ?");
        $stmtCheck->execute([$product_id]);
        $curr_qty = intval($stmtCheck->fetchColumn());

        if ($curr_qty <= intval($product['reorder_level'])) {
            $stmtAlert = $pdo->prepare("INSERT INTO LowStockAlert (product_id, current_quantity, reorder_level, status) VALUES (?, ?, ?, 'PENDING') ON DUPLICATE KEY UPDATE current_quantity = VALUES(current_quantity), status = 'PENDING'");
            $stmtAlert->execute([$product_id, $curr_qty, $product['reorder_level']]);
        }

        $pdo->commit();

        echo json_encode([
            'status' => 'success',
            'message' => 'Sale recorded successfully',
            'transaction_id' => $transaction_id
        ]);

    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['error' => 'Failed to record sale: ' . $e->getMessage()]);
    }
}

function handleGetSalesReport($pdo) {
    $store_id = $_GET['store_id'] ?? 1;

    // Fetch active completed sales items with store & transaction info
    $query = "SELECT
                si.sales_item_id,
                st.transaction_id,
                st.transaction_ref,
                DATE_FORMAT(st.transaction_date, '%Y-%m-%d') AS date,
                st.payment_method,
                COALESCE(st.store_id, 1) AS store_id,
                p.product_id,
                p.name AS product_name,
                si.quantity,
                si.unit_price,
                si.total_price
              FROM SalesItem si
              JOIN SalesTransaction st ON si.transaction_id = st.transaction_id
              JOIN Product p ON si.product_id = p.product_id
              WHERE st.store_id = ? AND st.status = 'COMPLETED'
              ORDER BY st.transaction_id DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute([$store_id]);
    $rows = $stmt->fetchAll();

    $total_sales = 0.0;
    $cash_total  = 0.0;
    $card_total  = 0.0;
    $mobile_total= 0.0;
    $items = [];

    foreach ($rows as $row) {
        $tot = floatval($row['total_price']);
        $total_sales += $tot;
        $pm = strtoupper($row['payment_method'] ?? 'CASH');

        if ($pm === 'CASH') {
            $cash_total += $tot;
        } else if ($pm === 'CARD') {
            $card_total += $tot;
        } else {
            $mobile_total += $tot;
        }

        $items[] = [
            'transaction_id' => intval($row['transaction_id']),
            'transaction_ref' => $row['transaction_ref'] ?: ('A-' . (4500 + intval($row['transaction_id']))),
            'date' => $row['date'] ?: date('Y-m-m'),
            'product_id' => intval($row['product_id']),
            'product_name' => $row['product_name'],
            'quantity' => intval($row['quantity']),
            'unit_price' => floatval($row['unit_price']),
            'total' => $tot,
            'payment_method' => $pm,
            'store_name' => 'Soweto'
        ];
    }

    $transaction_count = count($rows);
    $avg_sale = $transaction_count > 0 ? $total_sales / $transaction_count : 0.0;

    echo json_encode([
        'status' => 'success',
        'data' => [
            'date' => 'March 2026',
            'branch' => 'Soweto Branch',
            'total_sales' => $total_sales,
            'cash_total' => $cash_total,
            'card_total' => $card_total,
            'mobile_total' => $mobile_total,
            'transaction_count' => $transaction_count,
            'average_sale' => $avg_sale,
            'items' => $items
        ]
    ]);
}

function handleVoidSale($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    $transaction_id = intval($_GET['id'] ?? $input['transactionId'] ?? $input['transaction_id'] ?? 0);

    if ($transaction_id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Transaction ID required']);
        return;
    }

    try {
        $pdo->beginTransaction();

        // 1. Get sold items
        $stmtI = $pdo->prepare("SELECT product_id, quantity FROM SalesItem WHERE transaction_id = ?");
        $stmtI->execute([$transaction_id]);
        $items = $stmtI->fetchAll();

        // 2. Restore Stock quantity for each item
        $stmtR = $pdo->prepare("UPDATE Stock SET quantity = quantity + ? WHERE product_id = ?");
        foreach ($items as $item) {
            $stmtR->execute([intval($item['quantity']), intval($item['product_id'])]);
        }

        // 3. Mark SalesTransaction as VOIDED
        $stmtV = $pdo->prepare("UPDATE SalesTransaction SET status = 'VOIDED' WHERE transaction_id = ?");
        $stmtV->execute([$transaction_id]);

        $pdo->commit();

        echo json_encode([
            'status' => 'success',
            'message' => 'Sale voided and stock restored successfully'
        ]);

    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['error' => 'Failed to void sale: ' . $e->getMessage()]);
    }
}
