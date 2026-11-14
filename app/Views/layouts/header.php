<?php
/** Thanh điều hướng trên cùng. @var string $nav */
$items = [
    'showtimes' => ['/showtimes', 'Lịch chiếu'],
    'cinemas' => ['/cinemas', 'Rạp chiếu'],
    'movies' => ['/movies', 'Phim chiếu'],
    'promos' => ['/promotions', 'Ưu đãi'],
];
?>
<header class="site-header">
    <div class="container header-row">
        <a class="logo" href="/" aria-label="<?= APP_NAME ?> – Trang chủ">
            <span class="logo-mark"><svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="M8 5l12 7-12 7z" fill="currentColor"/></svg></span><?= APP_NAME ?><span class="dot" style="color: var(--accent);">.</span>
        </a>
        <a class="brand-service" href="/booking">
            <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true"><path d="M4 5h16v14H4zM4 9h16M8 5v4M16 5v4" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>
            <span>Đặt vé<br>xem phim</span>
        </a>
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
            <button class="theme-toggle-btn" id="themeToggleBtn" type="button" aria-label="Đổi giao diện Sáng / Tối" title="Chuyển chế độ Sáng / Tối">🌓</button>
            <?php $currentUser = User::current(); ?>
            <?php if ($currentUser): ?>
                <details class="account user-logged-in">
                    <summary class="btn btn-ghost btn-sm account-btn">
                        <span class="user-avatar-dot"><?php if (!empty($currentUser['avatar'])): ?><img src="<?= e($currentUser['avatar']) ?>" alt=""><?php else: ?><?= mb_strtoupper(mb_substr($currentUser['name'], 0, 1)) ?><?php endif; ?></span>
                        <span class="user-name-text"><?= e($currentUser['name']) ?></span>
                    </summary>
                    <div class="account-menu">
                        <div class="account-menu-user">
                            <strong><?= e($currentUser['name']) ?></strong>
                            <p class="muted small"><?= e($currentUser['email']) ?></p>
                            <span class="role-chip <?= in_array($currentUser['role'], ['admin', 'staff'], true) ? 'admin' : 'member' ?>">
                                <?= ['admin' => '🛡️ Quản trị viên', 'staff' => '🧑‍💼 Nhân viên rạp'][$currentUser['role']] ?? '🎟️ Thành viên CineGo' ?>
                            </span>
                        </div>
                        <div class="menu-divider"></div>
                        <?php if (in_array($currentUser['role'], ['admin', 'staff'], true)): ?><a href="/admin">🛠️ Trang quản trị</a><?php endif; ?>
                        <a href="/profile">👤 Thông tin tài khoản</a>
                        <a href="/tickets">🎟️ Vé của tôi</a>
                        <a href="/booking">🎬 Đặt vé mới</a>
                        <div class="menu-divider"></div>
                        <a href="/logout" class="menu-logout-link">🚪 Đăng xuất</a>
                    </div>
                </details>
            <?php else: ?>
                <details class="account">
                    <summary class="btn btn-ghost btn-sm">Tài khoản</summary>
                    <div class="account-menu">
                        <div class="account-guest-actions">
                            <a class="btn btn-primary btn-sm" href="/login">Đăng nhập</a>
                            <a class="btn btn-ghost btn-sm" href="/register">Đăng ký</a>
                        </div>
                        <div class="menu-divider"></div>
                        <p class="muted small">Chế độ khách: Vé được lưu tạm trên trình duyệt này.</p>
                        <a href="/tickets">🎟️ Vé của tôi</a>
                        <a href="/booking">🎬 Đặt vé mới</a>
                    </div>
                </details>
            <?php endif; ?>
        </div>
    </div>
</header>
