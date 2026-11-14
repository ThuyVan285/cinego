<?php
declare(strict_types=1);

final class HomeController
{
    public static function index(): void
    {
        $f = ['q' => queryStr('q'), 'genre' => queryStr('genre'), 'status' => queryStr('status'), 'sort' => queryStr('sort')];
        $filtered = $f['q'] !== '' || $f['genre'] !== '' || $f['status'] !== '' || $f['sort'] !== '';
        $all = Movie::search($f['q'], $f['genre'], $f['status'], $f['sort'] ?: 'release');
        $now = array_values(array_filter($all, fn($m) => $m['status'] === 'now'));
        $soon = array_values(array_filter($all, fn($m) => $m['status'] === 'soon'));
        $top = array_values(array_filter($all, fn($m) => $m['status'] === 'now' && $m['rating'] >= 8.0));

        $date = pickDate($_GET['date'] ?? null);
        $brands = Cinema::brands();
        $brand = isset($brands[queryStr('brand')]) ? queryStr('brand') : 'cgv';
        $quick = Showtime::schedule($date, ['brand' => $brand]);
        $cinemaId = queryStr('cinema');
        if (!isset($quick[$cinemaId])) {
            $cinemaId = $quick ? (string)array_key_first($quick) : '';
        }

        render('home/index', [
            'title' => APP_NAME . ' – Đặt vé xem phim online',
            'nav' => 'home',
            'heroes' => Movie::featured(3),
            'f' => $f, 'filtered' => $filtered, 'all' => $all,
            'now' => $now, 'soon' => $soon, 'top' => $top,
            'date' => $date, 'brand' => $brand, 'brands' => $brands, 'quick' => $quick, 'cinemaId' => $cinemaId,
            'promos' => Promotion::all(),
        ]);
    }

    public static function promotions(): void
    {
        render('promotions/index', ['title' => 'Ưu đãi – ' . APP_NAME, 'nav' => 'promos', 'promos' => Promotion::all()]);
    }
}
