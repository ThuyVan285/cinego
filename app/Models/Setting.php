<?php
declare(strict_types=1);

/** Cấu hình chỉnh được trong trang quản trị (bảng settings: k → v). */
final class Setting
{
    /** Giá vé mặc định khi tạo suất chiếu mới (từng suất vẫn có giá riêng). */
    public const DEFAULTS = [
        'price_standard' => 70000,
        'price_vip' => 90000,
        'price_couple' => 170000,
    ];

    private static ?array $cache = null;

    public static function all(): array
    {
        if (self::$cache === null) {
            self::$cache = self::DEFAULTS;
            try {
                foreach (Database::all('SELECT k, v FROM settings') as $r) {
                    self::$cache[$r['k']] = $r['v'];
                }
            } catch (PDOException) {
                // CSDL cũ chưa có bảng settings → dùng mặc định
            }
        }
        return self::$cache;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::all()[$key] ?? $default;
    }

    public static function set(string $key, string|int $value): void
    {
        Database::exec('INSERT INTO settings (k, v) VALUES (?, ?) ON DUPLICATE KEY UPDATE v = VALUES(v)', [$key, (string)$value]);
        self::$cache = null;
    }

    /** @return array{standard:int, vip:int, couple:int} */
    public static function prices(): array
    {
        return [
            'standard' => (int)self::get('price_standard'),
            'vip' => (int)self::get('price_vip'),
            'couple' => (int)self::get('price_couple'),
        ];
    }
}
