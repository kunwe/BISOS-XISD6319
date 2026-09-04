<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

// Optional: auto-logout after 30 minutes (client-side handled with JS)
// For server-side, we can check last activity
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset();
    session_destroy();
    redirect('login.php?timeout=1');
}
$_SESSION['last_activity'] = time();

// Role-based access control for specific pages can be added here.
?>