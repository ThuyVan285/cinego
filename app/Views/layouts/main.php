<?php
/** @var string $content */
/** @var string $title */
/** @var string $nav */
/** @var bool $bare */
?><!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?></title>
    <meta name="description" content="<?= APP_NAME ?> – website đặt vé xem phim trực tuyến.">
    <link rel="icon" href="/favicon.ico">

    <!-- Google Fonts: Bricolage Grotesque & Be Vietnam Pro -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&family=Bricolage+Grotesque:opsz,wght@12..96,700;12..96,800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/responsive.css')) ?>">
    <script>
        (function() {
            var saved = localStorage.getItem('cinego_theme');
            if (saved) {
                document.documentElement.setAttribute('data-theme', saved);
            }
        })();
    </script>
</head>
<body>
<?php if (!$bare) require __DIR__ . '/header.php'; ?>

<main id="main">
    <?php component('Flash'); ?>
    <?= $content ?>
</main>

<?php if (!$bare) require __DIR__ . '/footer.php'; ?>
<?php foreach (['main', 'booking', 'seat-selection', 'payment'] as $js): ?>
<script src="<?= e(asset("js/$js.js")) ?>" defer></script>
<?php endforeach; ?>
</body>
</html>
