<?php
// config/db.php

$db_host = getenv('DB_HOST') ?: $_ENV['DB_HOST'] ?? 'localhost';
$db_name = getenv('DB_NAME') ?: $_ENV['DB_NAME'] ?? 'bisos_db';
$db_user = getenv('DB_USER') ?: $_ENV['DB_USER'] ?? 'root';
$db_pass = getenv('DB_PASS') ?: $_ENV['DB_PASS'] ?? '';

if (empty($db_host) || $db_host === 'localhost') {
    $db_host = getenv('DB_HOST_LOCAL') ?: $_ENV['DB_HOST_LOCAL'] ?? 'localhost';
}

try {
    $pdo = new PDO(
        "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4",
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    error_log('Database connection failed: ' . $e->getMessage());
    die('Database connection failed. Please check the environment variables or local database setup.');
}
?>