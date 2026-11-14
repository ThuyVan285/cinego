<?php
declare(strict_types=1);

namespace Admin;

/** Tiện ích dùng chung cho các controller quản trị. */
final class Ui
{
    public const PER_PAGE = 20;

    /** Render app/Views/admin/{$view}.php trong layout quản trị. */
    public static function render(string $view, string $title, string $nav, array $vars = []): void
    {
        render('admin/' . $view, $vars + ['title' => $title . ' – Quản trị ' . APP_NAME, 'nav' => $nav, 'heading' => $title], 'admin');
    }

    /** Chuỗi POST đã trim; chuỗi không phải UTF-8 hợp lệ bị coi như rỗng. */
    public static function str(string $k, string $default = ''): string
    {
        $v = $_POST[$k] ?? $default;
        return is_string($v) && mb_check_encoding($v, 'UTF-8') ? trim($v) : $default;
    }

    public static function int(string $k, int $default = 0): int
    {
        $v = $_POST[$k] ?? null;
        return is_numeric($v) ? (int)$v : $default;
    }

    /** Mảng chuỗi POST (checkbox nhiều lựa chọn). @return string[] */
    public static function list(string $k): array
    {
        $v = $_POST[$k] ?? [];
        return is_array($v) ? array_values(array_filter(array_map(fn($x) => is_string($x) ? trim($x) : '', $v), 'strlen')) : [];
    }

    /** Trang hiện tại từ ?page= và tổng số trang. @return array{page:int, pages:int, offset:int, total:int} */
    public static function paginate(int $total, int $perPage = self::PER_PAGE): array
    {
        $pages = max(1, (int)ceil($total / $perPage));
        $page = min($pages, max(1, (int)($_GET['page'] ?? 1)));
        return ['page' => $page, 'pages' => $pages, 'offset' => ($page - 1) * $perPage, 'total' => $total];
    }

    /** Điều kiện LIKE an toàn cho ô tìm kiếm. */
    public static function like(string $q): string
    {
        return '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $q) . '%';
    }

    public static function back(string $to, string $msg, string $type = 'ok'): never
    {
        flash($type, $msg);
        redirect($to);
    }

    /** Ngày giờ 'Y-m-d' hợp lệ? */
    public static function isDate(string $d): bool
    {
        return validDate($d);
    }
}
