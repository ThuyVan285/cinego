<?php
declare(strict_types=1);

namespace Admin;

use Cinema;
use Database;
use Seat;
use Setting;
use Showtime;

/**
 * Suất chiếu: danh sách + tình trạng ghế (admin & nhân viên); thêm / sửa / xoá / tự sinh lịch (chỉ admin).
 */
final class ShowtimeController
{
    /** Thời gian dọn phòng giữa hai suất (phút). */
    public const CLEANUP_MINUTES = 15;

    public static function index(): void
    {
        $f = ['date' => validDate(queryStr('date')) ? queryStr('date') : today(), 'cinema' => queryStr('cinema'), 'movie' => queryStr('movie')];
        $where = ['s.show_date = ?'];
        $p = [$f['date']];
        if ($f['cinema'] !== '') {
            $where[] = 's.cinema_id = ?';
            $p[] = $f['cinema'];
        }
        if ($f['movie'] !== '') {
            $where[] = 's.movie_id = ?';
            $p[] = $f['movie'];
        }
        $w = implode(' AND ', $where);
        $pg = Ui::paginate((int)Database::one("SELECT COUNT(*) n FROM showtimes s WHERE $w", $p)['n'], 50);
        $rows = Database::all("SELECT s.*, m.title, m.duration, c.name AS cinema_name, r.name AS room_name,
                (SELECT COUNT(*) FROM seats x WHERE x.room_id = s.room_id AND x.active = 1) AS capacity,
                (SELECT COUNT(*) FROM booking_seats bs JOIN bookings b ON b.code = bs.booking_code WHERE bs.showtime_id = s.id AND bs.active = 1 AND b.status = 'confirmed') AS sold,
                (SELECT COUNT(*) FROM booking_seats bs JOIN bookings b ON b.code = bs.booking_code WHERE bs.showtime_id = s.id AND bs.active = 1 AND b.status = 'pending')
                  + (SELECT COUNT(*) FROM seat_holds h WHERE h.showtime_id = s.id AND h.expires_at > NOW()) AS held,
                (SELECT COUNT(*) FROM bookings b WHERE b.showtime_id = s.id) AS booking_count
            FROM showtimes s JOIN movies m ON m.id = s.movie_id JOIN cinemas c ON c.id = s.cinema_id JOIN rooms r ON r.id = s.room_id
            WHERE $w ORDER BY c.name, s.show_time, r.number LIMIT 50 OFFSET " . $pg['offset'], $p);
        Ui::render('showtimes/index', 'Suất chiếu', 'showtimes', [
            'rows' => $rows, 'f' => $f, 'pg' => $pg, 'cinemas' => Cinema::search(),
            'movies' => Database::all("SELECT id, title FROM movies WHERE status <> 'ended' ORDER BY title"),
            'isAdmin' => \User::isAdmin(),
        ]);
    }

    public static function create(): void
    {
        $p = Setting::prices();
        self::form(['id' => '', 'movie_id' => queryStr('movie'), 'cinema_id' => queryStr('cinema'), 'room_id' => 0,
            'show_date' => validDate(queryStr('date')) ? queryStr('date') : today(), 'show_time' => '19:00',
            'price_standard' => $p['standard'], 'price_vip' => $p['vip'], 'price_couple' => $p['couple']], 0);
    }

    public static function edit(string $id): void
    {
        $s = Database::one('SELECT * FROM showtimes WHERE id = ?', [$id]) ?? notFound();
        self::form($s, self::bookingCount($id));
    }

    private static function form(array $s, int $bookings, array $errors = []): void
    {
        Ui::render('showtimes/form', $s['id'] ? 'Sửa suất chiếu' : 'Thêm suất chiếu', 'showtimes', [
            's' => $s, 'bookings' => $bookings, 'errors' => $errors,
            'movies' => Database::all("SELECT id, title, duration, status FROM movies WHERE status <> 'ended' ORDER BY status = 'now' DESC, title"),
            'rooms' => Database::all('SELECT r.id, r.name, r.format, r.cinema_id, c.name AS cinema_name FROM rooms r JOIN cinemas c ON c.id = r.cinema_id ORDER BY c.name, r.number'),
        ]);
    }

    public static function save(): void
    {
        checkCsrf();
        $id = Ui::str('id');
        $old = $id !== '' ? (Database::one('SELECT * FROM showtimes WHERE id = ?', [$id]) ?? notFound()) : null;
        $bookings = $old ? self::bookingCount($id) : 0;
        $s = [
            'id' => $id,
            'movie_id' => Ui::str('movie_id'), 'room_id' => Ui::int('room_id'),
            'show_date' => Ui::str('show_date'), 'show_time' => Ui::str('show_time'),
            'price_standard' => Ui::int('price_standard'), 'price_vip' => Ui::int('price_vip'), 'price_couple' => Ui::int('price_couple'),
        ];
        if ($old && $bookings > 0) {
            // Suất đã có vé: chỉ đổi giá (áp dụng cho lượt đặt mới), không đổi phim/phòng/giờ.
            foreach (['movie_id', 'room_id', 'show_date', 'show_time'] as $k) {
                $s[$k] = $old[$k];
            }
        }
        $movie = Database::one('SELECT id, title, duration, status FROM movies WHERE id = ?', [$s['movie_id']]);
        $room = Database::one('SELECT * FROM rooms WHERE id = ?', [$s['room_id']]);
        $s['cinema_id'] = $room['cinema_id'] ?? '';
        $errors = [];
        if (!$movie || $movie['status'] === 'ended') {
            $errors['movie_id'] = 'Chọn phim đang chiếu hoặc sắp chiếu.';
        } elseif ((int)$movie['duration'] <= 0) {
            $errors['movie_id'] = 'Phim chưa có thời lượng – cập nhật phim trước khi xếp suất.';
        }
        if (!$room) {
            $errors['room_id'] = 'Chọn phòng chiếu.';
        }
        if (!validDate($s['show_date'])) {
            $errors['show_date'] = 'Ngày chiếu không hợp lệ.';
        }
        if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $s['show_time'])) {
            $errors['show_time'] = 'Giờ chiếu dạng HH:MM.';
        }
        if (!$errors && (!$old || $bookings === 0) && strtotime($s['show_date'] . ' ' . $s['show_time']) <= time()) {
            $errors['show_time'] = 'Không xếp suất chiếu trong quá khứ.';
        }
        foreach (['price_standard', 'price_vip', 'price_couple'] as $k) {
            if ($s[$k] < 10000 || $s[$k] > 2000000) {
                $errors[$k] = 'Giá từ 10.000đ đến 2.000.000đ.';
            }
        }
        if (!$errors && (!$old || $bookings === 0)) {
            $clash = self::findClash($s['room_id'], $s['show_date'], $s['show_time'], (int)$movie['duration'], $old['id'] ?? null);
            if ($clash) {
                $errors['show_time'] = 'Trùng giờ với suất ' . $clash['show_time'] . ' – ' . $clash['title'] . ' trong cùng phòng (tính cả '
                    . self::CLEANUP_MINUTES . ' phút dọn phòng).';
            }
        }
        if ($errors) {
            flash('error', 'Vui lòng kiểm tra lại các trường được đánh dấu.');
            self::form($s, $bookings, $errors);
            return;
        }

