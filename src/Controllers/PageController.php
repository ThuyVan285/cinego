<?php
declare(strict_types=1);

final class PageController
{
    private static function str(string $k): string
    {
        $v = $_GET[$k] ?? '';
        return is_string($v) ? trim($v) : '';
    }

    public static function home(): void
    {
        $f = ['q' => self::str('q'), 'genre' => self::str('genre'), 'status' => self::str('status'), 'sort' => self::str('sort')];
        $filtered = $f['q'] !== '' || $f['genre'] !== '' || $f['status'] !== '' || $f['sort'] !== '';
        $all = Movie::search($f['q'], $f['genre'], $f['status'], $f['sort'] ?: 'release');
        $now = array_values(array_filter($all, fn($m) => $m['status'] === 'now'));
        $soon = array_values(array_filter($all, fn($m) => $m['status'] === 'soon'));
        $top = array_values(array_filter($all, fn($m) => $m['status'] === 'now' && $m['rating'] >= 8.0));

        $date = pickDate($_GET['date'] ?? null);
        $brands = Cinema::brands();
        $brand = isset($brands[self::str('brand')]) ? self::str('brand') : 'cgv';
        $quick = Showtime::schedule($date, ['brand' => $brand]);
        $cinemaId = self::str('cinema');
        if (!isset($quick[$cinemaId])) {
            $cinemaId = $quick ? (string)array_key_first($quick) : '';
        }

        render('home', [
            'title' => APP_NAME . ' – Đặt vé xem phim online',
            'nav' => 'home',
            'heroes' => Movie::featured(3),
            'f' => $f, 'filtered' => $filtered, 'all' => $all,
            'now' => $now, 'soon' => $soon, 'top' => $top,
            'date' => $date, 'brand' => $brand, 'brands' => $brands, 'quick' => $quick, 'cinemaId' => $cinemaId,
            'promos' => Promotion::all(),
        ]);
    }

    public static function movies(): void
    {
        $f = ['q' => self::str('q'), 'genre' => self::str('genre'), 'status' => self::str('status'), 'sort' => self::str('sort')];
        $all = Movie::search($f['q'], $f['genre'], $f['status'], $f['sort'] ?: 'release');
        $pages = max(1, (int)ceil(count($all) / PER_PAGE));
        $page = min($pages, max(1, (int)($_GET['page'] ?? 1)));
        render('movies', [
            'title' => 'Phim – ' . APP_NAME, 'nav' => 'movies', 'f' => $f,
            'movies' => array_slice($all, ($page - 1) * PER_PAGE, PER_PAGE),
            'total' => count($all), 'page' => $page, 'pages' => $pages,
        ]);
    }

    public static function movie(string $id): void
    {
        $m = Movie::find($id) ?? notFound();
        $date = pickDate($_GET['date'] ?? null);
        $cinemaId = self::str('cinema');
        $sched = $m['status'] === 'now' ? Showtime::schedule($date, ['movie' => $id]) : [];
        $options = array_map(fn($g) => $g['cinema'], $sched);
        $shown = $cinemaId !== '' ? array_intersect_key($sched, [$cinemaId => 1]) : $sched;
        render('movie', [
            'title' => $m['title'] . ' – ' . APP_NAME, 'nav' => 'movies', 'm' => $m,
            'date' => $date, 'cinemaId' => $cinemaId, 'sched' => $shown, 'cinemaOptions' => $options,
            'related' => array_slice(array_values(array_filter(Movie::search('', $m['genres'][0]), fn($x) => $x['id'] !== $id)), 0, 4),
        ]);
    }

    public static function showtimes(): void
    {
        $city = self::str('city');
        $brands = Cinema::brands();
        $f = [
            'brand' => isset($brands[self::str('brand')]) ? self::str('brand') : '',
            'city' => in_array($city, Cinema::cities(), true) ? $city : '',
            'area' => self::str('area'),
            'movie' => self::str('movie'),
        ];
        $date = pickDate($_GET['date'] ?? null);
        render('showtimes', [
            'title' => 'Lịch chiếu – ' . APP_NAME, 'nav' => 'showtimes', 'date' => $date, 'f' => $f, 'brands' => $brands,
            'cities' => Cinema::cities(), 'areas' => Cinema::areas($f['city']),
            'movieList' => Movie::search('', '', 'now'),
            'sched' => Showtime::schedule($date, $f),
        ]);
    }

    public static function cinemas(): void
    {
        $f = ['q' => self::str('q'), 'city' => self::str('city'), 'brand' => self::str('brand')];
        $list = Cinema::search($f['q'], $f['city'], $f['brand']);
        foreach ($list as &$c) {
            $c['movies'] = Showtime::moviesAtCinema($c['id']);
        }
        unset($c);
        render('cinemas', [
            'title' => 'Rạp chiếu – ' . APP_NAME, 'nav' => 'cinemas', 'f' => $f, 'list' => $list,
            'cities' => Cinema::cities(), 'brands' => Cinema::brands(),
        ]);
    }

    public static function cinema(string $id): void
    {
        $c = Cinema::find($id) ?? notFound();
        $date = pickDate($_GET['date'] ?? null);
        render('cinema', [
            'title' => $c['name'] . ' – ' . APP_NAME, 'nav' => 'cinemas', 'c' => $c, 'date' => $date,
            'sched' => Showtime::schedule($date, ['cinema' => $id]),
        ]);
    }

    public static function promotions(): void
    {
        render('promotions', ['title' => 'Ưu đãi – ' . APP_NAME, 'nav' => 'promos', 'promos' => Promotion::all()]);
    }
}
