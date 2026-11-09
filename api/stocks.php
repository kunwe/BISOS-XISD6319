<?php
// api/stocks.php

function handleGetStocks($pdo) {
    $store_id = $_GET['store_id'] ?? 1;

    $query = "SELECT
                p.product_id,
                p.store_id,
                p.name,
                p.category,
                p.barcode,
                p.cost_price,
                p.selling_price,
                p.reorder_level,
                COALESCE(s.quantity, 0) AS quantity,
                COALESCE(s.minimum_level, 0) AS minimum_level,
                COALESCE(s.maximum_level, 999) AS maximum_level,
                s.location_code
              FROM Product p 
              LEFT JOIN Stock s ON p.product_id = s.product_id
              WHERE p.store_id = ?
              ORDER BY p.product_id DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$store_id]);
    $stocks = $stmt->fetchAll();

    echo json_encode([
        'status' => 'success',
        'data' => $stocks
    ]);
}

function handleAddProduct($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);

    $name     = trim($input['name'] ?? '');
    $price    = floatval($input['price'] ?? 0);
    $stock    = intval($input['stock'] ?? 0);
    $reorder  = intval($input['reorder'] ?? 10);
    $store_id = intval($input['storeId'] ?? $input['store_id'] ?? 1);

    if (empty($name)) {
        http_response_code(400);
        echo json_encode(['error' => 'Product name is required']);
        return;
    }

    try {
        $pdo->beginTransaction();

        // Insert into Product
        $stmtP = $pdo->prepare("INSERT INTO Product (store_id, name, cost_price, selling_price, reorder_level) VALUES (?, ?, ?, ?, ?)");
        $stmtP->execute([$store_id, $name, $price * 0.7, $price, $reorder]);
        $product_id = $pdo->lastInsertId();

        // Insert into Stock
        $stmtS = $pdo->prepare("INSERT INTO Stock (product_id, quantity, minimum_level, maximum_level, location_code) VALUES (?, ?, ?, 999, 'A-01')");
        $stmtS->execute([$product_id, $stock, $reorder]);

        $pdo->commit();

        echo json_encode([
            'status' => 'success',
            'message' => 'Product and stock added successfully',
            'product_id' => $product_id
        ]);
    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['error' => 'Failed to add product: ' . $e->getMessage()]);
    }
}

function handleUpdateProduct($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);

    $product_id = intval($input['id'] ?? $input['productId'] ?? $input['product_id'] ?? 0);
    $name       = trim($input['name'] ?? '');
    $price      = floatval($input['price'] ?? $input['sellingPrice'] ?? 0);
    $stock      = intval($input['stock'] ?? $input['quantity'] ?? 0);
    $reorder    = intval($input['reorder'] ?? $input['reorderLevel'] ?? 50);
    $barcode    = trim($input['barcode'] ?? '');

    if ($product_id <= 0 || empty($name)) {
        http_response_code(400);
        echo json_encode(['error' => 'Product ID and name are required']);
        return;
    }

    try {
        $pdo->beginTransaction();

        // Update Product
        $stmtP = $pdo->prepare("UPDATE Product SET name = ?, selling_price = ?, reorder_level = ?, barcode = ? WHERE product_id = ?");
        $stmtP->execute([$name, $price, $reorder, $barcode, $product_id]);

        // Update Stock
        $stmtS = $pdo->prepare("UPDATE Stock SET quantity = ? WHERE product_id = ?");
        $stmtS->execute([$stock, $product_id]);

        $pdo->commit();

        echo json_encode([
            'status' => 'success',
            'message' => 'Product updated successfully'
        ]);
    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update product: ' . $e->getMessage()]);
    }
}

function handleDeleteProduct($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    $product_id = intval($_GET['id'] ?? $input['productId'] ?? $input['product_id'] ?? 0);

    if ($product_id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Product ID is required']);
        return;
    }

    try {
        $pdo->beginTransaction();

        // Delete from LowStockAlert, Stock, and Product
        $stmtA = $pdo->prepare("DELETE FROM LowStockAlert WHERE product_id = ?");
        $stmtA->execute([$product_id]);

        $stmtS = $pdo->prepare("DELETE FROM Stock WHERE product_id = ?");
        $stmtS->execute([$product_id]);

        $stmtP = $pdo->prepare("DELETE FROM Product WHERE product_id = ?");
        $stmtP->execute([$product_id]);

        $pdo->commit();

        echo json_encode([
            'status' => 'success',
            'message' => 'Product deleted successfully'
        ]);
    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete product: ' . $e->getMessage()]);
    }
}
