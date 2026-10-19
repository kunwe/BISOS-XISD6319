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

// Simple Router based on URL path
$request_uri = $_SERVER['REQUEST_URI'];
$base_path = '/BISOS-PHP/api'; // Adjust this if your folder name is different
$endpoint = str_replace($base_path, '', $request_uri);
$endpoint = explode('?', $endpoint)[0]; // Remove query strings

// Route requests
switch ($endpoint) {
    case '/auth/login':
        require 'auth.php';
        handleLogin($pdo);
        break;
    case '/stocks':
        require 'stocks.php';
        handleGetStocks($pdo);
        break;
    case '/alerts':
        require 'alerts.php';
        handleGetAlerts($pdo);
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
        break;
}