        $num = (int)$room['number'];
        if ($old) {
            Database::exec('UPDATE showtimes SET movie_id = ?, cinema_id = ?, room_id = ?, room = ?, show_date = ?, show_time = ?,
                price_standard = ?, price_vip = ?, price_couple = ? WHERE id = ?', [$s['movie_id'], $s['cinema_id'], $s['room_id'], $num,
                $s['show_date'], $s['show_time'], $s['price_standard'], $s['price_vip'], $s['price_couple'], $old['id']]);
            Ui::back(self::listUrl($s), 'Đã cập nhật suất chiếu' . ($bookings ? ' (suất đã có vé nên chỉ đổi giá cho lượt đặt mới)' : '') . '.');
        }
        $newId = $s['cinema_id'] . '.' . $s['movie_id'] . '.' . $s['show_date'] . '.' . str_replace(':', '', $s['show_time']);
        if (Database::one('SELECT 1 FROM showtimes WHERE id = ?', [$newId])) {
            $newId .= '.r' . $num; // cùng phim, cùng giờ ở phòng khác
        }
        if (Database::one('SELECT 1 FROM showtimes WHERE id = ?', [$newId])) {
            flash('error', 'Suất chiếu này đã tồn tại.');
            self::form($s, 0, ['show_time' => 'Đã có suất cùng phim, cùng giờ, cùng phòng.']);
            return;
        }
        Database::exec('INSERT INTO showtimes (id, movie_id, cinema_id, room_id, show_date, show_time, room, price_standard, price_vip, price_couple)
            VALUES (?,?,?,?,?,?,?,?,?,?)', [$newId, $s['movie_id'], $s['cinema_id'], $s['room_id'], $s['show_date'], $s['show_time'], $num,
            $s['price_standard'], $s['price_vip'], $s['price_couple']]);
        Ui::back(self::listUrl($s), 'Đã thêm suất ' . $s['show_time'] . ' – ' . $movie['title'] . '.');
    }

    public static function delete(string $id): void
    {
        checkCsrf();
        $s = Database::one('SELECT * FROM showtimes WHERE id = ?', [$id]) ?? notFound();
        $n = self::bookingCount($id);
        if ($n > 0) {
            Ui::back(self::listUrl($s), "Suất này đã có $n đơn đặt vé nên không xoá được. Huỷ từng đơn (hoàn tiền) trong mục Đơn đặt vé nếu cần dừng suất.", 'error');
        }
        Database::exec('DELETE FROM showtimes WHERE id = ?', [$id]);
        Ui::back(self::listUrl($s), 'Đã xoá suất chiếu ' . $s['show_time'] . ' ngày ' . date('d/m', strtotime($s['show_date'])) . '.');
    }

    /** Tự sinh lịch chiếu 7 ngày tới cho các phim đang chiếu (giữ nguyên suất đã có). */
    public static function generate(): void
    {
        checkCsrf();
        $before = (int)Database::one('SELECT COUNT(*) n FROM showtimes WHERE show_date >= CURDATE()')['n'];
        for ($i = 0; $i < 7; $i++) {
            Showtime::generateDay(addDays(today(), $i));
        }
        $after = (int)Database::one('SELECT COUNT(*) n FROM showtimes WHERE show_date >= CURDATE()')['n'];
        Ui::back('/admin/showtimes', 'Đã thêm ' . ($after - $before) . ' suất chiếu tự động cho 7 ngày tới.');
    }

    /** Tình trạng ghế của một suất (admin & nhân viên). */
    public static function seats(string $id): void
    {
        $st = Showtime::find($id) ?? notFound();
        $status = Seat::statusMap($id);
        $owner = [];
        foreach (Database::all('SELECT bs.seat_code, b.code, b.customer_name, b.status FROM booking_seats bs JOIN bookings b ON b.code = bs.booking_code
                WHERE bs.showtime_id = ? AND bs.active = 1', [$id]) as $r) {
            $owner[$r['seat_code']] = $r;
        }
        $bookings = Database::all('SELECT b.*, (SELECT GROUP_CONCAT(seat_code ORDER BY seat_code) FROM booking_seats WHERE booking_code = b.code) AS seat_list,
                (SELECT status FROM payments p WHERE p.booking_code = b.code ORDER BY id DESC LIMIT 1) AS pay_status
            FROM bookings b WHERE b.showtime_id = ? ORDER BY b.created_at DESC', [$id]);
        Ui::render('showtimes/seats', 'Tình trạng ghế', 'showtimes', [
            'st' => $st, 'rows' => Seat::rows($st), 'status' => $status, 'owner' => $owner, 'bookings' => $bookings,
            'counts' => array_count_values($status),
        ]);
    }

    // ---------- Hỗ trợ ----------
    private static function bookingCount(string $id): int
    {
        return (int)Database::one('SELECT COUNT(*) n FROM bookings WHERE showtime_id = ?', [$id])['n'];
    }

    /** Suất khác trong cùng phòng, cùng ngày có khung giờ (kể cả dọn phòng) chồng lên suất mới. */
    private static function findClash(int $roomId, string $date, string $time, int $duration, ?string $exceptId): ?array
    {
        $start = strtotime("$date $time");
        $end = $start + ($duration + self::CLEANUP_MINUTES) * 60;
        foreach (Database::all('SELECT s.id, s.show_time, m.title, m.duration FROM showtimes s JOIN movies m ON m.id = s.movie_id
                WHERE s.room_id = ? AND s.show_date = ?', [$roomId, $date]) as $o) {
            if ($o['id'] === $exceptId) {
                continue;
            }
            $os = strtotime($date . ' ' . $o['show_time']);
            $oe = $os + (max(1, (int)$o['duration']) + self::CLEANUP_MINUTES) * 60;
            if ($start < $oe && $os < $end) {
                return $o;
            }
        }
        return null;
    }

    private static function listUrl(array $s): string
    {
        return url('/admin/showtimes', ['date' => $s['show_date'], 'cinema' => $s['cinema_id'] ?? '']);
    }
}
