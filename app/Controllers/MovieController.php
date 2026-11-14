<?php
declare(strict_types=1);

final class MovieController
{
    public static function index(): void
    {
        $f = ['q' => queryStr('q'), 'genre' => queryStr('genre'), 'status' => queryStr('status'), 'sort' => queryStr('sort')];
        $all = Movie::search($f['q'], $f['genre'], $f['status'], $f['sort'] ?: 'release');
        $pages = max(1, (int)ceil(count($all) / PER_PAGE));
        $page = min($pages, max(1, (int)($_GET['page'] ?? 1)));
        render('movies/index', [
            'title' => 'Phim – ' . APP_NAME, 'nav' => 'movies', 'f' => $f,
            'movies' => array_slice($all, ($page - 1) * PER_PAGE, PER_PAGE),
            'total' => count($all), 'page' => $page, 'pages' => $pages,
        ]);
    }

    public static function show(string $id): void
    {
        $m = Movie::find($id) ?? notFound();
        $date = pickDate($_GET['date'] ?? null);
        $cinemaId = queryStr('cinema');
        $sched = $m['status'] === 'now' ? Showtime::schedule($date, ['movie' => $id]) : [];
        $options = array_map(fn($g) => $g['cinema'], $sched);
        $shown = $cinemaId !== '' ? array_intersect_key($sched, [$cinemaId => 1]) : $sched;
        render('movies/detail', [
            'title' => $m['title'] . ' – ' . APP_NAME, 'nav' => 'movies', 'm' => $m,
            'date' => $date, 'cinemaId' => $cinemaId, 'sched' => $shown, 'cinemaOptions' => $options,
            'related' => array_slice(array_values(array_filter(Movie::search('', $m['genres'][0]), fn($x) => $x['id'] !== $id)), 0, 4),
        ]);
    }
}
