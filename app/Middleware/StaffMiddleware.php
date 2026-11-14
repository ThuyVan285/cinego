<?php
declare(strict_types=1);

/** Cho nhân viên rạp và quản trị viên (phần vận hành của trang quản trị). */
final class StaffMiddleware
{
    public const ROLES = ['admin', 'staff'];

    public static function handle(): void
    {
        $u = User::requireLogin(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
        if (!in_array($u['role'], self::ROLES, true)) {
            http_response_code(403);
            render('errors/error', ['title' => 'Không có quyền truy cập', 'message' => 'Chức năng này chỉ dành cho nhân viên và quản trị viên CineGo.']);
            exit;
        }
    }
}
