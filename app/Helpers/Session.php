<?php
declare(strict_types=1);

// ---------- Session, flash, CSRF, mã khách ----------
function startApp(): void
{
    date_default_timezone_set((string)config('app.timezone', 'Asia/Ho_Chi_Minh'));
    $dir = BASE_PATH . '/storage/sessions';
    if (is_dir($dir) && is_writable($dir)) {
        session_save_path($dir);
    }
    session_name('cinego_sess');
    session_start();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
}

function flash(string $type, string $msg): void
{
    $_SESSION['flash'][] = ['type' => $type, 'msg' => $msg];
}

function takeFlash(): array
{
    $f = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $f;
}

function csrfField(): string
{
    return '<input type="hidden" name="_csrf" value="' . e($_SESSION['csrf']) . '">';
}

function checkCsrf(): void
{
    $t = $_POST['_csrf'] ?? '';
    if (!is_string($t) || !hash_equals($_SESSION['csrf'], $t)) {
        http_response_code(419);
        render('errors/error', ['title' => 'Phiên làm việc hết hạn', 'message' => 'Vui lòng tải lại trang và thử lại.']);
        exit;
    }
}

/** Mã nhận diện trình duyệt (cookie) – gắn vé với khách chưa đăng nhập. */
function visitorId(): string
{
    $v = $_COOKIE['cg_vid'] ?? '';
    if (!is_string($v) || !preg_match('/^[a-f0-9]{32}$/', $v)) {
        $v = bin2hex(random_bytes(16));
        setcookie('cg_vid', $v, ['expires' => time() + 86400 * 365, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
        $_COOKIE['cg_vid'] = $v;
    }
    return $v;
}
