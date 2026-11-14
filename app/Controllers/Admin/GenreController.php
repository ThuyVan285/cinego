<?php
declare(strict_types=1);

namespace Admin;

use Database;
use Genre;

/** Quản lý thể loại phim. (Chỉ admin) */
final class GenreController
{
    public static function index(): void
    {
        Ui::render('genres/index', 'Thể loại phim', 'genres', ['rows' => Genre::withCounts()]);
    }

    /** Thêm mới (id = 0) hoặc đổi tên. */
    public static function save(): void
    {
        checkCsrf();
        $id = Ui::int('id');
        $name = (string)preg_replace('/\s+/u', ' ', Ui::str('name'));
        if (mb_strlen($name) < 2 || mb_strlen($name) > 40 || str_contains($name, ',')) {
            Ui::back('/admin/genres', 'Tên thể loại từ 2–40 ký tự và không chứa dấu phẩy.', 'error');
        }
        $dup = Database::one('SELECT id FROM genres WHERE name = ? AND id <> ?', [$name, $id]);
        if ($dup) {
            Ui::back('/admin/genres', 'Thể loại "' . $name . '" đã tồn tại.', 'error');
        }
        if ($id > 0) {
            $g = Genre::find($id) ?? notFound();
            Genre::rename($id, $name);
            Ui::back('/admin/genres', 'Đã đổi "' . $g['name'] . '" thành "' . $name . '" (cập nhật cả các phim đang dùng).');
        }
        Genre::create($name);
        Ui::back('/admin/genres', 'Đã thêm thể loại "' . $name . '".');
    }

    public static function delete(string $id): void
    {
        checkCsrf();
        $g = Genre::find((int)$id) ?? notFound();
        $n = (int)Database::one("SELECT COUNT(*) n FROM movies WHERE FIND_IN_SET(?, REPLACE(genres, ', ', ',')) > 0", [$g['name']])['n'];
        if ($n > 0) {
            Ui::back('/admin/genres', 'Thể loại "' . $g['name'] . '" đang được ' . $n . ' phim sử dụng – bỏ thể loại khỏi các phim đó trước khi xoá.', 'error');
        }
        Genre::delete((int)$id);
        Ui::back('/admin/genres', 'Đã xoá thể loại "' . $g['name'] . '".');
    }
}
