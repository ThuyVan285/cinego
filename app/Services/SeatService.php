<?php
declare(strict_types=1);

/** Chọn ghế: kiểm tra lựa chọn, giữ ghế tạm thời (seat_holds) và nhả ghế. */
final class SeatService
{
    public static function holdMinutes(): int
    {
        return (int)config('app.seat_hold_minutes', 10);
    }

    /**
     * @param string[] $codes  mã ghế khách chọn
     * @param array<string,string> $status  Seat::statusMap() nhìn từ phía khách này
     * @return string|null  thông báo lỗi, hoặc null nếu hợp lệ
     */
    public static function validateSelection(array $codes, array $status): ?string
    {
        if (!$codes) {
            return 'Vui lòng chọn ít nhất 1 ghế để tiếp tục.';
        }
        if (count($codes) > MAX_SEATS) {
            return 'Mỗi lượt đặt tối đa ' . MAX_SEATS . ' ghế.';
        }
        foreach ($codes as $c) {
            $s = $status[$c] ?? null;
            if ($s === null) {
                return 'Ghế ' . $c . ' không có trong phòng chiếu này.';
            }
            if ($s === 'booked') {
                return 'Ghế ' . $c . ' đã có người đặt. Vui lòng chọn ghế khác.';
            }
            if ($s === 'blocked') {
                return 'Ghế ' . $c . ' đang tạm ngưng sử dụng. Vui lòng chọn ghế khác.';
            }
            if ($s === 'held') {
                return 'Ghế ' . $c . ' đang được người khác giữ. Vui lòng chọn ghế khác.';
            }
        }
        return null;
    }

    /**
     * Giữ các ghế cho khách trong holdMinutes() phút; nhả các ghế khác khách đã giữ ở suất này.
     * Khoá dòng trong transaction + khoá chính (showtime_id, seat_code) nên hai người không thể giữ cùng một ghế.
     * @return array{0:?string,1:?string} [lỗi, thời điểm hết hạn 'Y-m-d H:i:s']
     */
    public static function hold(string $showtimeId, array $codes, string $visitorId): array
    {
        $pdo = Database::pdo();
        $until = date('Y-m-d H:i:s', time() + self::holdMinutes() * 60);
        try {
            $pdo->beginTransaction();
            Database::exec('DELETE FROM seat_holds WHERE showtime_id = ? AND expires_at <= NOW()', [$showtimeId]);
            $mine = array_column(Database::all('SELECT seat_code FROM seat_holds WHERE showtime_id = ? AND visitor_id = ? FOR UPDATE', [$showtimeId, $visitorId]), 'seat_code');
            $release = array_diff($mine, $codes);
            if ($release) {
                $in = implode(',', array_fill(0, count($release), '?'));
                Database::exec("DELETE FROM seat_holds WHERE showtime_id = ? AND visitor_id = ? AND seat_code IN ($in)", [$showtimeId, $visitorId, ...array_values($release)]);
            }
            foreach ($codes as $code) {
                $taken = Database::one('SELECT 1 FROM booking_seats WHERE showtime_id = ? AND seat_code = ? AND active = 1', [$showtimeId, $code]);
                if ($taken) {
                    throw new RuntimeException('Ghế ' . $code . ' vừa có người đặt. Vui lòng chọn ghế khác.');
                }
                if (in_array($code, $mine, true)) {
                    Database::exec('UPDATE seat_holds SET expires_at = ? WHERE showtime_id = ? AND seat_code = ? AND visitor_id = ?', [$until, $showtimeId, $code, $visitorId]);
                } else {
                    // Trùng khoá chính = người khác vừa giữ ghế này.
                    Database::exec('INSERT INTO seat_holds (showtime_id, seat_code, visitor_id, expires_at) VALUES (?,?,?,?)', [$showtimeId, $code, $visitorId, $until]);
                }
            }
            $pdo->commit();
            return [null, $until];
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            if ($e instanceof RuntimeException) {
                return [$e->getMessage(), null];
            }
            if ($e instanceof PDOException && ($e->errorInfo[1] ?? 0) === 1062) {
                return ['Một trong các ghế vừa được người khác giữ. Vui lòng chọn lại.', null];
            }
            throw $e;
        }
    }

    /** Nhả ghế khách đang giữ ở một suất (hoặc chỉ các ghế trong $codes). */
    public static function release(string $showtimeId, string $visitorId, ?array $codes = null): void
    {
        if ($codes === null) {
            Database::exec('DELETE FROM seat_holds WHERE showtime_id = ? AND visitor_id = ?', [$showtimeId, $visitorId]);
            return;
        }
        if ($codes) {
            $in = implode(',', array_fill(0, count($codes), '?'));
            Database::exec("DELETE FROM seat_holds WHERE showtime_id = ? AND visitor_id = ? AND seat_code IN ($in)", [$showtimeId, $visitorId, ...array_values($codes)]);
        }
    }

    /** Thời điểm hết hạn giữ ghế của khách ở suất này (null nếu không giữ ghế nào). */
    public static function holdUntil(string $showtimeId, string $visitorId): ?string
    {
        $r = Database::one('SELECT MIN(expires_at) t FROM seat_holds WHERE showtime_id = ? AND visitor_id = ? AND expires_at > NOW()', [$showtimeId, $visitorId]);
        return $r['t'] ?? null;
    }
}
