<?php
declare(strict_types=1);

/**
 * Số liệu cho trang quản trị.
 * Doanh thu = tiền thực thu: giao dịch 'success' (tính theo ngày thanh toán paid_at), không tính đơn chưa trả / đã hoàn.
 */
final class StatisticsService
{
    /** Điều kiện lọc theo rạp (tuỳ chọn) dùng chung. @return array{0:string,1:array} */
    private static function cinemaFilter(?string $cinemaId, string $alias = 's'): array
    {
        return $cinemaId ? [" AND $alias.cinema_id = ?", [$cinemaId]] : ['', []];
    }

    /** Tổng quan trong khoảng [from, to]. */
    public static function overview(string $from, string $to, ?string $cinemaId = null): array
    {
        [$cw, $cp] = self::cinemaFilter($cinemaId);
        $range = [$from . ' 00:00:00', $to . ' 23:59:59'];
        $paid = Database::one("SELECT COUNT(*) n, COALESCE(SUM(p.amount), 0) revenue FROM payments p
            JOIN bookings b ON b.code = p.booking_code JOIN showtimes s ON s.id = b.showtime_id
            WHERE p.status = 'success' AND p.paid_at BETWEEN ? AND ?$cw", [...$range, ...$cp]);
        $book = Database::one("SELECT COUNT(*) n, SUM(b.status = 'confirmed') confirmed, SUM(b.status = 'cancelled') cancelled,
                SUM(b.status = 'expired') expired, SUM(b.status = 'pending') pending
            FROM bookings b JOIN showtimes s ON s.id = b.showtime_id WHERE b.created_at BETWEEN ? AND ?$cw", [...$range, ...$cp]);
        $tickets = Database::one("SELECT COUNT(*) n FROM booking_seats bs JOIN bookings b ON b.code = bs.booking_code JOIN showtimes s ON s.id = b.showtime_id
            WHERE b.status = 'confirmed' AND b.created_at BETWEEN ? AND ?$cw", [...$range, ...$cp]);
        $unpaid = Database::one("SELECT COUNT(*) n, COALESCE(SUM(p.amount), 0) amount FROM payments p JOIN bookings b ON b.code = p.booking_code
            JOIN showtimes s ON s.id = b.showtime_id WHERE p.status = 'unpaid' AND b.status = 'confirmed'$cw", $cp);
        return [
            'revenue' => (int)$paid['revenue'],
            'paid_orders' => (int)$paid['n'],
            'bookings' => (int)$book['n'],
            'confirmed' => (int)$book['confirmed'],
            'cancelled' => (int)$book['cancelled'] + (int)$book['expired'],
            'pending' => (int)$book['pending'],
            'tickets' => (int)$tickets['n'],
            'avg_order' => (int)$paid['n'] ? intdiv((int)$paid['revenue'], (int)$paid['n']) : 0,
            'unpaid_count' => (int)$unpaid['n'],
            'unpaid_amount' => (int)$unpaid['amount'],
            'movies' => (int)Database::one("SELECT COUNT(*) n FROM movies WHERE status = 'now'")['n'],
            'cinemas' => (int)Database::one('SELECT COUNT(*) n FROM cinemas')['n'],
            'users' => (int)Database::one("SELECT COUNT(*) n FROM users WHERE role = 'user'")['n'],
        ];
    }

    /** Doanh thu & số vé theo ngày. @return array<string,array{revenue:int,orders:int}> 'Y-m-d' => ... (đủ mọi ngày) */
    public static function revenueByDay(string $from, string $to, ?string $cinemaId = null): array
    {
        [$cw, $cp] = self::cinemaFilter($cinemaId);
        $out = [];
        for ($d = $from; $d <= $to; $d = addDays($d, 1)) {
            $out[$d] = ['revenue' => 0, 'orders' => 0];
        }
        foreach (Database::all("SELECT DATE(p.paid_at) d, SUM(p.amount) revenue, COUNT(*) orders FROM payments p
                JOIN bookings b ON b.code = p.booking_code JOIN showtimes s ON s.id = b.showtime_id
                WHERE p.status = 'success' AND p.paid_at BETWEEN ? AND ?$cw GROUP BY DATE(p.paid_at)",
                [$from . ' 00:00:00', $to . ' 23:59:59', ...$cp]) as $r) {
            $out[$r['d']] = ['revenue' => (int)$r['revenue'], 'orders' => (int)$r['orders']];
        }
        return $out;
    }

    /** Doanh thu 12 tháng gần nhất. @return array<string,int> 'Y-m' => doanh thu */
    public static function revenueByMonth(int $months = 12, ?string $cinemaId = null): array
    {
        [$cw, $cp] = self::cinemaFilter($cinemaId);
        $out = [];
        $start = date('Y-m-01', strtotime('first day of -' . ($months - 1) . ' month'));
        for ($i = 0; $i < $months; $i++) {
            $out[date('Y-m', strtotime("$start +$i month"))] = 0;
        }
        foreach (Database::all("SELECT DATE_FORMAT(p.paid_at, '%Y-%m') m, SUM(p.amount) revenue FROM payments p
                JOIN bookings b ON b.code = p.booking_code JOIN showtimes s ON s.id = b.showtime_id
                WHERE p.status = 'success' AND p.paid_at >= ?$cw GROUP BY m", [$start, ...$cp]) as $r) {
            if (isset($out[$r['m']])) {
                $out[$r['m']] = (int)$r['revenue'];
            }
        }
        return $out;
    }

    /** Doanh thu theo rạp trong khoảng thời gian. @return array[] name, revenue, tickets */
    public static function revenueByCinema(string $from, string $to): array
    {
        return Database::all("SELECT c.id, c.name, COALESCE(SUM(p.amount), 0) revenue, COUNT(p.id) orders
            FROM cinemas c
            LEFT JOIN showtimes s ON s.cinema_id = c.id
            LEFT JOIN bookings b ON b.showtime_id = s.id
            LEFT JOIN payments p ON p.booking_code = b.code AND p.status = 'success' AND p.paid_at BETWEEN ? AND ?
            GROUP BY c.id, c.name ORDER BY revenue DESC", [$from . ' 00:00:00', $to . ' 23:59:59']);
    }

    /** Phim có doanh thu cao nhất. */
    public static function topMovies(string $from, string $to, ?string $cinemaId = null, int $limit = 8): array
    {
        [$cw, $cp] = self::cinemaFilter($cinemaId);
        return Database::all("SELECT m.id, m.title, SUM(p.amount) revenue, COUNT(DISTINCT b.code) orders,
                (SELECT COUNT(*) FROM booking_seats bs JOIN bookings b2 ON b2.code = bs.booking_code JOIN showtimes s2 ON s2.id = b2.showtime_id
                  WHERE s2.movie_id = m.id AND b2.status = 'confirmed' AND b2.created_at BETWEEN ? AND ?) tickets
            FROM payments p JOIN bookings b ON b.code = p.booking_code JOIN showtimes s ON s.id = b.showtime_id JOIN movies m ON m.id = s.movie_id
            WHERE p.status = 'success' AND p.paid_at BETWEEN ? AND ?$cw
            GROUP BY m.id, m.title ORDER BY revenue DESC LIMIT " . max(1, min(50, $limit)),
            [$from . ' 00:00:00', $to . ' 23:59:59', $from . ' 00:00:00', $to . ' 23:59:59', ...$cp]);
    }

    /** Giao dịch theo trạng thái & cổng thanh toán (theo ngày tạo giao dịch). */
    public static function transactions(string $from, string $to, ?string $cinemaId = null): array
    {
        [$cw, $cp] = self::cinemaFilter($cinemaId);
        return Database::all("SELECT p.status, p.provider, COUNT(*) n, COALESCE(SUM(p.amount), 0) amount FROM payments p
            JOIN bookings b ON b.code = p.booking_code JOIN showtimes s ON s.id = b.showtime_id
            WHERE p.created_at BETWEEN ? AND ?$cw GROUP BY p.status, p.provider ORDER BY p.status, p.provider",
            [$from . ' 00:00:00', $to . ' 23:59:59', ...$cp]);
    }

    /** Đơn đặt vé mới nhất (kèm tên phim). */
    public static function latestBookings(int $limit = 8): array
    {
        return Database::all(
            'SELECT b.code, b.customer_name, b.total, b.status, b.created_at, m.title AS movie
             FROM bookings b JOIN showtimes s ON s.id = b.showtime_id JOIN movies m ON m.id = s.movie_id
             ORDER BY b.created_at DESC LIMIT ' . max(1, min(100, $limit))
        );
    }
}
