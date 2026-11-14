<?php
declare(strict_types=1);

/**
 * Cài đặt CSDL demo: tạo database, bảng và nạp dữ liệu mẫu.
 * Chạy:  php database/seeders/DatabaseSeeder.php
 */
require __DIR__ . '/../../app/bootstrap.php';
require __DIR__ . '/data.php';
date_default_timezone_set('Asia/Ho_Chi_Minh');
$cli = PHP_SAPI === 'cli';
$out = [];

try {
    $cfg = config('database');
    $root = Database::connect(false);
    $root->exec('CREATE DATABASE IF NOT EXISTS `' . str_replace('`', '', $cfg['db_name']) . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $root->exec('USE `' . str_replace('`', '', $cfg['db_name']) . '`');
    $root->exec(file_get_contents(__DIR__ . '/../movie_booking.sql'));
    $out[] = 'Đã tạo bảng.';

    $pdo = Database::pdo();
    $pdo->beginTransaction();
    $st = $pdo->prepare('INSERT INTO brands (id, name, color) VALUES (?,?,?)');
    foreach (brands() as $id => $b) {
        $st->execute([$id, $b['name'], $b['color']]);
    }
    $st = $pdo->prepare('INSERT INTO cinemas (id, brand, name, city, area, address, rooms) VALUES (?,?,?,?,?,?,?)');
    foreach (cinemas() as $c) {
        $st->execute([$c['id'], $c['brand'], $c['name'], $c['city'], $c['area'], $c['addr'], $c['rooms']]);
        Room::createForCinema($c['id'], (int)$c['rooms']);
    }
    $st = $pdo->prepare('INSERT INTO movies (id, title, genres, duration, age, rating, status, release_date, director, cast_list, synopsis, palette, poster_url, trailer_url, featured) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    foreach (movies() as $m) {
        $st->execute([$m['id'], $m['title'], implode(', ', $m['genres']), $m['duration'], $m['age'], $m['rating'], $m['status'], $m['release'],
            $m['director'], implode(', ', $m['cast']), $m['synopsis'], implode(',', $m['palette']), $m['poster'] ?? ('/assets/images/movies/' . $m['id'] . '.webp'), $m['trailer'], (int)$m['featured']]);
    }
    $st = $pdo->prepare('INSERT INTO combos (id, name, description, price, sort_order) VALUES (?,?,?,?,?)');
    foreach (combos() as $i => $c) {
        $st->execute([$c['id'], $c['name'], $c['desc'], $c['price'], $i]);
    }
    $st = $pdo->prepare('INSERT INTO promotions (code, title, description, conditions, expires_at, type, value, max_discount, min_spend, need_combo, tag, color1, color2) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)');
    foreach (promos() as $p) {
        $st->execute([$p['code'], $p['title'], $p['desc'], $p['cond'], $p['expires'], $p['type'], $p['value'], $p['max'], $p['min'], (int)($p['needCombo'] ?? false), $p['tag'], $p['colors'][0], $p['colors'][1]]);
    }
    $pdo->commit();
    $rooms = Database::one('SELECT COUNT(*) n FROM rooms')['n'];
    $seats = Database::one('SELECT COUNT(*) n FROM seats')['n'];
    $out[] = "Đã nạp phim, rạp, combo, ưu đãi; $rooms phòng chiếu với $seats ghế.";

    // Bảng users được giữ lại khi cài đặt lại → bổ sung cột mới nếu CSDL cũ chưa có.
    if (!Database::all("SHOW COLUMNS FROM users LIKE 'avatar'")) {
        $pdo->exec('ALTER TABLE users ADD COLUMN avatar VARCHAR(255) NULL AFTER role');
        $out[] = 'Đã thêm cột avatar vào bảng users.';
    }

    $check = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $st = $pdo->prepare('INSERT INTO users (name, email, phone, password, role) VALUES (?,?,?,?,?)');
    foreach (seedUsers() as $u) {
        $check->execute([$u['email']]);
        if ($check->fetch()) {
            $out[] = "Tài khoản {$u['email']} đã tồn tại, giữ nguyên.";
            continue;
        }
        $st->execute([$u['name'], $u['email'], $u['phone'], password_hash($u['password'], PASSWORD_DEFAULT), $u['role']]);
        $out[] = "Đã tạo tài khoản demo {$u['email']} ({$u['role']}) / mật khẩu: {$u['password']}";
    }

    // Cột/bảng của trang quản trị (staff, thể loại, giá vé mặc định...) – idempotent, áp cả cho bảng users được giữ lại.
    require __DIR__ . '/../migrations/2026_11_15_admin_features.php';

    Showtime::ensureRange();
    $n = Database::one('SELECT COUNT(*) n FROM showtimes')['n'];
    $out[] = "Đã sinh $n suất chiếu demo cho 7 ngày tới.";
    $ok = true;
} catch (Throwable $e) {
    $ok = false;
    $out[] = 'LỖI: ' . $e->getMessage();
    $out[] = 'Hãy bật MySQL (XAMPP Control Panel → Start) hoặc chỉnh file .env (DB_HOST, DB_USER, DB_PASS…).';
}

if ($cli) {
    echo implode(PHP_EOL, $out), PHP_EOL;
    exit($ok ? 0 : 1);
}
header('Content-Type: text/html; charset=utf-8');
echo '<!doctype html><meta charset="utf-8"><title>Cài đặt CineGo</title><body style="font-family:sans-serif;max-width:560px;margin:40px auto"><h1>Cài đặt CineGo</h1><ul>';
foreach ($out as $l) {
    echo '<li>' . htmlspecialchars($l) . '</li>';
}
echo '</ul>' . ($ok ? '<p><a href="/">Vào trang chủ →</a></p>' : '') . '</body>';
