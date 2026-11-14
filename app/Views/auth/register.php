<?php
/** @var array $old @var array $errors */
$err = fn(string $k) => isset($errors[$k]) ? '<span class="field-error" role="alert">' . e($errors[$k]) . '</span>' : '';
?>
<div class="container narrow">
    <div class="auth-wrapper">
        <div class="auth-card card">
            <div class="auth-header">
                <span class="auth-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="8.5" cy="7" r="4"/>
                        <line x1="20" y1="8" x2="20" y2="14"/>
                        <line x1="23" y1="11" x2="17" y2="11"/>
                    </svg>
                </span>
                <h1>Đăng ký tài khoản</h1>
                <p class="muted">Tạo tài khoản CineGo miễn phí để lưu lịch sử đặt vé và nhận ưu đãi độc quyền.</p>
            </div>

            <?php if (!empty($errors['general'])): ?>
                <div class="auth-alert error" role="alert">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span><?= e($errors['general']) ?></span>
                </div>
            <?php endif; ?>

            <form method="post" action="/register" class="auth-form" novalidate>
                <?= csrfField() ?>

                <label class="field <?= isset($errors['name']) ? 'has-error' : '' ?>">
                    <span>Họ và tên</span>
                    <input type="text" name="name" value="<?= e($old['name'] ?? '') ?>" autocomplete="name" placeholder="Ví dụ: Nguyễn Văn A" required autofocus>
                    <?= $err('name') ?>
                </label>

                <div class="field-row">
                    <label class="field <?= isset($errors['email']) ? 'has-error' : '' ?>">
                        <span>Email</span>
                        <input type="email" name="email" value="<?= e($old['email'] ?? '') ?>" autocomplete="email" placeholder="ten@example.com" required>
                        <?= $err('email') ?>
                    </label>

                    <label class="field <?= isset($errors['phone']) ? 'has-error' : '' ?>">
                        <span>Số điện thoại</span>
                        <input type="tel" name="phone" value="<?= e($old['phone'] ?? '') ?>" autocomplete="tel" inputmode="tel" placeholder="0912345678" required>
                        <?= $err('phone') ?>
                    </label>
                </div>

                <div class="field-row">
                    <label class="field <?= isset($errors['password']) ? 'has-error' : '' ?>">
                        <span>Mật khẩu (tối thiểu 6 ký tự)</span>
                        <input type="password" name="password" autocomplete="new-password" placeholder="Tạo mật khẩu..." required>
                        <?= $err('password') ?>
                    </label>

                    <label class="field <?= isset($errors['password_confirm']) ? 'has-error' : '' ?>">
                        <span>Xác nhận mật khẩu</span>
                        <input type="password" name="password_confirm" autocomplete="new-password" placeholder="Nhập lại mật khẩu..." required>
                        <?= $err('password_confirm') ?>
                    </label>
                </div>

                <div class="auth-actions">
                    <button class="btn btn-primary btn-lg grow" type="submit">Đăng ký tài khoản</button>
                </div>
            </form>

            <div class="auth-footer">
                <p>Đã có tài khoản? <a href="/login">Đăng nhập tại đây</a></p>
            </div>
        </div>
    </div>
</div>

