<?php
declare(strict_types=1);

final class SeatController
{
    /** Bước 2 – chọn ghế; khi tiếp tục, ghế được giữ cho khách trong SeatService::holdMinutes() phút. */
    public static function select(): void
    {
        $sum = BookingService::requireCurrent();
        $st = $sum['showtime'];
        $status = Seat::statusMap($st['id'], visitorId());

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            checkCsrf();
            $codes = array_values(array_unique(array_filter(explode(',', (string)($_POST['seats'] ?? '')), 'strlen')));
            $err = SeatService::validateSelection($codes, $status);
            $until = null;
            if ($err === null) {
                [$err, $until] = SeatService::hold($st['id'], $codes, visitorId());
            }
            if ($err === null) {
                $s = Booking::state();
                $s['seats'] = $codes;
                $s['hold_until'] = $until;
                Booking::save($s);
                redirect('/booking/combos');
            }
            flash('error', $err);
            $status = Seat::statusMap($st['id'], visitorId());
            $sum['seats'] = array_map(fn($c) => ['code' => $c, 'type' => Seat::typeOf($c, $st), 'price' => 0],
                array_filter($codes, fn($c) => in_array($status[$c] ?? '', ['available', 'mine'], true)));
        }

        render('booking/select-seat', [
            'title' => 'Chọn ghế – ' . APP_NAME, 'nav' => 'movies', 'step' => 2, 'sum' => $sum, 'status' => $status,
            'rows' => Seat::rows($st), 'hasCouple' => Seat::hasCouple($st),
            'selected' => array_column($sum['seats'], 'code'),
        ]);
    }
}
