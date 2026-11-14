<?php
declare(strict_types=1);

namespace Admin;

use Cinema;
use Database;
use Room;

/** Quản lý hệ thống rạp. (Chỉ admin) */
final class CinemaController
{
    public static function index(): void
    {
        $rows = Database::all("SELECT c.*, b.name AS brand_name, b.color,
                (SELECT COUNT(*) FROM rooms r WHERE r.cinema_id = c.id) AS room_count,
                (SELECT COUNT(*) FROM showtimes s WHERE s.cinema_id = c.id AND s.show_date >= CURDATE()) AS upcoming_shows,
                (SELECT COUNT(*) FROM bookings bk JOIN showtimes s ON s.id = bk.showtime_id WHERE s.cinema_id = c.id AND bk.status = 'confirmed') AS bookings
            FROM cinemas c JOIN brands b ON b.id = c.brand ORDER BY c.city, c.name");
        Ui::render('cinemas/index', 'Rạp chiếu', 'cinemas', ['rows' => $rows]);
    }

    public static function create(): void
    {
        self::form(['id' => '', 'brand' => '', 'name' => '', 'city' => 'Đà Nẵng', 'area' => '', 'address' => ''], true);
    }

    public static function edit(string $id): void
    {
        self::form(Cinema::find($id) ?? notFound(), false);
    }

    private static function form(array $c, bool $isNew, array $errors = []): void
    {
        Ui::render('cinemas/form', $isNew ? 'Thêm rạp' : 'Sửa rạp', 'cinemas', [
            'c' => $c, 'isNew' => $isNew, 'errors' => $errors, 'brands' => Cinema::brands(), 'layouts' => Room::LAYOUTS,
        ]);
    }

    public static function save(): void
    {
        checkCsrf();
        $isNew = Ui::str('is_new') === '1';
        $c = [
            'id' => $isNew ? self::slug(Ui::str('id') !== '' ? Ui::str('id') : Ui::str('name')) : Ui::str('id'),
            'brand' => Ui::str('brand'), 'name' => Ui::str('name'), 'city' => Ui::str('city'),
            'area' => Ui::str('area'), 'address' => Ui::str('address'),
        ];
        $old = $isNew ? null : (Cinema::find($c['id']) ?? notFound());
        $errors = [];
        if (!isset(Cinema::brands()[$c['brand']])) {
            $errors['brand'] = 'Chọn chuỗi rạp.';
        }
        foreach (['name' => [3, 100], 'city' => [2, 40], 'area' => [2, 40], 'address' => [5, 160]] as $k => [$min, $max]) {
            if (mb_strlen($c[$k]) < $min || mb_strlen($c[$k]) > $max) {
                $errors[$k] = "Từ $min đến $max ký tự.";
            }
        }
        if ($isNew && (strlen($c['id']) < 3 || Cinema::find($c['id']))) {
            $errors['id'] = 'Mã rạp phải có ít nhất 3 ký tự và chưa được dùng.';
        }
        $roomCount = max(0, min(15, Ui::int('room_count', 3)));
        if ($errors) {
            flash('error', 'Vui lòng kiểm tra lại các trường được đánh dấu.');
            self::form($c, $isNew, $errors);
            return;
        }
        if ($old) {
            Database::exec('UPDATE cinemas SET brand = ?, name = ?, city = ?, area = ?, address = ? WHERE id = ?',
                [$c['brand'], $c['name'], $c['city'], $c['area'], $c['address'], $c['id']]);
            Ui::back('/admin/cinemas', 'Đã cập nhật rạp "' . $c['name'] . '".');
        }
        Database::exec('INSERT INTO cinemas (id, brand, name, city, area, address, rooms) VALUES (?,?,?,?,?,?,0)',
            [$c['id'], $c['brand'], $c['name'], $c['city'], $c['area'], $c['address']]);
        $layout = Ui::str('layout');
        for ($i = 1; $i <= $roomCount; $i++) {
            Room::add($c['id'], 'Phòng ' . $i, '2D', isset(Room::LAYOUTS[$layout]) ? $layout : Room::layoutKeyFor($i));
        }
        Ui::back('/admin/rooms?cinema=' . rawurlencode($c['id']), 'Đã thêm rạp "' . $c['name'] . '" với ' . $roomCount . ' phòng chiếu.');
    }

    /** Xoá rạp chưa có vé (kèm phòng, ghế, suất chiếu trống). */
    public static function delete(string $id): void
    {
        checkCsrf();
        $c = Cinema::find($id) ?? notFound();
        $n = (int)Database::one('SELECT COUNT(*) n FROM bookings b JOIN showtimes s ON s.id = b.showtime_id WHERE s.cinema_id = ?', [$id])['n'];
        if ($n > 0) {
            Ui::back('/admin/cinemas', 'Rạp "' . $c['name'] . '" đã có ' . $n . ' đơn đặt vé nên không xoá được (cần giữ lịch sử giao dịch).', 'error');
        }
        Database::exec('DELETE FROM showtimes WHERE cinema_id = ?', [$id]);
        Database::exec('DELETE FROM cinemas WHERE id = ?', [$id]); // rooms, seats xoá theo ON DELETE CASCADE
        Ui::back('/admin/cinemas', 'Đã xoá rạp "' . $c['name'] . '".');
    }

    /** "CGV Hải Châu" → "cgv-hai-chau" (tối đa 24 ký tự, khớp cột cinemas.id). */
    private static function slug(string $s): string
    {
        $map = [
            'a' => 'àáạảãâầấậẩẫăằắặẳẵ', 'e' => 'èéẹẻẽêềếệểễ', 'i' => 'ìíịỉĩ', 'o' => 'òóọỏõôồốộổỗơờớợởỡ',
            'u' => 'ùúụủũưừứựửữ', 'y' => 'ỳýỵỷỹ', 'd' => 'đ',
        ];
        $s = mb_strtolower($s);
        foreach ($map as $ascii => $chars) {
            $s = str_replace(mb_str_split($chars), $ascii, $s);
        }
        $s = trim((string)preg_replace('/[^a-z0-9]+/', '-', $s), '-');
        return substr($s, 0, 24);
    }
}
