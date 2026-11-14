<?php
declare(strict_types=1);

namespace Admin;

use Cinema;
use Database;
use Room;
use Seat;
use Setting;

/** Quản lý phòng chiếu, giá vé mặc định. (Chỉ admin) Sơ đồ ghế xem SeatController. */
final class RoomController
{
    public const FORMATS = ['2D', '3D', 'IMAX', '4DX', 'ScreenX'];

    public static function index(): void
    {
        $cinemas = Cinema::search();
        $cinemaId = queryStr('cinema') ?: ($cinemas[0]['id'] ?? '');
        $rooms = [];
        if ($cinemaId !== '') {
            $rooms = Room::forCinema($cinemaId);
            foreach ($rooms as &$r) {
                $r['upcoming_shows'] = (int)Database::one('SELECT COUNT(*) n FROM showtimes WHERE room_id = ? AND show_date >= CURDATE()', [$r['id']])['n'];
            }
            unset($r);
        }
        Ui::render('rooms/index', 'Phòng chiếu, sơ đồ ghế & giá vé', 'rooms', [
            'cinemas' => $cinemas, 'cinemaId' => $cinemaId, 'cinema' => $cinemaId ? Cinema::find($cinemaId) : null,
            'rooms' => $rooms, 'layouts' => Room::LAYOUTS, 'prices' => Setting::prices(),
        ]);
    }

    /** Thêm phòng (id = 0) hoặc sửa tên / định dạng. */
    public static function save(): void
    {
        checkCsrf();
        $id = Ui::int('id');
        $name = Ui::str('name');
        $format = in_array(Ui::str('format'), self::FORMATS, true) ? Ui::str('format') : '2D';
        if (mb_strlen($name) > 40) {
            Ui::back('/admin/rooms', 'Tên phòng tối đa 40 ký tự.', 'error');
        }
        if ($id > 0) {
            $r = Room::find($id) ?? notFound();
            Database::exec('UPDATE rooms SET name = ?, format = ? WHERE id = ?', [$name !== '' ? $name : 'Phòng ' . $r['number'], $format, $id]);
            Ui::back('/admin/rooms?cinema=' . rawurlencode($r['cinema_id']), 'Đã cập nhật ' . ($name ?: 'Phòng ' . $r['number']) . '.');
        }
        $cinemaId = Ui::str('cinema_id');
        Cinema::find($cinemaId) ?? notFound();
        $newId = Room::add($cinemaId, $name, $format, Ui::str('layout'));
        Ui::back('/admin/rooms/' . $newId . '/seats', 'Đã thêm phòng chiếu. Bạn có thể chỉnh sơ đồ ghế bên dưới.');
    }

    /** Xoá phòng chưa từng có vé (kèm ghế và suất chiếu trống của phòng). */
    public static function delete(string $id): void
    {
        checkCsrf();
        $r = Room::find((int)$id) ?? notFound();
        $back = '/admin/rooms?cinema=' . rawurlencode($r['cinema_id']);
        $n = (int)Database::one('SELECT COUNT(*) n FROM bookings b JOIN showtimes s ON s.id = b.showtime_id WHERE s.room_id = ?', [$r['id']])['n'];
        if ($n > 0) {
            Ui::back($back, $r['name'] . ' đã có ' . $n . ' đơn đặt vé nên không xoá được.', 'error');
        }
        Database::exec('DELETE FROM showtimes WHERE room_id = ?', [$r['id']]);
        Database::exec('DELETE FROM rooms WHERE id = ?', [$r['id']]);
        Room::syncCount($r['cinema_id']);
        Ui::back($back, 'Đã xoá ' . $r['name'] . '.');
    }

    /** Giá vé mặc định; tuỳ chọn áp dụng cho các suất chiếu sắp tới chưa có vé. */
    public static function prices(): void
    {
        checkCsrf();
        $p = [];
        foreach (['standard', 'vip', 'couple'] as $t) {
            $v = Ui::int('price_' . $t);
            if ($v < 10000 || $v > 2000000 || $v % 1000 !== 0) {
                Ui::back('/admin/rooms', 'Giá vé ' . Seat::TYPE_LABEL[$t] . ' phải từ 10.000đ đến 2.000.000đ và chia hết cho 1.000.', 'error');
            }
            $p[$t] = $v;
        }
        foreach ($p as $t => $v) { // lưu sau khi cả 3 giá đều hợp lệ
            Setting::set('price_' . $t, $v);
        }
        $msg = 'Đã lưu giá vé mặc định cho suất chiếu mới.';
        if (!empty($_POST['apply_future'])) {
            $n = Database::exec("UPDATE showtimes s SET s.price_standard = ?, s.price_vip = ?, s.price_couple = ?
                WHERE CONCAT(s.show_date, ' ', s.show_time) > DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i')
                  AND NOT EXISTS (SELECT 1 FROM bookings b WHERE b.showtime_id = s.id)", [$p['standard'], $p['vip'], $p['couple']]);
            $msg .= ' Đã áp dụng cho ' . $n . ' suất chiếu sắp tới chưa có vé.';
        }
        Ui::back('/admin/rooms' . (Ui::str('cinema') !== '' ? '?cinema=' . rawurlencode(Ui::str('cinema')) : ''), $msg);
    }
}
