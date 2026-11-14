<?php
declare(strict_types=1);

namespace Admin;

use Booking;
use Cinema;
use Database;
use Payment;
use PaymentService;
use User;

/** Đơn đặt vé: tra cứu, xem chi tiết, xác nhận thu tiền tại quầy, huỷ đơn. (Admin & nhân viên) */
final class BookingController
{
    public static function index(): void
    {
        Booking::expireStale();
        $f = [
            'q' => queryStr('q'), 'status' => queryStr('status'), 'pay' => queryStr('pay'), 'cinema' => queryStr('cinema'),
            'from' => validDate(queryStr('from')) ? queryStr('from') : '', 'to' => validDate(queryStr('to')) ? queryStr('to') : '',
        ];
        $where = [];
        $p = [];
        if ($f['q'] !== '') {
            $where[] = '(b.code LIKE ? OR b.customer_name LIKE ? OR b.phone LIKE ? OR b.email LIKE ?)';
            array_push($p, ...array_fill(0, 4, Ui::like($f['q'])));
        }
        if (isset(Booking::STATUS_LABEL[$f['status']])) {
            $where[] = 'b.status = ?';
            $p[] = $f['status'];
        }
        if (isset(Payment::STATUS_LABEL[$f['pay']])) {
            $where[] = '(SELECT status FROM payments px WHERE px.booking_code = b.code ORDER BY px.id DESC LIMIT 1) = ?';
            $p[] = $f['pay'];
        }
        if ($f['cinema'] !== '') {
            $where[] = 's.cinema_id = ?';
            $p[] = $f['cinema'];
        }
        if ($f['from'] !== '') {
            $where[] = 'b.created_at >= ?';
            $p[] = $f['from'] . ' 00:00:00';
        }
        if ($f['to'] !== '') {
            $where[] = 'b.created_at <= ?';
            $p[] = $f['to'] . ' 23:59:59';
        }
        $w = $where ? ' WHERE ' . implode(' AND ', $where) : '';
        $from = ' FROM bookings b JOIN showtimes s ON s.id = b.showtime_id JOIN movies m ON m.id = s.movie_id JOIN cinemas c ON c.id = s.cinema_id';
        $sum = Database::one("SELECT COUNT(*) n, COALESCE(SUM(CASE WHEN b.status = 'confirmed' THEN b.total END), 0) amount $from$w", $p);
        $pg = Ui::paginate((int)$sum['n']);
        $rows = Database::all("SELECT b.*, m.title, s.show_date, s.show_time, c.name AS cinema_name,
                (SELECT GROUP_CONCAT(seat_code ORDER BY seat_code SEPARATOR ', ') FROM booking_seats bs WHERE bs.booking_code = b.code) AS seat_list,
                (SELECT status FROM payments px WHERE px.booking_code = b.code ORDER BY px.id DESC LIMIT 1) AS pay_status
            $from$w ORDER BY b.created_at DESC LIMIT " . Ui::PER_PAGE . ' OFFSET ' . $pg['offset'], $p);
        Ui::render('bookings/index', 'Đơn đặt vé', 'bookings', [
            'rows' => $rows, 'f' => $f, 'pg' => $pg, 'sum' => $sum, 'cinemas' => Cinema::search(),
        ]);
    }

    public static function show(string $code): void
    {
        Booking::expireStale();
        $b = Booking::findByCode($code) ?? notFound();
        $user = $b['user_id'] ? User::find((int)$b['user_id']) : null;
        $history = Database::one('SELECT COUNT(*) n, COALESCE(SUM(CASE WHEN status = \'confirmed\' THEN total END), 0) spent FROM bookings WHERE phone = ? OR email = ?', [$b['phone'], $b['email']]);
        Ui::render('bookings/show', 'Đơn ' . $b['code'], 'bookings', ['b' => $b, 'user' => $user, 'history' => $history]);
    }

    /** Nhân viên xác nhận đã thu tiền tại quầy. */
    public static function markPaid(string $code): void
    {
        checkCsrf();
        $b = Booking::findByCode($code) ?? notFound();
        $p = $b['payment'];
        if ($b['status'] !== 'confirmed' || !$p || $p['status'] !== 'unpaid') {
            Ui::back('/admin/bookings/' . rawurlencode($code), 'Đơn này không ở trạng thái chờ thu tiền tại quầy.', 'error');
        }
        $me = User::current();
        Payment::update((int)$p['id'], ['status' => 'success', 'paid_at' => date('Y-m-d H:i:s'),
            'message' => 'Đã thu tiền tại quầy – ' . ($me['name'] ?? 'nhân viên')]);
        Ui::back('/admin/bookings/' . rawurlencode($code), 'Đã xác nhận thu ' . money((int)$b['total']) . ' tại quầy cho đơn ' . $code . '.');
    }

    /** Huỷ đơn (admin/nhân viên được huỷ cả khi gần giờ chiếu); trả ghế và hoàn tiền nếu đã thanh toán. */
    public static function cancel(string $code): void
    {
        checkCsrf();
        $b = Booking::findByCode($code) ?? notFound();
        if (!in_array($b['status'], ['confirmed', 'pending'], true)) {
            Ui::back('/admin/bookings/' . rawurlencode($code), 'Đơn đã ' . mb_strtolower($b['status_label']) . ', không thể huỷ.', 'error');
        }
        if ($b['showtime'] && \Showtime::isPast($b['showtime'])) {
            Ui::back('/admin/bookings/' . rawurlencode($code), 'Suất chiếu đã diễn ra, không thể huỷ đơn.', 'error');
        }
        Booking::release($b['code'], 'cancelled');
        $msg = PaymentService::refund($b);
        Ui::back('/admin/bookings/' . rawurlencode($code), 'Đã huỷ đơn ' . $code . ' và trả ghế. ' . $msg);
    }
}
