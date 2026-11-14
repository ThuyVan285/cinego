<?php
declare(strict_types=1);

namespace Admin;

use Cinema;
use StatisticsService as Stats;

/** /admin – tổng quan, thống kê và biểu đồ doanh thu. (Admin & nhân viên) */
final class DashboardController
{
    public const PERIODS = [7 => '7 ngày', 30 => '30 ngày', 90 => '90 ngày', 365 => '12 tháng'];

    public static function index(): void
    {
        $days = (int)queryStr('days');
        $days = isset(self::PERIODS[$days]) ? $days : 30;
        $cinemas = Cinema::search();
        $cinemaId = queryStr('cinema');
        $cinemaId = in_array($cinemaId, array_column($cinemas, 'id'), true) ? $cinemaId : null;
        $to = today();
        $from = addDays($to, -($days - 1));

        $byDay = Stats::revenueByDay($from, $to, $cinemaId);
        $prevFrom = addDays($from, -$days);
        $prev = Stats::overview($prevFrom, addDays($from, -1), $cinemaId);
        Ui::render('dashboard', 'Tổng quan & thống kê', 'dashboard', [
            'days' => $days, 'from' => $from, 'to' => $to, 'cinemas' => $cinemas, 'cinemaId' => $cinemaId,
            'stats' => Stats::overview($from, $to, $cinemaId), 'prev' => $prev,
            'byDay' => array_map(fn($r) => $r['revenue'], $byDay),
            'byMonth' => Stats::revenueByMonth(12, $cinemaId),
            'byCinema' => Stats::revenueByCinema($from, $to),
            'topMovies' => Stats::topMovies($from, $to, $cinemaId),
            'transactions' => Stats::transactions($from, $to, $cinemaId),
            'latest' => Stats::latestBookings(),
        ]);
    }
}
