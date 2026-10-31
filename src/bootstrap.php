<?php
declare(strict_types=1);

require __DIR__ . '/helpers.php';
require __DIR__ . '/data.php';

spl_autoload_register(function (string $class): void {
    foreach (['Core', 'Models', 'Controllers'] as $dir) {
        $f = __DIR__ . "/$dir/$class.php";
        if (is_file($f)) {
            require $f;
            return;
        }
    }
});

/** Mã nhận diện trình duyệt (cookie) – dùng để gắn vé với người dùng demo, không cần đăng nhập. */
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
