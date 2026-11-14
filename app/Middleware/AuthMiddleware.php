<?php
declare(strict_types=1);

/** Chặn khách chưa đăng nhập; chuyển về /login rồi quay lại trang đang xem. */
final class AuthMiddleware
{
    public static function handle(): void
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        User::requireLogin($path);
    }
}
