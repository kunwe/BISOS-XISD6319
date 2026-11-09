<?php
// api/db.php

$host = getenv('DB_HOST') ?: $_ENV['DB_HOST'] ?? 'localhost';
$db   = getenv('DB_NAME') ?: $_ENV['DB_NAME'] ?? 'bisos_db';
$user = getenv('DB_USER') ?: $_ENV['DB_USER'] ?? 'root';
$pass = getenv('DB_PASS') ?: $_ENV['DB_PASS'] ?? '';

$dsn = "mysql:host={$host};charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // 1. Connect without DB selected to ensure database exists
    $pdo_root = new PDO($dsn, $user, $pass, $options);
    $pdo_root->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    // 2. Connect to the specific database
    $pdo = new PDO("mysql:host={$host};dbname={$db};charset=utf8mb4", $user, $pass, $options);

    // 3. Auto-initialize tables and seed data if Product table is missing
    $tables = $pdo->query("SHOW TABLES LIKE 'Product'")->fetchAll();
    if (empty($tables)) {
        $db_dir = __DIR__ . '/../database';
        $schema_file = "$db_dir/schema.sql";
        $seed_file   = "$db_dir/seed.sql";

        if (file_exists($schema_file)) {
            $schema = file_get_contents($schema_file);
            $pdo->exec($schema);
        }
        if (file_exists($seed_file)) {
            $seed = file_get_contents($seed_file);
            $pdo->exec($seed);
        }
    }

} catch (\PDOException $e) {
    header('Content-Type: application/json', true, 500);
    echo json_encode(['error' => 'Database connection or initialization failed: ' . $e->getMessage()]);
    error_log('API DB connection failed: ' . $e->getMessage());
    exit;
}
