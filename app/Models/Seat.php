<?php
declare(strict_types=1);

/**
 * Ghế của phòng chiếu (bảng seats) và tình trạng ghế theo từng suất chiếu.
 * Tình trạng: available (trống) · mine (mình đang giữ) · held (người khác đang giữ / đang thanh toán) · booked (đã bán)
 *             · blocked (ghế bị tắt trong trang quản trị).
 */
final class Seat
{
    public const TYPE_LABEL = ['standard' => 'Thường', 'vip' => 'VIP', 'couple' => 'Ghế đôi'];

    /** @var array<int,array[]> cache ghế theo room_id trong một request */
    private static array $cache = [];

    /** @return array[] ghế của phòng: code, row_label, col_no, type (đã sắp xếp) */
    public static function forRoom(int $roomId): array
    {
        return self::$cache[$roomId] ??= Database::all(
            'SELECT code, row_label, col_no, type, active FROM seats WHERE room_id = ? ORDER BY row_label, col_no', [$roomId]);
    }

    /** @return array<string,string> mã ghế → loại ghế của phòng chiếu suất này */
    public static function typeMap(array $showtime): array
    {
        // Chỉ ghế đang hoạt động mới đặt được; ghế tắt (hỏng/bảo trì) vẫn vẽ trên sơ đồ nhưng không chọn được.
        return array_column(array_filter(self::forRoom((int)$showtime['room_id']), fn($s) => (int)$s['active'] === 1), 'type', 'code');
    }

    /** @return array<string,array[]> ghế nhóm theo hàng (A → J) */
    public static function rows(array $showtime): array
    {
        $rows = [];
        foreach (self::forRoom((int)$showtime['room_id']) as $s) {
            $rows[$s['row_label']][] = $s;
        }
        return $rows;
    }

    public static function exists(string $code, array $showtime): bool
    {
        return isset(self::typeMap($showtime)[$code]);
    }

    public static function typeOf(string $code, array $showtime): string
    {
        return self::typeMap($showtime)[$code] ?? 'standard';
    }

    public static function price(string $code, array $showtime): int
    {
        return (int)$showtime['price_' . self::typeOf($code, $showtime)];
    }

    /** Phòng có ghế đôi không (để ẩn chú thích ghế đôi). */
    public static function hasCouple(array $showtime): bool
    {
        return in_array('couple', self::typeMap($showtime), true);
    }

    /**
     * Tình trạng mọi ghế của suất chiếu, nhìn từ phía khách $visitorId.
     * @return array<string,string> code => available|mine|held|booked|blocked
     */
    public static function statusMap(string $showtimeId, ?string $visitorId = null): array
    {
        $st = Showtime::find($showtimeId);
        if (!$st) {
            return [];
        }
        Booking::expireStale();

        $map = [];
        foreach (self::forRoom((int)$st['room_id']) as $seat) {
            $map[$seat['code']] = (int)$seat['active'] === 1 ? 'available' : 'blocked';
        }
        // Ghế thuộc đơn còn hiệu lực: đã xác nhận → booked, đang chờ thanh toán → held.
        foreach (Database::all('SELECT bs.seat_code, b.status FROM booking_seats bs JOIN bookings b ON b.code = bs.booking_code
                WHERE bs.showtime_id = ? AND bs.active = 1', [$showtimeId]) as $r) {
            $map[$r['seat_code']] = $r['status'] === 'confirmed' ? 'booked' : 'held';
        }
        // Ghế khách khác đang chọn (giữ tạm, chưa hết hạn).
        foreach (Database::all('SELECT seat_code, visitor_id FROM seat_holds WHERE showtime_id = ? AND expires_at > NOW()', [$showtimeId]) as $h) {
            if (($map[$h['seat_code']] ?? '') === 'available') {
                $map[$h['seat_code']] = $h['visitor_id'] === $visitorId ? 'mine' : 'held';
            }
        }
        return $map;
    }
}
