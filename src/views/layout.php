<?php
/** @var string $content */
/** @var string $title */
/** @var string $nav */
/** @var bool $bare */
$items = ['showtimes' => ['/showtimes', 'Lịch chiếu'], 'cinemas' => ['/cinemas', 'Rạp chiếu'], 'movies' => ['/movies', 'Phim chiếu'], 'promos' => ['/promotions', 'Ưu đãi']];
?><!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?></title>
    <meta name="description" content="<?= APP_NAME ?> – website đặt vé xem phim trực tuyến.">
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Cdefs%3E%3ClinearGradient id='g'%3E%3Cstop stop-color='%23ff7b54'/%3E%3Cstop offset='1' stop-color='%23e83f6f'/%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width='32' height='32' rx='9' fill='url(%23g)'/%3E%3Cpath d='M12 9l11 7-11 7z' fill='%23fff'/%3E%3C/svg%3E">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body>
<?php if (!$bare): ?>
<header class="site-header">
    <div class="container header-row">
        <a class="logo" href="/" aria-label="<?= APP_NAME ?> – Trang chủ">
            <span class="logo-mark"><svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="M8 5l12 7-12 7z" fill="currentColor"/></svg></span><?= APP_NAME ?>
        </a>
        <span class="brand-service"><svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true"><path d="M4 5h16v14H4zM4 9h16M8 5v4M16 5v4" fill="none" stroke="currentColor" stroke-width="1.8"/></svg><span>Đặt vé<br>xem phim</span></span>
        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="main-nav" aria-label="Mở menu">
            <span></span><span></span><span></span>
        </button>
        <div class="nav-wrap" id="main-nav">
            <nav class="main-nav" aria-label="Điều hướng chính">
                <?php foreach ($items as $k => [$href, $label]): ?>
                    <a href="<?= $href ?>" class="<?= $nav === $k ? 'is-active' : '' ?>"><?= $label ?></a>
                <?php endforeach; ?>
            </nav>
            <form class="header-search" action="/movies" method="get" role="search">
                <input type="search" name="q" placeholder="Tìm phim..." aria-label="Tìm phim" value="<?= e($_GET['q'] ?? '') ?>">
                <button type="submit" aria-label="Tìm kiếm"><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg></button>
            </form>
            <details class="account">
                <summary class="btn btn-ghost btn-sm">Tài khoản</summary>
                <div class="account-menu">
                    <p>Bạn đang dùng chế độ khách – không cần đăng nhập. Vé được gắn với trình duyệt này.</p>
                    <a href="/tickets">Vé của tôi</a>
                    <a href="/booking">Đặt vé mới</a>
                </div>
            </details>
        </div>
    </div>
</header>
<?php endif; ?>

<main id="main">
    <?php partial('flash'); ?>
    <?= $content ?>
</main>

<?php if (!$bare): ?>
<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <a class="logo logo-light" href="/"><span class="logo-mark"><svg viewBox="0 0 24 24" width="18" height="18"><path d="M8 5l12 7-12 7z" fill="currentColor"/></svg></span><?= APP_NAME ?></a>
            <p>Đặt vé xem phim trực tuyến: chọn phim, rạp, suất chiếu, ghế, bắp nước và hoàn tất đặt vé nhanh chóng.</p>
        </div>
        <div>
            <h4>Khám phá</h4>
            <a href="/movies">Phim</a><a href="/showtimes">Lịch chiếu</a><a href="/cinemas">Rạp chiếu</a><a href="/promotions">Ưu đãi</a>
        </div>
        <div>
            <h4>Hỗ trợ khách hàng</h4>
            <a href="/tickets">Vé của tôi</a>
            <a href="#terms">Điều khoản sử dụng</a>
            <span>Hotline: 1900 0000</span>
        </div>
        <div>
            <h4>Liên hệ</h4>
            <span>hello@cinego.example</span>
            <span>123 Bạch Đằng, Hải Châu, Đà Nẵng</span>
        </div>
    </div>
    <div class="container footer-note" id="terms">
        <strong>Điều khoản sử dụng:</strong> Đây là sản phẩm minh hoạ. Không thu tiền thật, không tích hợp cổng thanh toán, mã QR và mã đặt vé không có giá trị tại rạp. Các thương hiệu rạp xuất hiện trong dữ liệu chỉ nhằm mô phỏng và không liên quan đến đơn vị thật.
        <div class="copy">© <?= date('Y') ?> <?= APP_NAME ?></div>
    </div>
</footer>
<?php endif; ?>
<script src="<?= e(asset('js/app.js')) ?>" defer></script>
</body>
</html>
