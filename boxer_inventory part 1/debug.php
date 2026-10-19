<?php
require_once 'config.php';

$username = 'admin';
$stmt = $conn->prepare("SELECT password_hash FROM user WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

echo "Stored hash for admin: " . $row['password_hash'] . "<br>";

$test_password = 'Password123!';
if (password_verify($test_password, $row['password_hash'])) {
    echo "Password verification: SUCCESS";
} else {
    echo "Password verification: FAILED";
}
?>