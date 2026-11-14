<?php
declare(strict_types=1);

/**
 * Migration: các thay đổi CSDL cho trang quản trị. Chạy lại nhiều lần vẫn an toàn, không mất dữ liệu.
 *  - users.role thêm 'staff'; users.locked (khoá tài khoản)
 *  - movies.status thêm 'ended' (ngừng chiếu)
 *  - bảng genres (thể loại) – nạp từ danh sách cũ + thể loại đang dùng trong phim
 *  - seats.active (ghế hỏng/tạm ngưng), promotions.active (bật/tắt mã)
 *  - bảng settings (giá vé mặc định cho suất chiếu mới)
 * Chạy:  php database/migrations/2026_11_15_admin_features.php
 */
require_once __DIR__ . '/../../app/bootstrap.php';

$log = fn(string $m) => print($m . PHP_EOL);
$hasCol = fn(string $t, string $c) => (bool)Database::all("SHOW COLUMNS FROM `$t` LIKE '$c'");
$pdo = Database::pdo();

try {
    $pdo->exec("ALTER TABLE users MODIFY role ENUM('user','staff','admin') NOT NULL DEFAULT 'user'");
    if (!$hasCol('users', 'locked')) {
        $pdo->exec('ALTER TABLE users ADD COLUMN locked TINYINT(1) NOT NULL DEFAULT 0 AFTER role');
    }
    $log('users: role có staff, cột locked.');

    $pdo->exec("ALTER TABLE movies MODIFY status ENUM('now','soon','ended') NOT NULL");
    $log("movies: status có 'ended'.");

    $pdo->exec('CREATE TABLE IF NOT EXISTS genres (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(40) NOT NULL UNIQUE,
        sort_order SMALLINT NOT NULL DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
    $names = Genre::DEFAULTS;
    foreach (Database::all('SELECT genres FROM movies') as $r) {
        foreach (explode(',', $r['genres']) as $g) {
            $names[] = trim($g);
        }
    }
    $ins = $pdo->prepare('INSERT IGNORE INTO genres (name, sort_order) VALUES (?, ?)');
    foreach (array_values(array_unique(array_filter($names))) as $i => $g) {
        $ins->execute([$g, $i]);
    }
    $log('genres: ' . Database::one('SELECT COUNT(*) n FROM genres')['n'] . ' thể loại.');

    if (!$hasCol('seats', 'active')) {
        $pdo->exec('ALTER TABLE seats ADD COLUMN active TINYINT(1) NOT NULL DEFAULT 1');
    }
    if (!$hasCol('promotions', 'active')) {
        $pdo->exec('ALTER TABLE promotions ADD COLUMN active TINYINT(1) NOT NULL DEFAULT 1');
    }
    $log('seats.active, promotions.active.');

    $pdo->exec('CREATE TABLE IF NOT EXISTS settings (
        k VARCHAR(40) NOT NULL PRIMARY KEY,
        v VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
    $ins = $pdo->prepare('INSERT IGNORE INTO settings (k, v) VALUES (?, ?)');
    foreach (Setting::DEFAULTS as $k => $v) {
        $ins->execute([$k, (string)$v]);
    }
    $log('settings: giá vé mặc định.');
    $log('=== XONG ===');
} catch (Throwable $e) {
    $log('LỖI: ' . $e->getMessage());
    exit(1);
}
