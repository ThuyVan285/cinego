<?php
declare(strict_types=1);

/**
 * Migration script an toàn: Cập nhật dữ liệu phim vào MySQL hiện tại.
 * 
 * Đặc điểm an toàn:
 * 1. KHÔNG DROP TABLE, KHÔNG làm mất cấu trúc database.
 * 2. Bảo vệ toàn vẹn lịch sử đặt vé (bookings, booking_seats, booking_combos).
 * 3. Bảo toàn dữ liệu rạp (cinemas), combo, ưu đãi (promotions).
 * 4. Sử dụng Database Transaction (ACID) - rollback nếu có lỗi.
 * 5. Idempotent: Có thể chạy lại nhiều lần an toàn mà không sinh trùng dữ liệu.
 * 6. Kiểm tra khóa ngoại trước khi xử lý phim cũ.
 * 
 * Cách chạy:
 *   CLI:     php database/migrate_movies.php
 *   Browser: http://localhost:8000/database/migrate_movies.php (hoặc qua XAMPP)
 */

require_once __DIR__ . '/../src/bootstrap.php';
date_default_timezone_set('Asia/Ho_Chi_Minh');

$cli = PHP_SAPI === 'cli';
$logs = [];

function logMsg(string $msg): void {
    global $logs;
    $logs[] = $msg;
}

