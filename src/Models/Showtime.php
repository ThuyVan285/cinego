<?php
declare(strict_types=1);

final class Showtime
{
    private const SELECT = 'SELECT s.id, s.movie_id, s.cinema_id, s.show_date, s.show_time, s.room,
        s.price_standard, s.price_vip, s.price_couple,
        m.title AS movie_title, m.age AS movie_age, m.duration AS movie_duration, m.palette AS movie_palette,
        c.name AS cinema_name, c.address AS cinema_address, c.city AS cinema_city, c.brand AS cinema_brand,
        c.area AS cinema_area
        FROM showtimes s JOIN movies m ON m.id = s.movie_id JOIN cinemas c ON c.id = s.cinema_id';

    public static function find(string $id): ?array
    {
        return Database::one(self::SELECT . ' WHERE s.id = ?', [$id]);
    }

    public static function isPast(array $s): bool
    {
        return strtotime($s['show_date'] . ' ' . $s['show_time']) <= time();
    }

    /** Suất chiếu có thể đặt: chưa qua giờ. */
    public static function isBookable(array $s): bool
    {
        return !self::isPast($s);
    }

    /**
     * Lịch chiếu theo ngày, nhóm theo rạp → phim → giờ.
     * @return array<string,array{cinema:array,movies:array<string,array{movie:array,times:array[]}>}>
     */
    public static function schedule(string $date, array $f = []): array
    {
        $where = ['s.show_date = ?'];
        $p = [$date];
        foreach (['brand' => 'c.brand', 'city' => 'c.city', 'area' => 'c.area', 'movie' => 's.movie_id', 'cinema' => 's.cinema_id'] as $k => $col) {
            if (!empty($f[$k])) {
                $where[] = "$col = ?";
                $p[] = $f[$k];
            }
        }
        $sql = self::SELECT . ' WHERE ' . implode(' AND ', $where) . ' ORDER BY c.city, c.name, m.title, s.show_time';
        $out = [];
        foreach (Database::all($sql, $p) as $r) {
            $cid = $r['cinema_id'];
            $out[$cid] ??= ['cinema' => Cinema::find($cid), 'movies' => []];
            $out[$cid]['movies'][$r['movie_id']] ??= [
                'movie' => ['id' => $r['movie_id'], 'title' => $r['movie_title'], 'age' => $r['movie_age'], 'duration' => (int)$r['movie_duration']],
                'times' => [],
            ];
            $r['past'] = self::isPast($r);
            $out[$cid]['movies'][$r['movie_id']]['times'][] = $r;
        }
        return $out;
    }

    /** Số suất chiếu của phim trong 7 ngày tới (để biết phim có lịch hay không). */
    public static function countForMovie(string $movieId): int
    {
        $r = Database::one('SELECT COUNT(*) n FROM showtimes WHERE movie_id = ? AND show_date >= ?', [$movieId, today()]);
        return (int)($r['n'] ?? 0);
    }

    /** Phim đang có suất ở một rạp (7 ngày tới). */
    public static function moviesAtCinema(string $cinemaId): array
    {
        return Database::all('SELECT DISTINCT m.id, m.title, m.age FROM showtimes s JOIN movies m ON m.id = s.movie_id
            WHERE s.cinema_id = ? AND s.show_date >= ? ORDER BY m.title', [$cinemaId, today()]);
    }

    // ---------- Sinh lịch chiếu demo ----------
    private const SLOTS = ['09:30', '11:45', '14:00', '16:15', '18:30', '19:45', '21:00', '22:15'];

    /** Đảm bảo có lịch chiếu cho 7 ngày tới (tạo bổ sung khi sang ngày mới). */
    public static function ensureRange(): void
    {
        $cnt = Database::all('SELECT show_date, COUNT(*) n FROM showtimes WHERE show_date >= ? GROUP BY show_date', [today()]);
        $have = array_column($cnt, 'n', 'show_date');
        for ($i = 0; $i < 7; $i++) {
            $d = addDays(today(), $i);
            if (empty($have[$d])) {
                self::generateDay($d);
            }
        }
    }

    public static function generateDay(string $date): void
    {
        $movies = Database::all("SELECT id FROM movies WHERE status = 'now' AND release_date <= ?", [$date]);
        $pdo = Database::pdo();
        $st = $pdo->prepare('INSERT IGNORE INTO showtimes (id, movie_id, cinema_id, show_date, show_time, room, price_standard, price_vip, price_couple) VALUES (?,?,?,?,?,?,?,?,?)');
        $pdo->beginTransaction();
        foreach (Database::all('SELECT id, rooms FROM cinemas') as $c) {
            // Một vài ngày rạp tạm ngưng chiếu (để minh hoạ trạng thái "không có suất").
            if (crc32('off|' . $c['id'] . '|' . $date) % 13 === 0 && $date !== today()) {
                continue;
            }
            foreach ($movies as $m) {
                if (crc32('mv|' . $c['id'] . '|' . $m['id']) % 100 >= 62) {
                    continue;
                }
                $picked = 0;
                foreach (self::SLOTS as $i => $time) {
                    if (crc32("sl|{$c['id']}|{$m['id']}|$date|$i") % 100 < 38) {
                        $picked++;
                        self::insert($st, $c, $m['id'], $date, $time);
                    }
                }
                if ($picked === 0) {
                    $time = self::SLOTS[crc32("fb|{$c['id']}|{$m['id']}|$date") % count(self::SLOTS)];
                    self::insert($st, $c, $m['id'], $date, $time);
                }
            }
        }
        $pdo->commit();
    }

    private static function insert(PDOStatement $st, array $c, string $movieId, string $date, string $time): void
    {
        $id = $c['id'] . '.' . $movieId . '.' . $date . '.' . str_replace(':', '', $time);
        $room = 1 + crc32('rm|' . $id) % (int)$c['rooms'];
        $st->execute([$id, $movieId, $c['id'], $date, $time, $room, 70000, 90000, 170000]);
    }
}
