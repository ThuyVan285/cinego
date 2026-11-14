<?php
declare(strict_types=1);

// ---------- Cấu hình / môi trường ----------
/** Nạp file .env (KEY=VALUE) vào biến môi trường, không ghi đè biến đã có. */
function loadEnv(string $file): void
{
    if (!is_file($file)) {
        return;
    }
    foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) {
            continue;
        }
        [$k, $v] = array_map('trim', explode('=', $line, 2));
        $v = trim($v, "\"'");
        if (getenv($k) === false) {
            putenv("$k=$v");
            $_ENV[$k] = $v;
        }
    }
}

function env(string $key, string $default = ''): string
{
    $v = getenv($key);
    return ($v === false || $v === '') ? $default : $v;
}

/** config('app.name') → giá trị 'name' trong app/Config/app.php */
function config(string $key, mixed $default = null): mixed
{
    static $cache = [];
    [$file, $item] = array_pad(explode('.', $key, 2), 2, null);
    $path = BASE_PATH . "/app/Config/$file.php";
    $cache[$file] ??= is_file($path) ? require $path : [];
    if ($item === null) {
        return $cache[$file];
    }
    return $cache[$file][$item] ?? $default;
}

// ---------- Chuỗi, tiền, URL ----------
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

/** Lấy tham số GET dạng chuỗi đã trim (bỏ qua mảng). */
function queryStr(string $k): string
{
    $v = $_GET[$k] ?? '';
    return is_string($v) ? trim($v) : '';
}

// ---------- Ngày giờ ----------
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

// ---------- Ảnh & tài nguyên tĩnh ----------
function asset(string $p): string
{
    $f = BASE_PATH . '/public/assets/' . $p;
    return '/assets/' . $p . (is_file($f) ? '?v=' . filemtime($f) : '');
}

function artUrl(string $kind, string $id): string
{
    if ($kind === 'poster' || $kind === 'backdrop') {
        $dirs = $kind === 'backdrop' ? ['banners', 'movies'] : ['movies'];
        foreach ($dirs as $dir) {
            foreach (['webp', 'jpg', 'png'] as $ext) {
                $rel = '/assets/images/' . $dir . '/' . $id . '.' . $ext;
                if (is_file(BASE_PATH . '/public' . $rel)) {
                    return $rel;
                }
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
            if (is_file(BASE_PATH . '/public' . $p)) {
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

/** Link YouTube (watch?v=, youtu.be/, /embed/, /shorts/) → URL nhúng youtube-nocookie; link khác → null. */
function youtubeEmbed(?string $url): ?string
{
    if (!$url || !preg_match('~(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/)|youtu\.be/)([\w-]{11})~', $url, $m)) {
        return null;
    }
    return 'https://www.youtube-nocookie.com/embed/' . $m[1] . '?autoplay=1&rel=0';
}

function ageClass(string $age): string
{
    return 'age age-' . strtolower($age);
}

/** Nhãn độ tuổi (P, K, T13, T16, T18); phim chưa được phân loại thì không hiện gì. */
function ageBadge(?string $age): string
{
    $age = (string)$age;
    return $age === '' ? '' : '<span class="' . e(ageClass($age)) . '">' . e($age) . '</span>';
}

/** Mô tả phân loại độ tuổi theo quy định phổ biến phim tại Việt Nam. */
function ageLabel(?string $age): string
{
    return [
        'P' => 'P – Mọi lứa tuổi',
        'K' => 'K – Dưới 13 tuổi cần xem cùng cha mẹ',
        'T13' => 'T13 – Từ 13 tuổi',
        'T16' => 'T16 – Từ 16 tuổi',
        'T18' => 'T18 – Từ 18 tuổi',
    ][(string)$age] ?? 'Chưa phân loại';
}

/** "120 phút", hoặc "Đang cập nhật" khi phim chưa công bố thời lượng. */
function durationText(int $minutes): string
{
    return $minutes > 0 ? $minutes . ' phút' : 'Đang cập nhật';
}