try {
    $pdo = Database::pdo();

    logMsg("=== BẮT ĐẦU CẬP NHẬT DỮ LIỆU PHIM (CINEGO MIGRATION) ===");

    // Đảm bảo cột poster_url tồn tại trong bảng movies (DDL chạy trước transaction)
    $colCheck = Database::all("SHOW COLUMNS FROM movies LIKE 'poster_url'");
    if (empty($colCheck)) {
        $pdo->exec("ALTER TABLE movies ADD COLUMN poster_url VARCHAR(255) NULL AFTER palette");
        logMsg("Đã bổ sung cột poster_url vào bảng movies.");
    }

    $pdo->beginTransaction();

    // 1. Kiểm tra trạng thái hiện tại của database
    $curMovies = Database::all('SELECT id, title, status FROM movies');
    $curMovieIds = array_column($curMovies, 'id');
    $curMovieMap = [];
    foreach ($curMovies as $cm) {
        $curMovieMap[$cm['id']] = $cm;
    }

    $curBookingsCount = (int)(Database::one('SELECT COUNT(*) n FROM bookings')['n'] ?? 0);
    logMsg("Hiện có: " . count($curMovies) . " phim trong database, $curBookingsCount đơn đặt vé.");

    // 2. Lấy danh sách phim mới từ src/data.php
    $newMovies = movies();
    $newMovieIds = array_column($newMovies, 'id');
    logMsg("Số lượng phim thực tế trong file data.php: " . count($newMovies));

    // 3. Upsert phim mới vào bảng movies
    $sqlUpsert = 'INSERT INTO movies (id, title, genres, duration, age, rating, status, release_date, director, cast_list, synopsis, palette, poster_url, trailer_url, featured)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
        ON DUPLICATE KEY UPDATE
            title = VALUES(title),
            genres = VALUES(genres),
            duration = VALUES(duration),
            age = VALUES(age),
            rating = VALUES(rating),
            status = VALUES(status),
            release_date = VALUES(release_date),
            director = VALUES(director),
            cast_list = VALUES(cast_list),
            synopsis = VALUES(synopsis),
            palette = VALUES(palette),
            poster_url = VALUES(poster_url),
            trailer_url = VALUES(trailer_url),
            featured = VALUES(featured)';
    
    $stUpsert = $pdo->prepare($sqlUpsert);
    $insertedCount = 0;
    $updatedCount = 0;

    foreach ($newMovies as $m) {
        $isExisting = isset($curMovieMap[$m['id']]);
        $posterPath = $m['poster'] ?? ('/assets/images/posters/' . $m['id'] . '.webp');
        $stUpsert->execute([
            $m['id'],
            $m['title'],
            implode(', ', $m['genres']),
            $m['duration'],
            $m['age'],
            $m['rating'],
            $m['status'],
            $m['release'],
            $m['director'],
            implode(', ', $m['cast']),
            $m['synopsis'],
            implode(',', $m['palette']),
            $posterPath,
            $m['trailer'] ?? null,
            (int)($m['featured'] ?? false)
        ]);

        if ($isExisting) {
            $updatedCount++;
            logMsg("  [Cập nhật] [{$m['id']}] {$m['title']} ({$m['status']}, {$m['release']})");
        } else {
            $insertedCount++;
            logMsg("  [Thêm mới] [{$m['id']}] {$m['title']} ({$m['status']}, {$m['release']})");
        }
    }

    // 4. Kiểm tra các phim cũ không còn trong danh sách phim mới
    $obsoleteIds = array_diff($curMovieIds, $newMovieIds);
    if (!empty($obsoleteIds)) {
        logMsg("Phát hiện " . count($obsoleteIds) . " phim cũ trong database không thuộc danh sách mới:");
        foreach ($obsoleteIds as $oldId) {
            $oldTitle = $curMovieMap[$oldId]['title'] ?? $oldId;
            // Kiểm tra xem phim cũ này có đơn đặt vé nào không (liên kết qua showtimes -> bookings)
            $bookingCheck = Database::one(
                'SELECT COUNT(b.code) n 
                 FROM bookings b 
                 JOIN showtimes s ON s.id = b.showtime_id 
                 WHERE s.movie_id = ?',
                [$oldId]
            );
            $hasBookings = (int)($bookingCheck['n'] ?? 0) > 0;

            if ($hasBookings) {
                // Có đơn đặt vé: BẢO TỒN bản ghi để không mất lịch sử đặt vé của khách
                logMsg("  -> Phim [{$oldId}] '$oldTitle' CÓ {$bookingCheck['n']} đơn đặt vé: GIỮ NGUYÊN để bảo toàn lịch sử.");
            } else {
                // Không có đơn đặt vé: Xóa các suất chiếu trống chưa có vé và xóa phim cũ
                $pdo->prepare('DELETE FROM showtimes WHERE movie_id = ?')->execute([$oldId]);
                $pdo->prepare('DELETE FROM movies WHERE id = ?')->execute([$oldId]);
                logMsg("  -> Phim cũ [{$oldId}] '$oldTitle' không có đơn đặt vé: Đã gỡ bỏ an toàn.");
            }
        }
    }

    // 5. Kiểm tra và đồng bộ suất chiếu:
    // Chỉ phim 'now' và đã đến ngày phát hành mới có suất chiếu
    // Với phim 'soon', kiểm tra xem có suất chiếu nào tạo nhầm trước ngày chiếu mà chưa có vé không
    $soonMovies = array_filter($newMovies, fn($m) => $m['status'] === 'soon');
    foreach ($soonMovies as $sm) {
        $soonId = $sm['id'];
        // Xóa các suất chiếu của phim soon mà chưa có khách đặt vé
        $delShowtimes = $pdo->prepare(
            'DELETE s FROM showtimes s
             LEFT JOIN bookings b ON b.showtime_id = s.id
             WHERE s.movie_id = ? AND b.code IS NULL'
        );
        $delShowtimes->execute([$soonId]);
        $cntDeleted = $delShowtimes->rowCount();
        if ($cntDeleted > 0) {
            logMsg("  -> Đã gỡ $cntDeleted suất chiếu trống của phim sắp chiếu [{$soonId}] {$sm['title']}");
        }
    }

    if ($pdo->inTransaction()) {
        $pdo->commit();
    }
    logMsg("=== CẬP NHẬT DỮ LIỆU PHIM THÀNH CÔNG (TRANSACTION COMMITTED) ===");

    // 6. Đảm bảo lịch chiếu cho 7 ngày tới cho các phim 'now'
    Showtime::ensureRange();
    $totalShowtimes = (int)(Database::one('SELECT COUNT(*) n FROM showtimes')['n'] ?? 0);
    logMsg("Đồng bộ lịch chiếu thành công: Hiện có $totalShowtimes suất chiếu hợp lệ.");
    logMsg("=== HOÀN TẤT MIGRATION DỮ LIỆU PHIM VÀ POSTER ===");
    $ok = true;
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $ok = false;
    logMsg("LỖI MIGRATION: " . $e->getMessage());
    logMsg("Đã Rollback transaction - Dữ liệu database được giữ nguyên trạng.");
}

// Xuất kết quả
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
    <title>CineGo - Cập nhật dữ liệu phim</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #0f172a; color: #f8fafc; padding: 30px; }
        .card { max-width: 720px; margin: 0 auto; background: #1e293b; border-radius: 12px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.4); }
        h1 { color: #38bdf8; font-size: 20px; margin-top: 0; }
        .log { background: #0f172a; border-radius: 8px; padding: 16px; font-family: monospace; font-size: 13px; line-height: 1.6; max-height: 400px; overflow-y: auto; }
        .success { color: #4ade80; font-weight: bold; }
        .error { color: #f87171; font-weight: bold; }
        a { color: #38bdf8; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="card">
    <h1>CineGo – Cập nhật dữ liệu phim thực tế</h1>
    <div class="log">
        <?php foreach ($logs as $l): ?>
            <div><?= htmlspecialchars($l) ?></div>
        <?php endforeach; ?>
    </div>
    <p style="margin-top: 20px;">
        <span class="<?= $ok ? 'success' : 'error' ?>"><?= $ok ? 'Hoàn thành cập nhật an toàn!' : 'Cập nhật thất bại!' ?></span>
        &nbsp;|&nbsp; <a href="../public/index.php">Về trang chủ CineGo →</a>
    </p>
</div>
</body>
</html>
