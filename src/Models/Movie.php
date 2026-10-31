<?php
declare(strict_types=1);

final class Movie
{
    private static function hydrate(array $r): array
    {
        return [
            'id' => $r['id'],
            'title' => $r['title'],
            'genres' => array_map('trim', explode(',', $r['genres'])),
            'duration' => (int)$r['duration'],
            'age' => $r['age'],
            'rating' => (float)$r['rating'],
            'status' => $r['status'],
            'release' => $r['release_date'],
            'director' => $r['director'],
            'cast' => array_map('trim', explode(',', $r['cast_list'])),
            'synopsis' => $r['synopsis'],
            'palette' => explode(',', $r['palette']),
            'trailer' => self::validUrl($r['trailer_url']),
            'featured' => (bool)$r['featured'],
        ];
    }

    private static function validUrl(?string $u): ?string
    {
        if ($u && filter_var($u, FILTER_VALIDATE_URL) && preg_match('#^https?://#i', $u)) {
            return $u;
        }
        return null;
    }

    public static function find(string $id): ?array
    {
        $r = Database::one('SELECT * FROM movies WHERE id = ?', [$id]);
        return $r ? self::hydrate($r) : null;
    }

    /** @return array[] */
    public static function search(string $q = '', string $genre = '', string $status = '', string $sort = 'release'): array
    {
        $where = [];
        $p = [];
        if ($q !== '') {
            $where[] = 'title LIKE ?';
            $p[] = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $q) . '%';
        }
        if ($genre !== '' && in_array($genre, genres(), true)) {
            $where[] = 'FIND_IN_SET(?, REPLACE(genres, \', \', \',\')) > 0';
            $p[] = $genre;
        }
        if ($status === 'now' || $status === 'soon') {
            $where[] = 'status = ?';
            $p[] = $status;
        }
        $order = match ($sort) {
            'name_asc' => 'title ASC',
            'name_desc' => 'title DESC',
            'rating_desc' => 'rating DESC, title ASC',
            'rating_asc' => 'rating ASC, title ASC',
            default => "status = 'soon' ASC, release_date DESC",
        };
        $sql = 'SELECT * FROM movies' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY ' . $order;
        return array_map([self::class, 'hydrate'], Database::all($sql, $p));
    }

    public static function featured(int $n = 3): array
    {
        $rows = Database::all('SELECT * FROM movies WHERE featured = 1 AND status = \'now\' ORDER BY rating DESC LIMIT ' . $n);
        return array_map([self::class, 'hydrate'], $rows);
    }
}
