<?php
$user   = current_user() ?? ['name' => 'Guest', 'initials' => '??'];
$pages  = [
    ['href' => 'dashboard.php',        'icon' => 'fa-home',                'label' => 'Dashboard', 'id' => 'dashboard'],
    ['href' => 'stocks.php',           'icon' => 'fa-boxes',               'label' => 'Stocks',    'id' => 'stocks'],
    ['href' => 'products.php',         'icon' => 'fa-cube',                'label' => 'Products',  'id' => 'products'],
    ['href' => 'sales-report.php',     'icon' => 'fa-chart-bar',           'label' => 'Reports',   'id' => 'sales-report'],
    ['href' => 'low-stock-alerts.php', 'icon' => 'fa-exclamation-triangle','label' => 'Alerts',    'id' => 'alerts', 'badge' => 8],
    ['href' => 'store-management.php', 'icon' => 'fa-store',               'label' => 'Stores',    'id' => 'stores'],
    ['href' => 'settings.php',         'icon' => 'fa-cog',                 'label' => 'Settings',  'id' => 'settings'],
];
?>
<header role="banner">
    <nav class="navbar" id="main-nav" role="navigation" aria-label="Primary navigation">
        <a href="dashboard.php" class="nav-brand" aria-label="BISOS Home">
            <div class="logo-icon" aria-hidden="true">B</div>
            <span>BISOS</span>
        </a>

        <button class="hamburger" id="hamburger" aria-label="Toggle navigation" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>

        <ul class="nav-menu" id="nav-menu" role="menubar">
            <?php foreach ($pages as $p): ?>
                <li role="none">
                    <a href="<?= e($p['href']) ?>" role="menuitem"
                       class="<?= $activePage === $p['id'] ? 'active' : '' ?>"
                       <?= $activePage === $p['id'] ? 'aria-current="page"' : '' ?>>
                        <i class="fas <?= e($p['icon']) ?>" aria-hidden="true"></i>
                        <?= e($p['label']) ?>
                        <?php if (!empty($p['badge'])): ?>
                            <span class="badge" aria-label="<?= (int)$p['badge'] ?> alerts"><?= (int)$p['badge'] ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="nav-user">
            <div class="user-avatar" aria-hidden="true"><?= e($user['initials']) ?></div>
            <span class="user-name"><?= e($user['name']) ?></span>
            <a href="logout.php" class="logout-btn" aria-label="Logout">
                <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
            </a>
        </div>
    </nav>
</header>
<script>
document.getElementById('hamburger')?.addEventListener('click', function () {
    const menu = document.getElementById('nav-menu');
    const expanded = this.getAttribute('aria-expanded') === 'true';
    menu.classList.toggle('open');
    this.setAttribute('aria-expanded', String(!expanded));
});
</script>