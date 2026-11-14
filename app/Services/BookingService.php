<?php
declare(strict_types=1);

/** Kiểm soát các bước của luồng đặt vé (dùng chung cho Booking/Seat/PaymentController). */
final class BookingService
{
    /**
     * Trả về tóm tắt đơn đang đặt; nếu chưa đủ điều kiện cho bước hiện tại thì chuyển hướng về bước phù hợp.
     * Từ bước bắp nước trở đi, ghế phải còn trong thời gian giữ (hoặc vẫn còn trống để giữ lại).
     */
    public static function requireCurrent(bool $needSeats = false, bool $needCustomer = false): array
    {
        $sum = Booking::currentSummary();
        if (!$sum) {
            flash('error', 'Bạn chưa chọn suất chiếu.');
            redirect('/booking');
        }
        if (!Showtime::isBookable($sum['showtime'])) {
            Booking::reset();
            flash('error', 'Suất chiếu đã qua giờ, vui lòng chọn suất khác.');
            redirect('/booking');
        }
        if ($needSeats && !$sum['seats']) {
            flash('error', 'Vui lòng chọn ghế trước khi tiếp tục.');
            redirect('/booking/seats');
        }
        if ($needSeats) {
            $sum = self::ensureHold($sum);
        }
        if ($needCustomer && (empty($sum['customer']['name']))) {
            flash('error', 'Vui lòng điền thông tin và xác nhận trước khi thanh toán.');
            redirect('/booking/confirm');
        }
        return $sum;
    }

    /** Hết giờ giữ ghế: thử giữ lại (nếu ghế vẫn trống), không được thì quay về chọn ghế. */
    private static function ensureHold(array $sum): array
    {
        $stId = $sum['showtime']['id'];
        if (SeatService::holdUntil($stId, visitorId()) !== null) {
            return $sum;
        }
        // Ghế đang bị khoá bởi chính đơn chờ thanh toán của khách (vd. quay lại từ cổng mà chưa có kết quả hợp lệ):
        // vẫn là ghế của khách – thanh toán lại sẽ huỷ đơn chờ cũ và giữ lại ghế (PaymentService::start).
        $pending = Booking::state()['pending_code'] ?? null;
        if ($pending) {
            $b = Database::one('SELECT status, expires_at FROM bookings WHERE code = ?', [$pending]);
            if ($b && $b['status'] === 'pending' && strtotime((string)$b['expires_at']) > time()) {
                $sum['hold_until'] = $b['expires_at'];
                return $sum;
            }
        }
        [$err, $until] = SeatService::hold($stId, array_column($sum['seats'], 'code'), visitorId());
        if ($err !== null) {
            $s = Booking::state();
            $s['seats'] = [];
            $s['hold_until'] = null;
            Booking::save($s);
            flash('error', 'Hết thời gian giữ ghế và ' . lcfirst($err));
            redirect('/booking/seats');
        }
        $s = Booking::state();
        $s['hold_until'] = $until;
        Booking::save($s);
        $sum['hold_until'] = $until;
        return $sum;
    }
}
