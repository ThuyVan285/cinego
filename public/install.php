<?php
declare(strict_types=1);

/**
 * Cài đặt CSDL demo: tạo database, bảng và nạp dữ liệu mẫu.
 * Chạy:  php public/install.php   hoặc mở http://localhost:8000/install.php
 */
require __DIR__ . '/../src/bootstrap.php';
date_default_timezone_set('Asia/Ho_Chi_Minh');
$cli = PHP_SAPI === 'cli';
$out = [];

try {
    $cfg = require __DIR__ . '/../src/config.php';
    $root = Database::connect(false);
    $root->exec('CREATE DATABASE IF NOT EXISTS `' . str_replace('`', '', $cfg['db_name']) . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $root->exec('USE `' . str_replace('`', '', $cfg['db_name']) . '`');
    $root->exec(file_get_contents(__DIR__ . '/../database/schema.sql'));
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
    }
    $st = $pdo->prepare('INSERT INTO movies (id, title, genres, duration, age, rating, status, release_date, director, cast_list, synopsis, palette, poster_url, trailer_url, featured) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    foreach (movies() as $m) {
        $st->execute([$m['id'], $m['title'], implode(', ', $m['genres']), $m['duration'], $m['age'], $m['rating'], $m['status'], $m['release'],
            $m['director'], implode(', ', $m['cast']), $m['synopsis'], implode(',', $m['palette']), $m['poster'] ?? ('/assets/images/posters/' . $m['id'] . '.webp'), $m['trailer'], (int)$m['featured']]);
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
    $out[] = 'Đã nạp phim, rạp, combo, ưu đãi.';

    Showtime::ensureRange();
    $n = Database::one('SELECT COUNT(*) n FROM showtimes')['n'];
    $out[] = "Đã sinh $n suất chiếu demo cho 7 ngày tới.";
    $ok = true;
} catch (Throwable $e) {
    $ok = false;
    $out[] = 'LỖI: ' . $e->getMessage();
    $out[] = 'Hãy bật MySQL (XAMPP Control Panel → Start) hoặc chỉnh src/config.php.';
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
