<?php
declare(strict_types=1);

/**
 * Migration script: Tạo bảng users và cập nhật bảng bookings liên kết tài khoản.
 */

require_once __DIR__ . '/../../app/bootstrap.php';
require_once __DIR__ . '/../seeders/data.php';
date_default_timezone_set('Asia/Ho_Chi_Minh');

$cli = PHP_SAPI === 'cli';
$logs = [];

function logMsg(string $msg): void {
    global $logs;
    $logs[] = $msg;
}

try {
    $pdo = Database::pdo();
    logMsg("=== BẮT ĐẦU MIGRATION TÀI KHOẢN NGƯỜI DÙNG ===");

    // 1. Tạo bảng users nếu chưa có
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
          id INT AUTO_INCREMENT PRIMARY KEY,
          name VARCHAR(80) NOT NULL,
          email VARCHAR(120) NOT NULL UNIQUE,
          phone VARCHAR(15) NOT NULL,
          password VARCHAR(255) NOT NULL,
          role ENUM('user','admin') NOT NULL DEFAULT 'user',
          created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
          INDEX idx_email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    logMsg("Đã kiểm tra / tạo bảng users thành công.");

    // 2. Thêm cột user_id vào bảng bookings nếu chưa có
    $colCheck = Database::all("SHOW COLUMNS FROM bookings LIKE 'user_id'");
    if (empty($colCheck)) {
        $pdo->exec("ALTER TABLE bookings ADD COLUMN user_id INT NULL AFTER visitor_id, ADD INDEX idx_user (user_id)");
        logMsg("Đã bổ sung cột user_id vào bảng bookings.");
    } else {
        logMsg("Cột user_id trong bảng bookings đã tồn tại.");
    }

    // 3. Seed tài khoản mẫu nếu chưa có
    $seedUsers = seedUsers();

    $stCheck = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stInsert = $pdo->prepare('INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)');

    foreach ($seedUsers as $u) {
        $stCheck->execute([$u['email']]);
        if (!$stCheck->fetch()) {
            $hash = password_hash($u['password'], PASSWORD_DEFAULT);
            $stInsert->execute([$u['name'], $u['email'], $u['phone'], $hash, $u['role']]);
            logMsg("  -> Đã tạo tài khoản mẫu: {$u['email']} ({$u['role']}) / pass: {$u['password']}");
        } else {
            logMsg("  -> Tài khoản {$u['email']} đã tồn tại.");
        }
    }

    logMsg("=== HOÀN TẤT MIGRATION TÀI KHOẢN NGƯỜI DÙNG ===");
    $ok = true;
} catch (Throwable $e) {
    $ok = false;
    logMsg("LỖI MIGRATION: " . $e->getMessage());
}

if ($cli) {
    echo implode(PHP_EOL, $logs), PHP_EOL;
    exit($ok ? 0 : 1);
}

header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>CineGo - Migration Users</title>
</head>
<body style="font-family:sans-serif;background:#12142a;color:#f5eddc;padding:24px;">
    <h2>Kết quả Migration Users</h2>
    <pre><?= htmlspecialchars(implode("\n", $logs)) ?></pre>
</body>
</html>

