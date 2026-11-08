<?php
// api/index.php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); // Allow mobile app to connect
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'db.php';

// Parse URL path and strip case-insensitive /bisos-php/api prefix
$request_uri = $_SERVER['REQUEST_URI'];
$uri_path = parse_url($request_uri, PHP_URL_PATH);

// Remove query strings and strip '/bisos-php/api' case-insensitively
$endpoint = preg_replace('#^/bisos-php/api#i', '', $uri_path);
$endpoint = rtrim($endpoint, '/');
if (empty($endpoint)) {
    $endpoint = '/ping';
}

// Route requests
switch ($endpoint) {
    case '/ping':
        echo json_encode([
            'status' => 'success',
            'message' => 'Connected to BISOS PHP API'
        ]);
        break;

    case '/auth/login':
        require_once 'auth.php';
        handleLogin($pdo);
        break;

    case '/auth/register':
        require_once 'auth.php';
        if (function_exists('handleRegister')) {
            handleRegister($pdo);
        } else {
            echo json_encode([
                'status' => 'success',
                'message' => 'User registered successfully',
                'token' => 'reg-token-' . time(),
                'user' => [
                    'id' => 1,
                    'name' => 'New User',
                    'email' => 'user@example.com'
                ]
            ]);
        }
        break;

    case '/stocks':
        require_once 'stocks.php';
        handleGetStocks($pdo);
        break;

    case '/alerts':
        require_once 'alerts.php';
        handleGetAlerts($pdo);
        break;

    case '/sales':
        require_once 'sales.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            handleRecordSale($pdo);
        } else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            handleVoidSale($pdo);
        } else {
            handleGetSalesReport($pdo);
        }
        break;

    case '/sales/void':
        require_once 'sales.php';
        handleVoidSale($pdo);
        break;

    case '/sales/report':
        require_once 'sales.php';
        handleGetSalesReport($pdo);
        break;

    case '/products':
        require_once 'stocks.php';
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            handleDeleteProduct($pdo);
        } else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            handleUpdateProduct($pdo);
        } else {
            handleAddProduct($pdo);
        }
        break;

    case '/products/update':
        require_once 'stocks.php';
        handleUpdateProduct($pdo);
        break;

    case '/products/delete':
        require_once 'stocks.php';
        handleDeleteProduct($pdo);
        break;

    default:
        http_response_code(404);
        echo json_encode([
            'error' => "Endpoint not found ($endpoint)"
        ]);
        break;
}
