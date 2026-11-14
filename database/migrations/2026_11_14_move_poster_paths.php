<?php
declare(strict_types=1);

/**
 * Migration: poster chuyển từ public/assets/images/posters/ sang public/assets/images/movies/.
 * Cập nhật movies.poster_url trong CSDL đang có (không mất dữ liệu đặt vé). Chạy lại nhiều lần vẫn an toàn.
 * Chạy:  php database/migrations/2026_11_14_move_poster_paths.php
 */
require_once __DIR__ . '/../../app/bootstrap.php';

try {
    $n = Database::exec(
        "UPDATE movies SET poster_url = REPLACE(poster_url, '/assets/images/posters/', '/assets/images/movies/')
         WHERE poster_url LIKE '/assets/images/posters/%'"
    );
    echo "Đã cập nhật đường dẫn poster cho $n phim.", PHP_EOL;
} catch (Throwable $e) {
    echo 'LỖI: ', $e->getMessage(), PHP_EOL;
    exit(1);
}
