<?php
declare(strict_types=1);

const APP_NAME = 'CineGo';
const MAX_SEATS = 8;
const PER_PAGE = 8;

function e(?string $s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function money(int $v): string
{
    return number_format($v, 0, ',', '.') . 'đ';
}

function url(string $path = '/', array $q = []): string
{
    $q = array_filter($q, fn($v) => $v !== null && $v !== '' && $v !== false);
    return $path . ($q ? '?' . http_build_query($q) : '');
}

function redirect(string $to): never
{
    header('Location: ' . $to);
    exit;
}

function today(): string
{
    return date('Y-m-d');
}

function addDays(string $ymd, int $n): string
{
    return date('Y-m-d', strtotime($ymd . ' +' . $n . ' day'));
}

function weekdayVi(string $ymd, bool $short = false): string
{
    $w = (int)date('w', strtotime($ymd));
    $long = ['Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy'];
    $sh = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];
    return $short ? $sh[$w] : $long[$w];
}

function dateVi(string $ymd): string
{
    return weekdayVi($ymd) . ', ' . date('d/m/Y', strtotime($ymd));
}

function validDate(?string $d): bool
{
    return is_string($d) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d) === 1 && strtotime($d) !== false;
}

/** Ngày hợp lệ để xem lịch (hôm nay → +6), mặc định hôm nay. */
function pickDate(?string $d): string
{
    if (validDate($d) && $d >= today() && $d <= addDays(today(), 6)) {
        return $d;
    }
    return today();
}

function asset(string $p): string
{
    $f = __DIR__ . '/../public/assets/' . $p;
    return '/assets/' . $p . (is_file($f) ? '?v=' . filemtime($f) : '');
}

// ---------- Session, flash, CSRF ----------
function startApp(): void
{
    date_default_timezone_set('Asia/Ho_Chi_Minh');
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
        render('error', ['title' => 'Phiên làm việc hết hạn', 'message' => 'Vui lòng tải lại trang và thử lại.']);
        exit;
    }
}

// ---------- Rendering ----------
function render(string $view, array $vars = []): void
{
    $vars += ['title' => APP_NAME, 'nav' => '', 'bare' => false];
    extract($vars, EXTR_SKIP);
    ob_start();
    require __DIR__ . '/views/' . $view . '.php';
    $content = ob_get_clean();
    require __DIR__ . '/views/layout.php';
}

function partial(string $name, array $vars = []): void
{
    extract($vars, EXTR_SKIP);
    require __DIR__ . '/views/partials/' . $name . '.php';
}

function notFound(): never
{
    http_response_code(404);
    render('error', ['title' => 'Không tìm thấy trang', 'message' => 'Trang bạn tìm không tồn tại hoặc đã được di chuyển.']);
    exit;
}

function artUrl(string $kind, string $id): string
{
    if ($kind === 'poster' || $kind === 'backdrop') {
        if ($kind === 'backdrop') {
            foreach (['webp', 'jpg', 'png'] as $ext) {
                $rel = '/assets/images/backdrops/' . $id . '.' . $ext;
                if (is_file(__DIR__ . '/../public' . $rel)) {
                    return $rel;
                }
            }
        }
        foreach (['webp', 'jpg', 'png'] as $ext) {
            $rel = '/assets/images/posters/' . $id . '.' . $ext;
            if (is_file(__DIR__ . '/../public' . $rel)) {
                return $rel;
            }
        }
    }
    return '/art/' . $kind . '/' . rawurlencode($id) . '.svg';
}

function posterUrl(string|array $movieOrId): string
{
    if (is_array($movieOrId)) {
        if (!empty($movieOrId['poster'])) {
            $p = (string)$movieOrId['poster'];
            if (is_file(__DIR__ . '/../public' . $p)) {
                return $p;
            }
        }
        $id = (string)($movieOrId['id'] ?? '');
    } else {
        $id = $movieOrId;
    }
    return artUrl('poster', $id);
}

function img(string $src, string $alt, string $class = '', string $fallbackKind = 'poster'): string
{
    return '<img src="' . e($src) . '" alt="' . e($alt) . '" class="' . e($class) . '" loading="lazy" data-fallback="' . e(artUrl($fallbackKind, 'fallback')) . '">';
}

function ageClass(string $age): string
{
    return 'age age-' . strtolower($age);
}
