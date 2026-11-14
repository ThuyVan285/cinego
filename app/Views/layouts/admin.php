<?php
/** Layout trang quản trị. @var string $content @var string $title @var string $nav @var string $heading */
$me = User::current();
$isAdmin = ($me['role'] ?? '') === 'admin';
// nhóm => [khoá => [đường dẫn, nhãn, icon, chỉ admin?]]
$adminMenu = [
    'Tổng quan' => [
        'dashboard' => ['/admin', 'Bảng điều khiển', 'dashboard', false],
    ],
    'Vận hành' => [
        'bookings' => ['/admin/bookings', 'Đơn đặt vé', 'ticket', false],
        'showtimes' => ['/admin/showtimes', 'Suất chiếu & ghế', 'calendar', false],
    ],
    'Nội dung' => [
        'movies' => ['/admin/movies', 'Phim', 'film', true],
        'genres' => ['/admin/genres', 'Thể loại', 'tags', true],
        'cinemas' => ['/admin/cinemas', 'Rạp chiếu', 'building', true],
        'rooms' => ['/admin/rooms', 'Phòng, ghế & giá vé', 'armchair', true],
        'promotions' => ['/admin/promotions', 'Khuyến mãi', 'percent', true],
    ],
    'Hệ thống' => [
        'users' => ['/admin/users', 'Người dùng & phân quyền', 'users', true],
    ],
];
$current = 'Quản trị';
foreach ($adminMenu as $items) {
    foreach ($items as $k => $it) {
        if ($k === $nav) {
            $current = $it[1];
        }
    }
}
$initial = mb_strtoupper(mb_substr((string)($me['name'] ?? 'A'), 0, 1));
?><!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?></title>
    <link rel="icon" href="/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&family=Bricolage+Grotesque:opsz,wght@12..96,700;12..96,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
    <script>
        (function () { // áp giao diện đã lưu trước khi vẽ trang (tránh nháy màu)
            try { var t = localStorage.getItem('cinego_theme'); if (t) document.documentElement.setAttribute('data-theme', t); } catch (e) {}
        })();
    </script>
</head>
<body class="admin-body">
<div class="admin-shell">
    <aside class="admin-sidebar" id="ad-sidebar" aria-label="Menu quản trị">
        <a class="ad-brand" href="/admin">
            <span class="ad-brand-mark"><?= Icon::svg('play', 16) ?></span>
            <span class="ad-brand-text"><strong><?= APP_NAME ?></strong><span>Cinema Manager</span></span>
        </a>
        <nav class="ad-nav">
            <?php foreach ($adminMenu as $group => $items):
                $visible = array_filter($items, fn($it) => !$it[3] || $isAdmin);
                if (!$visible) continue; ?>
                <div class="ad-nav-label"><?= e($group) ?></div>
                <?php foreach ($visible as $k => [$href, $label, $icon]): ?>
                    <a href="<?= $href ?>" class="<?= $nav === $k ? 'is-active' : '' ?>" <?= $nav === $k ? 'aria-current="page"' : '' ?>><?= Icon::svg($icon) ?><span><?= e($label) ?></span></a>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </nav>
        <div class="ad-side-foot">
            <a class="ad-side-link" href="/" target="_blank" rel="noopener"><?= Icon::svg('external') ?><span>Xem trang khách</span></a>
            <a class="ad-side-link" href="/logout"><?= Icon::svg('logout') ?><span>Đăng xuất</span></a>
            <div class="ad-user">
                <span class="ad-avatar"><?php if (!empty($me['avatar'])): ?><img src="<?= e($me['avatar']) ?>" alt=""><?php else: ?><?= e($initial) ?><?php endif; ?></span>
                <span class="ad-user-info"><strong><?= e($me['name'] ?? '') ?></strong><span><?= e(User::ROLE_LABEL[$me['role'] ?? 'user'] ?? '') ?></span></span>
            </div>
        </div>
    </aside>
    <div class="ad-backdrop" data-nav-close></div>

    <div class="ad-main">
        <header class="ad-topbar">
            <button class="icon-btn ad-menu-btn" type="button" data-nav-toggle aria-controls="ad-sidebar" aria-expanded="false" aria-label="Mở menu"><?= Icon::svg('menu') ?></button>
            <div class="ad-crumbs"><span class="ad-crumb-root">Quản trị</span><span class="ad-crumb-root"><?= Icon::svg('chevron-right', 14) ?></span><strong><?= e($current) ?></strong></div>
            <div class="ad-top-actions">
                <form class="ad-search" action="/admin/bookings" method="get" role="search">
                    <?= Icon::svg('search', 16) ?>
                    <input type="search" name="q" placeholder="Tìm đơn: mã, tên, SĐT, email…" aria-label="Tìm đơn đặt vé" value="<?= $nav === 'bookings' ? e(queryStr('q')) : '' ?>">
                </form>
                <button class="icon-btn" type="button" data-theme-toggle aria-label="Đổi giao diện sáng / tối" title="Giao diện sáng / tối"><?= Icon::svg('sun-moon') ?></button>
                <a class="icon-btn" href="/" target="_blank" rel="noopener" aria-label="Xem trang khách" title="Xem trang khách"><?= Icon::svg('external') ?></a>
            </div>
        </header>

        <?php if ($flashes = takeFlash()): ?>
            <div class="ad-toasts" aria-live="polite">
                <?php foreach ($flashes as $f): $ok = $f['type'] !== 'error'; ?>
                    <div class="ad-toast ad-toast-<?= $ok ? 'ok' : 'error' ?>" role="<?= $ok ? 'status' : 'alert' ?>">
                        <?= Icon::svg($ok ? 'check' : 'alert') ?><span><?= e($f['msg']) ?></span>
                        <button type="button" data-toast-close aria-label="Đóng"><?= Icon::svg('x', 16) ?></button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <main class="admin-main">
            <?= $content ?>
        </main>
    </div>
</div>
<script src="<?= e(asset('js/admin.js')) ?>" defer></script>
</body>
</html>
