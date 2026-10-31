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
        render('tickets/show', ['title' => 'Vé ' . $b['code'] . ' – ' . APP_NAME, 'nav' => 'tickets', 'b' => $b]);
    }

    public static function clear(): void
    {
        checkCsrf();
        Booking::clearAll(visitorId());
        flash('ok', 'Đã xoá lịch sử vé demo. Các ghế đã được trả về trạng thái trống.');
        redirect('/tickets');
    }
}
