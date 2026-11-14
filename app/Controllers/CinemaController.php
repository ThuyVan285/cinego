<?php
declare(strict_types=1);

final class CinemaController
{
    public static function index(): void
    {
        $f = ['q' => queryStr('q'), 'city' => queryStr('city'), 'brand' => queryStr('brand')];
        $list = Cinema::search($f['q'], $f['city'], $f['brand']);
        foreach ($list as &$c) {
            $c['movies'] = Showtime::moviesAtCinema($c['id']);
        }
        unset($c);
        render('cinemas/index', [
            'title' => 'Rạp chiếu – ' . APP_NAME, 'nav' => 'cinemas', 'f' => $f, 'list' => $list,
            'cities' => Cinema::cities(), 'brands' => Cinema::brands(),
        ]);
    }

    public static function show(string $id): void
    {
        $c = Cinema::find($id) ?? notFound();
        $date = pickDate($_GET['date'] ?? null);
        render('cinemas/detail', [
            'title' => $c['name'] . ' – ' . APP_NAME, 'nav' => 'cinemas', 'c' => $c, 'date' => $date,
            'rooms' => Room::forCinema($id),
            'sched' => Showtime::schedule($date, ['cinema' => $id]),
        ]);
    }
}
