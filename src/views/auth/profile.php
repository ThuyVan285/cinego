<?php
/** @var array $user @var array $tickets @var array $errors */
$err = fn(string $k) => isset($errors[$k]) ? '<span class="field-error" role="alert">' . e($errors[$k]) . '</span>' : '';
$isAdmin = $user['role'] === 'admin';
?>
<div class="container">
    <div class="page-head">
        <h1>Tài khoản của tôi</h1>
        <p class="muted">Quản lý thông tin cá nhân, vé đã đặt và bảo mật tài khoản.</p>
    </div>

    <div class="profile-layout">
        <!-- Cột trái: Thẻ tóm tắt thông tin người dùng -->
        <aside class="profile-sidebar">
            <div class="profile-user-card card">
                <div class="profile-avatar">
                    <span><?= mb_strtoupper(mb_substr($user['name'], 0, 1)) ?></span>
                </div>
                <h2 class="profile-name"><?= e($user['name']) ?></h2>
                <div class="profile-role">
                    <?php if ($isAdmin): ?>
                        <span class="role-badge admin">Quản trị viên CineGo</span>
                    <?php else: ?>
                        <span class="role-badge member">Thành viên CineGo</span>
                    <?php endif; ?>
                </div>
                <div class="profile-meta-list">
                    <div class="meta-item">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        <span><?= e($user['email']) ?></span>
                    </div>
                    <div class="meta-item">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        <span><?= e($user['phone']) ?></span>
                    </div>
                    <div class="meta-item">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <span>Gia nhập: <?= e(date('d/m/Y', strtotime($user['created_at'] ?? 'now'))) ?></span>
                    </div>
                </div>

                <div class="profile-stats">
                    <div class="stat-box">
                        <strong><?= count($tickets) ?></strong>
                        <span>Vé đã đặt</span>
                    </div>
                </div>

                <div class="profile-nav-actions">
                    <a class="btn btn-primary btn-sm" href="/tickets">Xem tất cả vé của tôi</a>
                    <a class="btn btn-ghost btn-sm" href="/logout">Đăng xuất</a>
                </div>
            </div>
        </aside>

        <!-- Cột phải: Cập nhật thông tin và đổi mật khẩu -->
        <main class="profile-main">
            <!-- Form thông tin cá nhân -->
            <section class="card pad profile-section">
                <h2>Cập nhật thông tin cá nhân</h2>
                <form method="post" action="/profile" class="profile-form">
                    <?= csrfField() ?>
                    <input type="hidden" name="action" value="update_info">

                    <label class="field <?= isset($errors['name']) ? 'has-error' : '' ?>">
                        <span>Họ và tên</span>
                        <input type="text" name="name" value="<?= e($user['name']) ?>" required>
                        <?= $err('name') ?>
                    </label>

                    <div class="field-row">
                        <label class="field">
                            <span>Email (không thể đổi)</span>
                            <input type="email" value="<?= e($user['email']) ?>" disabled style="opacity: 0.6; cursor: not-allowed;">
                        </label>

                        <label class="field <?= isset($errors['phone']) ? 'has-error' : '' ?>">
                            <span>Số điện thoại</span>
                            <input type="tel" name="phone" value="<?= e($user['phone']) ?>" required>
                            <?= $err('phone') ?>
                        </label>
                    </div>

                    <div class="form-btn-row">
                        <button class="btn btn-primary" type="submit">Lưu thay đổi</button>
                    </div>
                </form>
            </section>

            <!-- Form đổi mật khẩu -->
            <section class="card pad profile-section mt">
                <h2>Đổi mật khẩu</h2>
                <form method="post" action="/profile" class="profile-form">
                    <?= csrfField() ?>
                    <input type="hidden" name="action" value="change_password">

                    <label class="field <?= isset($errors['old_password']) ? 'has-error' : '' ?>">
                        <span>Mật khẩu hiện tại</span>
                        <input type="password" name="old_password" placeholder="Nhập mật khẩu đang dùng" required>
                        <?= $err('old_password') ?>
                    </label>

                    <div class="field-row">
                        <label class="field <?= isset($errors['new_password']) ? 'has-error' : '' ?>">
                            <span>Mật khẩu mới (tối thiểu 6 ký tự)</span>
                            <input type="password" name="new_password" placeholder="Mật khẩu mới" required>
                            <?= $err('new_password') ?>
                        </label>

                        <label class="field <?= isset($errors['confirm_password']) ? 'has-error' : '' ?>">
                            <span>Xác nhận mật khẩu mới</span>
                            <input type="password" name="confirm_password" placeholder="Nhập lại mật khẩu mới" required>
                            <?= $err('confirm_password') ?>
                        </label>
                    </div>

                    <div class="form-btn-row">
                        <button class="btn btn-ghost" type="submit">Cập nhật mật khẩu</button>
                    </div>
                </form>
            </section>
        </main>
    </div>
</div>

