<?php
declare(strict_types=1);

final class ShowtimeController
{
    public static function index(): void
    {
        $city = queryStr('city');
        $brands = Cinema::brands();
        $f = [
            'brand' => isset($brands[queryStr('brand')]) ? queryStr('brand') : '',
            'city' => in_array($city, Cinema::cities(), true) ? $city : '',
            'area' => queryStr('area'),
            'movie' => queryStr('movie'),
        ];
        $date = pickDate($_GET['date'] ?? null);
        render('showtimes/index', [
            'title' => 'Lịch chiếu – ' . APP_NAME, 'nav' => 'showtimes', 'date' => $date, 'f' => $f, 'brands' => $brands,
            'cities' => Cinema::cities(), 'areas' => Cinema::areas($f['city']),
            'movieList' => Movie::search('', '', 'now'),
            'sched' => Showtime::schedule($date, $f),
        ]);
    }
}
