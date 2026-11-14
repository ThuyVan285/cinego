<?php
declare(strict_types=1);

/**
 * Thể loại phim (bảng genres, quản lý ở /admin/genres).
 * Phim lưu thể loại dạng chuỗi "Hành động, Hài" trong movies.genres; đổi tên thể loại sẽ cập nhật cả chuỗi này.
 */
final class Genre
{
    /** Danh sách ban đầu (dùng để seed và khi CSDL cũ chưa có bảng genres). */
    public const DEFAULTS = ['Hành động', 'Kinh dị', 'Hoạt hình', 'Tình cảm', 'Hài', 'Khoa học viễn tưởng', 'Tâm lý', 'Trinh thám', 'Gia đình', 'Phiêu lưu',
        'Hồi hộp', 'Thần thoại', 'Bí ẩn'];

    private static ?array $cache = null;

    /** @return string[] tên thể loại theo thứ tự hiển thị */
    public static function all(): array
    {
        if (self::$cache === null) {
            try {
                self::$cache = array_column(Database::all('SELECT name FROM genres ORDER BY sort_order, name'), 'name');
            } catch (PDOException) {
                self::$cache = self::DEFAULTS;
            }
        }
        return self::$cache;
    }

    public static function exists(string $name): bool
    {
        return in_array($name, self::all(), true);
    }

    /** @return array[] id, name, sort_order, movie_count */
    public static function withCounts(): array
    {
        $rows = Database::all('SELECT * FROM genres ORDER BY sort_order, name');
        foreach ($rows as &$r) {
            $r['movie_count'] = (int)Database::one("SELECT COUNT(*) n FROM movies WHERE FIND_IN_SET(?, REPLACE(genres, ', ', ',')) > 0", [$r['name']])['n'];
        }
        return $rows;
    }

    public static function find(int $id): ?array
    {
        return Database::one('SELECT * FROM genres WHERE id = ?', [$id]);
    }

    public static function create(string $name): void
    {
        $max = (int)Database::one('SELECT COALESCE(MAX(sort_order), 0) n FROM genres')['n'];
        Database::exec('INSERT INTO genres (name, sort_order) VALUES (?, ?)', [$name, $max + 1]);
        self::$cache = null;
    }

    /** Đổi tên thể loại và cập nhật chuỗi thể loại của mọi phim đang dùng tên cũ. */
    public static function rename(int $id, string $new): void
    {
        $g = self::find($id);
        if (!$g || $g['name'] === $new) {
            return;
        }
        Database::exec('UPDATE genres SET name = ? WHERE id = ?', [$new, $id]);
        foreach (Database::all("SELECT id, genres FROM movies WHERE FIND_IN_SET(?, REPLACE(genres, ', ', ',')) > 0", [$g['name']]) as $m) {
            $list = array_map(fn($x) => trim($x) === $g['name'] ? $new : trim($x), explode(',', $m['genres']));
            Database::exec('UPDATE movies SET genres = ? WHERE id = ?', [implode(', ', $list), $m['id']]);
        }
        self::$cache = null;
    }

    public static function delete(int $id): void
    {
        Database::exec('DELETE FROM genres WHERE id = ?', [$id]);
        self::$cache = null;
    }
}
