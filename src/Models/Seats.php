<?php
declare(strict_types=1);

/** Sơ đồ phòng chiếu: hàng A–D thường, E–H VIP, hàng I ghế đôi. */
final class Seats
{
    public const ROWS = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];
    public const COLS = 12;
    public const COUPLE_COLS = 6;
    public const TYPE_LABEL = ['standard' => 'Thường', 'vip' => 'VIP', 'couple' => 'Ghế đôi'];

    public static function typeOfRow(string $row): string
    {
        return match (true) {
            $row === 'I' => 'couple',
            $row >= 'E' => 'vip',
            default => 'standard',
        };
    }

    public static function isValid(string $code): bool
    {
        if (!preg_match('/^([A-I])(\d{1,2})$/', $code, $m)) {
            return false;
        }
        $max = $m[1] === 'I' ? self::COUPLE_COLS : self::COLS;
        return (int)$m[2] >= 1 && (int)$m[2] <= $max;
    }

    public static function typeOf(string $code): string
    {
        return self::typeOfRow($code[0]);
    }

    public static function price(string $code, array $showtime): int
    {
        return (int)$showtime['price_' . self::typeOf($code)];
    }

    /** Ghế "đã có người đặt" mang tính minh hoạ, ổn định theo suất chiếu. */
    public static function isDemoTaken(string $showtimeId, string $code): bool
    {
        return crc32('t|' . $showtimeId . '|' . $code) % 100 < 24;
    }

    /** Ghế không khả dụng (hỏng/bảo trì) theo phòng. */
    public static function isBlocked(string $showtimeId, string $code): bool
    {
        return self::typeOf($code) !== 'couple' && crc32('b|' . $showtimeId . '|' . $code) % 60 === 0;
    }

    /** @return string[] mã ghế đã đặt thật (do người dùng demo). */
    public static function realTaken(string $showtimeId): array
    {
        return array_column(Database::all('SELECT seat_code FROM booking_seats WHERE showtime_id = ?', [$showtimeId]), 'seat_code');
    }

    /** @return array<string,string> code => available|booked|blocked */
    public static function statusMap(string $showtimeId): array
    {
        $real = array_flip(self::realTaken($showtimeId));
        $map = [];
        foreach (self::ROWS as $row) {
            $cols = $row === 'I' ? self::COUPLE_COLS : self::COLS;
            for ($c = 1; $c <= $cols; $c++) {
                $code = $row . $c;
                $map[$code] = isset($real[$code]) || self::isDemoTaken($showtimeId, $code) ? 'booked'
                    : (self::isBlocked($showtimeId, $code) ? 'blocked' : 'available');
            }
        }
        return $map;
    }
}
