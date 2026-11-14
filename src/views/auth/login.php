<?php
/** @var string $email @var array $errors @var string $redirect */
$err = fn(string $k) => isset($errors[$k]) ? '<span class="field-error" role="alert">' . e($errors[$k]) . '</span>' : '';
?>
<div class="container narrow">
    <div class="auth-wrapper">
        <div class="auth-card card">
            <div class="auth-header">
                <span class="auth-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                        <polyline points="10 17 15 12 10 7"/>
                        <line x1="15" y1="12" x2="3" y2="12"/>
                    </svg>
                </span>
                <h1>Đăng nhập CineGo</h1>
                <p class="muted">Chào mừng bạn quay lại! Đăng nhập để quản lý vé và nhận ưu đãi riêng.</p>
            </div>

            <?php if (!empty($errors['general'])): ?>
                <div class="auth-alert error" role="alert">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span><?= e($errors['general']) ?></span>
                </div>
            <?php endif; ?>

            <form method="post" action="/login" class="auth-form" novalidate>
                <?= csrfField() ?>
                <input type="hidden" name="redirect" value="<?= e($redirect) ?>">

                <label class="field <?= isset($errors['email']) ? 'has-error' : '' ?>">
                    <span>Email đăng nhập</span>
                    <input type="email" name="email" value="<?= e($email) ?>" autocomplete="email" placeholder="ví dụ: khachhang@gmail.com" required autofocus>
                    <?= $err('email') ?>
                </label>

                <label class="field <?= isset($errors['password']) ? 'has-error' : '' ?>">
                    <span>Mật khẩu</span>
                    <input type="password" name="password" autocomplete="current-password" placeholder="Nhập mật khẩu..." required>
                    <?= $err('password') ?>
                </label>

                <div class="auth-actions">
                    <button class="btn btn-primary btn-lg grow" type="submit">Đăng nhập</button>
                </div>
            </form>

            <div class="auth-demo-box">
                <p class="small muted"><strong>Tài khoản demo sẵn có:</strong></p>
                <div class="demo-account-list">
                    <button type="button" class="demo-fill-btn" onclick="document.querySelector('input[name=email]').value='khachhang@gmail.com'; document.querySelector('input[name=password]').value='user123';">
                        <span>👤 Khách hàng</span><code>khachhang@gmail.com / user123</code>
                    </button>
                    <button type="button" class="demo-fill-btn" onclick="document.querySelector('input[name=email]').value='admin@cinego.vn'; document.querySelector('input[name=password]').value='admin123';">
                        <span>🛡️ Quản trị viên</span><code>admin@cinego.vn / admin123</code>
                    </button>
                </div>
            </div>

            <div class="auth-footer">
                <p>Chưa có tài khoản? <a href="/register">Đăng ký ngay</a></p>
            </div>
        </div>
    </div>
</div>

