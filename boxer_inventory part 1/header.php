<?php
if (!isset($page_title)) $page_title = 'Informal Market';
// Base URL for root-relative links (adjust if your project folder changes)
$base_url = '/boxer_inventory';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> | Boxer Superstores</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 (icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/css/style.css">
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-boxes"></i> Informal Market
    </div>
    <hr>
    <nav class="sidebar-nav">
        <a href="<?php echo $base_url; ?>/index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>

        <?php if (hasPermission('view_stock')): ?>
        <a href="<?php echo $base_url; ?>/stock_clerk/view_stock.php" class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'view_stock') !== false ? 'active' : ''; ?>">
            <i class="fas fa-eye"></i> View Stock
        </a>
        <?php endif; ?>

        <?php if (hasPermission('update_stock')): ?>
        <a href="<?php echo $base_url; ?>/stock_clerk/update_stock.php" class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'update_stock') !== false ? 'active' : ''; ?>">
            <i class="fas fa-edit"></i> Update Stock
        </a>
        <?php endif; ?>

        <?php if (hasPermission('view_alerts')): ?>
        <a href="<?php echo $base_url; ?>/stock_clerk/low_stock_alerts.php" class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'low_stock_alerts') !== false ? 'active' : ''; ?>">
            <i class="fas fa-bell"></i> Alerts <span class="badge bg-danger"><?php 
                $count = $conn->query("SELECT COUNT(*) as cnt FROM low_stock_alert WHERE status='PENDING'")->fetch_assoc()['cnt'];
                echo $count; 
            ?></span>
        </a>
        <?php endif; ?>

        <?php if (hasPermission('generate_reports')): ?>
        <div class="nav-section-title">Reports</div>
        <a href="<?php echo $base_url; ?>/manager/daily_sales.php" class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'daily_sales') !== false ? 'active' : ''; ?>">
            <i class="fas fa-chart-line"></i> Daily Sales
        </a>
        <a href="<?php echo $base_url; ?>/manager/low_stock_report.php" class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'low_stock_report') !== false ? 'active' : ''; ?>">
            <i class="fas fa-exclamation-triangle"></i> Low Stock Report
        </a>
        <a href="<?php echo $base_url; ?>/manager/inventory_valuation.php" class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'inventory_valuation') !== false ? 'active' : ''; ?>">
            <i class="fas fa-file-invoice-dollar"></i> Valuation
        </a>
        <a href="<?php echo $base_url; ?>/manager/set_threshold.php" class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'set_threshold') !== false ? 'active' : ''; ?>">
            <i class="fas fa-sliders-h"></i> Set Thresholds
        </a>
        <?php endif; ?>

        <?php if (hasPermission('manage_products') || hasPermission('manage_users')): ?>
        <div class="nav-section-title">Admin</div>
        <?php endif; ?>
        <?php if (hasPermission('manage_products')): ?>
        <a href="<?php echo $base_url; ?>/admin/manage_products.php" class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'manage_products') !== false || strpos($_SERVER['PHP_SELF'], 'add_product') !== false || strpos($_SERVER['PHP_SELF'], 'edit_product') !== false ? 'active' : ''; ?>">
            <i class="fas fa-cubes"></i> Products
        </a>
        <?php endif; ?>
        <?php if (hasPermission('manage_users')): ?>
        <a href="<?php echo $base_url; ?>/admin/manage_users.php" class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'manage_users') !== false || strpos($_SERVER['PHP_SELF'], 'add_user') !== false || strpos($_SERVER['PHP_SELF'], 'edit_user') !== false ? 'active' : ''; ?>">
            <i class="fas fa-users-cog"></i> Users
        </a>
        <?php endif; ?>

        <hr>
        <a href="<?php echo $base_url; ?>/logout.php" class="nav-link text-danger">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">
    <!-- Top Navbar -->
    <nav class="top-navbar">
        <div class="d-flex justify-content-between align-items-center w-100">
            <h5 class="m-0"><?php echo $page_title; ?></h5>
            <div class="d-flex align-items-center">
                <span class="me-3 text-muted">
                    <i class="fas fa-user-circle"></i> <?php echo $_SESSION['full_name']; ?>
                    <span class="badge bg-secondary ms-1"><?php echo $_SESSION['role']; ?></span>
                </span>
                <a href="<?php echo $base_url; ?>/logout.php" class="btn btn-sm btn-outline-danger"><i class="fas fa-sign-out-alt"></i></a>
            </div>
        </div>
    </nav>

    <!-- Page Content Wrapper -->
    <div class="content-wrapper">