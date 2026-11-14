<?php
declare(strict_types=1);

/** Quy tắc kiểm tra dữ liệu dùng chung cho các form. */
final class Validator
{
    public static function email(string $v): bool
    {
        return filter_var($v, FILTER_VALIDATE_EMAIL) !== false;
    }

    /** Họ tên 2–80 ký tự chữ (hỗ trợ tiếng Việt có dấu), khoảng trắng, ' . - */
    public static function name(string $v): bool
    {
        $len = mb_strlen($v);
        return $len >= 2 && $len <= 80 && preg_match('/^[\p{L}\p{M}\s\'.-]{2,80}$/u', $v) === 1;
    }

    /** Số di động VN: 0 hoặc +84, đầu số 3/5/7/8/9, tổng 10 chữ số. */
    public static function mobile(string $v): bool
    {
        return preg_match('/^(0|\+84)(3|5|7|8|9)\d{8}$/', $v) === 1;
    }

    /** Bỏ khoảng trắng, dấu chấm, gạch ngang trong số điện thoại. */
    public static function cleanPhone(string $v): string
    {
        return (string)preg_replace('/[\s.\-]/', '', $v);
    }

    public static function password(string $v, int $min = 6): bool
    {
        return strlen($v) >= $min;
    }
}
