<?php
declare(strict_types=1);

/**
 * Phòng chiếu và sơ đồ ghế của phòng.
 * Sơ đồ mẫu theo cỡ phòng (dùng khi seed); admin có thể chỉnh từng ghế trong bảng seats.
 */
final class Room
{
    /**
     * Mẫu sơ đồ: số hàng, số ghế/hàng, các hàng VIP, có hàng ghế đôi ở cuối hay không.
     * Phòng 1 lớn nhất, phòng 2 vừa, từ phòng 3 là phòng nhỏ không có ghế đôi.
     */
    public const LAYOUTS = [
        'large'  => ['label' => 'Phòng lớn', 'rows' => 'ABCDEFGHIJ', 'cols' => 14, 'vip' => 'EFGH', 'couple' => true],
        'medium' => ['label' => 'Phòng vừa', 'rows' => 'ABCDEFGHI', 'cols' => 12, 'vip' => 'EFG', 'couple' => true],
        'small'  => ['label' => 'Phòng nhỏ', 'rows' => 'ABCDEFGH', 'cols' => 10, 'vip' => 'EF', 'couple' => false],
    ];

    public static function layoutKeyFor(int $number): string
    {
        return match ($number) {
            1 => 'large',
            2 => 'medium',
            default => 'small',
        };
    }

    /**
     * Danh sách ghế theo mẫu: hàng cuối là ghế đôi (nếu có), số ghế đôi = nửa số cột.
     * @return array<int,array{code:string,row:string,col:int,type:string}>
     */
    public static function seatsForLayout(string $key): array
    {
        $l = self::LAYOUTS[$key];
        $rows = str_split($l['rows']);
        $last = end($rows);
        $out = [];
        foreach ($rows as $r) {
            $isCouple = $l['couple'] && $r === $last;
            $cols = $isCouple ? intdiv($l['cols'], 2) : $l['cols'];
            $type = $isCouple ? 'couple' : (str_contains($l['vip'], $r) ? 'vip' : 'standard');
            for ($c = 1; $c <= $cols; $c++) {
                $out[] = ['code' => $r . $c, 'row' => $r, 'col' => $c, 'type' => $type];
            }
        }
        return $out;
    }

    /** Tạo $count phòng (kèm ghế) cho một rạp. */
    public static function createForCinema(string $cinemaId, int $count): void
    {
        $insRoom = Database::pdo()->prepare('INSERT INTO rooms (cinema_id, number, name, format) VALUES (?,?,?,?)');
        $insSeat = Database::pdo()->prepare('INSERT INTO seats (room_id, code, row_label, col_no, type) VALUES (?,?,?,?,?)');
        for ($n = 1; $n <= $count; $n++) {
            $insRoom->execute([$cinemaId, $n, 'Phòng ' . $n, '2D']);
            $roomId = (int)Database::pdo()->lastInsertId();
            foreach (self::seatsForLayout(self::layoutKeyFor($n)) as $s) {
                $insSeat->execute([$roomId, $s['code'], $s['row'], $s['col'], $s['type']]);
            }
        }
    }

    /** Thêm một phòng (kèm ghế theo mẫu sơ đồ) vào rạp; trả về id phòng. */
    public static function add(string $cinemaId, string $name, string $format, string $layoutKey): int
    {
        $number = (int)Database::one('SELECT COALESCE(MAX(number), 0) + 1 n FROM rooms WHERE cinema_id = ?', [$cinemaId])['n'];
        Database::exec('INSERT INTO rooms (cinema_id, number, name, format) VALUES (?,?,?,?)', [$cinemaId, $number, $name !== '' ? $name : 'Phòng ' . $number, $format]);
        $roomId = (int)Database::pdo()->lastInsertId();
        $ins = Database::pdo()->prepare('INSERT INTO seats (room_id, code, row_label, col_no, type) VALUES (?,?,?,?,?)');
        foreach (self::seatsForLayout(isset(self::LAYOUTS[$layoutKey]) ? $layoutKey : 'small') as $s) {
            $ins->execute([$roomId, $s['code'], $s['row'], $s['col'], $s['type']]);
        }
        self::syncCount($cinemaId);
        return $roomId;
    }

    /** Cập nhật cột cinemas.rooms theo số phòng thực có. */
    public static function syncCount(string $cinemaId): void
    {
        Database::exec('UPDATE cinemas SET rooms = (SELECT COUNT(*) FROM rooms WHERE cinema_id = ?) WHERE id = ?', [$cinemaId, $cinemaId]);
    }

    public static function find(int $id): ?array
    {
        return Database::one('SELECT r.*, c.name AS cinema_name FROM rooms r JOIN cinemas c ON c.id = r.cinema_id WHERE r.id = ?', [$id]);
    }

    /** @return array[] phòng của rạp, kèm số ghế */
    public static function forCinema(string $cinemaId): array
    {
        return Database::all('SELECT r.*, COUNT(s.id) AS seat_count, SUM(s.type = \'couple\') AS couple_count, SUM(s.type = \'vip\') AS vip_count,
                SUM(s.active = 0) AS inactive_count
            FROM rooms r LEFT JOIN seats s ON s.room_id = r.id WHERE r.cinema_id = ? GROUP BY r.id ORDER BY r.number', [$cinemaId]);
    }

    /** id phòng theo rạp + số phòng. */
    public static function idFor(string $cinemaId, int $number): ?int
    {
        $r = Database::one('SELECT id FROM rooms WHERE cinema_id = ? AND number = ?', [$cinemaId, $number]);
        return $r ? (int)$r['id'] : null;
    }
}
