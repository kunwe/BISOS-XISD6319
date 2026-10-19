<?php
require_once __DIR__ . '/../auth.php';
if (!hasPermission('manage_users')) redirect('../index.php');

$id = $_GET['id'] ?? 0;
if ($id && $id != $_SESSION['user_id']) {
    $conn->query("DELETE FROM user WHERE user_id = $id");
}
redirect('manage_users.php');