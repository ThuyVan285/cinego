<?php
declare(strict_types=1);

namespace Admin;

use Database;
use Room;
use Seat;

/**
 * Sơ đồ ghế của phòng chiếu: bật/tắt ghế (hỏng, bảo trì), đổi loại ghế theo hàng, thêm hàng, bỏ hàng cuối. (Chỉ admin)
 * Tình trạng ghế theo từng suất chiếu: xem ShowtimeController::seats().
 */
final class SeatController
{
    public static function edit(string $roomId): void
    {
        $r = Room::find((int)$roomId) ?? notFound();
        $rows = [];
        foreach (Database::all('SELECT * FROM seats WHERE room_id = ? ORDER BY row_label, col_no', [$r['id']]) as $s) {
            $rows[$s['row_label']][] = $s;
        }
        Ui::render('seats/edit', 'Sơ đồ ghế – ' . $r['name'] . ' · ' . $r['cinema_name'], 'rooms', ['room' => $r, 'rows' => $rows]);
    }

    public static function update(string $roomId): void
    {
        checkCsrf();
        $r = Room::find((int)$roomId) ?? notFound();
        $back = '/admin/rooms/' . $r['id'] . '/seats';
        $action = Ui::str('action');
        $types = array_keys(Seat::TYPE_LABEL);

        if ($action === 'toggle') {
            $s = Database::one('SELECT * FROM seats WHERE room_id = ? AND code = ?', [$r['id'], Ui::str('code')]) ?? notFound();
            Database::exec('UPDATE seats SET active = 1 - active WHERE id = ?', [$s['id']]);
            $n = $s['active'] ? self::futureBookings($r['id'], [$s['code']]) : 0;
            Ui::back($back, 'Ghế ' . $s['code'] . ($s['active'] ? ' đã tạm ngưng' : ' đã mở lại') . '.'
                . ($n ? " Lưu ý: ghế đang có $n vé ở suất sắp tới – các vé này vẫn giữ nguyên." : ''));
        }

        if ($action === 'row_type') {
            $row = Ui::str('row');
            $type = Ui::str('type');
            if (!in_array($type, $types, true)) {
                Ui::back($back, 'Loại ghế không hợp lệ.', 'error');
            }
            $n = Database::exec('UPDATE seats SET type = ? WHERE room_id = ? AND row_label = ?', [$type, $r['id'], $row]);
            Ui::back($back, "Hàng $row: $n ghế chuyển thành " . Seat::TYPE_LABEL[$type] . '. Giá vé theo loại ghế áp dụng cho các lượt đặt mới.');
        }

        if ($action === 'add_row') {
            $type = in_array(Ui::str('type'), $types, true) ? Ui::str('type') : 'standard';
            $count = max(1, min(20, Ui::int('count', 10)));
            $last = Database::one('SELECT MAX(row_label) l FROM seats WHERE room_id = ?', [$r['id']])['l'] ?? null;
            $row = $last ? chr(ord($last) + 1) : 'A';
            if ($row > 'Z') {
                Ui::back($back, 'Phòng đã đủ 26 hàng (A–Z).', 'error');
            }
            $ins = Database::pdo()->prepare('INSERT INTO seats (room_id, code, row_label, col_no, type) VALUES (?,?,?,?,?)');
            for ($c = 1; $c <= $count; $c++) {
                $ins->execute([$r['id'], $row . $c, $row, $c, $type]);
            }
            Ui::back($back, "Đã thêm hàng $row với $count ghế " . Seat::TYPE_LABEL[$type] . '.');
        }

        if ($action === 'remove_row') {
            $last = Database::one('SELECT MAX(row_label) l FROM seats WHERE room_id = ?', [$r['id']])['l'] ?? null;
            if (!$last) {
                Ui::back($back, 'Phòng chưa có ghế.', 'error');
            }
            $codes = array_column(Database::all('SELECT code FROM seats WHERE room_id = ? AND row_label = ?', [$r['id'], $last]), 'code');
            $n = self::futureBookings($r['id'], $codes);
            if ($n > 0) {
                Ui::back($back, "Hàng $last đang có $n vé ở suất chiếu sắp tới nên không bỏ được. Hãy tạm ngưng từng ghế thay vì xoá.", 'error');
            }
            Database::exec('DELETE FROM seats WHERE room_id = ? AND row_label = ?', [$r['id'], $last]);
            Ui::back($back, "Đã bỏ hàng $last (" . count($codes) . ' ghế).');
        }
        Ui::back($back, 'Thao tác không hợp lệ.', 'error');
    }

    /** Số vé còn hiệu lực ở các suất chưa chiếu của phòng có dùng các ghế này. */
    private static function futureBookings(int $roomId, array $codes): int
    {
        if (!$codes) {
            return 0;
        }
        $in = implode(',', array_fill(0, count($codes), '?'));
        return (int)Database::one("SELECT COUNT(*) n FROM booking_seats bs JOIN showtimes s ON s.id = bs.showtime_id
            WHERE s.room_id = ? AND bs.active = 1 AND bs.seat_code IN ($in)
              AND CONCAT(s.show_date, ' ', s.show_time) > DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i')", [$roomId, ...$codes])['n'];
    }
}
