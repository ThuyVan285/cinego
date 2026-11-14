<?php
declare(strict_types=1);

final class TicketController
{
    public static function index(): void
    {
        render('tickets/index', ['title' => 'Vé của tôi – ' . APP_NAME, 'nav' => 'tickets', 'list' => Booking::forVisitor(visitorId())]);
    }

    public static function show(string $code): void
    {
        $b = Booking::find($code, visitorId()) ?? notFound();
        render('tickets/detail', ['title' => 'Vé ' . $b['code'] . ' – ' . APP_NAME, 'nav' => 'tickets', 'b' => $b]);
    }

    public static function clear(): void
    {
        checkCsrf();
        Booking::clearAll(visitorId());
        flash('ok', 'Đã xoá lịch sử vé demo. Các ghế đã được trả về trạng thái trống.');
        redirect('/tickets');
    }

    /** Huỷ vé (còn ít nhất app.cancel_before_minutes phút trước giờ chiếu): trả ghế và hoàn tiền nếu đã thanh toán. */
    public static function cancel(string $code): void
    {
        checkCsrf();
        $b = Booking::find($code, visitorId()) ?? notFound();
        if (!$b['can_cancel']) {
            $mins = (int)config('app.cancel_before_minutes', 120);
            flash('error', 'Vé này không thể huỷ (chỉ huỷ được vé đã xác nhận, trước giờ chiếu ít nhất ' . $mins . ' phút).');
            redirect('/tickets/' . rawurlencode($code));
        }
        Booking::release($b['code'], 'cancelled');
        $refund = PaymentService::refund($b);
        flash('ok', 'Đã huỷ vé ' . $b['code'] . '. ' . $refund);
        redirect('/tickets/' . rawurlencode($code));
    }

    /** Trang giao diện "Vé Giấy" độc lập (/ticket) – tự chứa HTML/CSS, không dùng layout chung. */
    public static function paper(): void
    {
        require BASE_PATH . '/app/Views/tickets/paper.php';
    }
}
