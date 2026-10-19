<?php
// config/config.php
session_start();

define('APP_NAME', 'BISOS');
define('DEMO_EMAIL', 'demo@bisos.co.za');
define('DEMO_PASSWORD', 'Demo@123');

// Include the database connection
require_once __DIR__ . '/db.php';

// --- Helpers -------------------------------------------------------------
function e(?string $s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function is_logged_in(): bool {
    return isset($_SESSION['user']);
}

function current_user(): ?array {
    return $_SESSION['user'] ?? null;
}

function require_login(): void {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function redirect_if_logged_in(string $to = 'dashboard.php'): void {
    if (is_logged_in()) {
        header("Location: $to");
        exit;
    }
}

function flash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array {
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}