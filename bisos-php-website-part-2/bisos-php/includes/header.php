<?php
require_once __DIR__ . '/../config/config.php';

$pageTitle  = $pageTitle  ?? 'BISOS';
$extraCss   = $extraCss   ?? [];
$bodyClass  = $bodyClass  ?? '';
$activePage = $activePage ?? '';
$showNav    = $showNav    ?? true;
$inlineStyle = $inlineStyle ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | BISOS</title>
    <link rel="stylesheet" href="css/style.css">
    <?php foreach ($extraCss as $css): ?>
        <link rel="stylesheet" href="<?= e($css) ?>">
    <?php endforeach; ?>
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php if ($inlineStyle): ?><style><?= $inlineStyle ?></style><?php endif; ?>
</head>
<body class="<?= e($bodyClass) ?>">
<?php if ($showNav): include __DIR__ . '/nav.php'; endif; ?>