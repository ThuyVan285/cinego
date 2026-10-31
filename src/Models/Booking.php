<?php
declare(strict_types=1);

/**
 * Nghiệp vụ đặt vé demo: trạng thái đang đặt (session), tính tiền thống nhất và lưu vé (MySQL).
 */
final class Booking
{
    public const PAYMENTS = [
        'wallet' => 'Ví điện tử',
        'transfer' => 'Chuyển khoản',
        'counter' => 'Thanh toán tại quầy',
    ];

    // ---------- Trạng thái trong session ----------
    public static function state(): array
    {
        return $_SESSION['bk'] ?? [];
    }

    public static function save(array $s): void
    {
        $_SESSION['bk'] = $s;
    }

    public static function reset(): void
    {
        unset($_SESSION['bk']);
    }

    public static function start(string $showtimeId): void
    {
        $old = self::state();
        if (($old['st'] ?? null) === $showtimeId) {
            return; // giữ lựa chọn nếu cùng suất
        }
        self::save(['st' => $showtimeId, 'seats' => [], 'combos' => [], 'customer' => [], 'promo' => null, 'pay' => null]);
    }

    // ---------- Tổng hợp & tính tiền (nguồn duy nhất) ----------
    /**
     * @return array|null null nếu chưa có suất chiếu hợp lệ
     */
    public static function summary(array $s): ?array
    {
        if (empty($s['st']) || !($st = Showtime::find($s['st']))) {
            return null;
        }
        $seats = [];
        $ticketTotal = 0;
        foreach (array_unique($s['seats'] ?? []) as $code) {
            if (!Seats::isValid($code)) {
                continue;
            }
            $price = Seats::price($code, $st);
            $seats[] = ['code' => $code, 'type' => Seats::typeOf($code), 'price' => $price];
            $ticketTotal += $price;
        }
        usort($seats, fn($a, $b) => strnatcmp($a['code'], $b['code']));

        $comboMap = Combo::map();
        $combos = [];
        $comboTotal = 0;
        foreach ($s['combos'] ?? [] as $id => $qty) {
            $qty = (int)$qty;
            if ($qty < 1 || !isset($comboMap[$id])) {
                continue;
            }
            $qty = min($qty, 10);
            $combos[] = ['id' => $id, 'name' => $comboMap[$id]['name'], 'qty' => $qty, 'price' => (int)$comboMap[$id]['price']];
            $comboTotal += $qty * (int)$comboMap[$id]['price'];
        }

        $discount = 0;
        $promoError = null;
        $promo = null;
        if (!empty($s['promo'])) {
            $promo = Promotion::find($s['promo']);
            [$discount, $promoError] = Promotion::evaluate($promo, $ticketTotal, $comboTotal);
            if ($promoError !== null) {
                $promo = null;
            }
        }
        return [
            'showtime' => $st,
            'seats' => $seats,
            'combos' => $combos,
            'ticket_total' => $ticketTotal,
            'combo_total' => $comboTotal,
            'discount' => $discount,
            'promo' => $promo,
            'promo_error' => $promoError,
            'total' => $ticketTotal + $comboTotal - $discount,
            'customer' => $s['customer'] ?? [],
            'pay' => $s['pay'] ?? null,
        ];
    }

    public static function currentSummary(): ?array
    {
        return self::summary(self::state());
    }

    // ---------- Lưu vé ----------
    /**
     * @return array{0:?string,1:?string} [mã vé, lỗi]
     */
    public static function create(string $visitorId): array
    {
        $s = self::state();
        $sum = self::summary($s);
        if (!$sum || !$sum['seats'] || !Showtime::isBookable($sum['showtime'])) {
            return [null, 'Phiên đặt vé không hợp lệ hoặc suất chiếu đã qua giờ.'];
        }
        $c = $sum['customer'];
        if (empty($c['name']) || empty($c['email']) || empty($c['phone']) || !isset(self::PAYMENTS[$s['pay'] ?? ''])) {
            return [null, 'Thiếu thông tin đặt vé hoặc phương thức thanh toán.'];
        }
        $stId = $sum['showtime']['id'];
        $status = Seats::statusMap($stId);
        foreach ($sum['seats'] as $seat) {
            if (($status[$seat['code']] ?? 'booked') !== 'available') {
                return [null, 'Ghế ' . $seat['code'] . ' vừa không còn khả dụng. Vui lòng chọn lại ghế.'];
            }
        }

        $pdo = Database::pdo();
        for ($try = 0; $try < 5; $try++) {
            $code = 'CG-' . date('Ymd') . '-' . self::randomSuffix();
            try {
                $pdo->beginTransaction();
                Database::exec('INSERT INTO bookings (code, visitor_id, showtime_id, customer_name, email, phone, payment_method, promo_code,
                    ticket_total, combo_total, discount, total, created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,NOW())', [
                    $code, $visitorId, $stId, $c['name'], $c['email'], $c['phone'], $s['pay'], $sum['promo']['code'] ?? null,
                    $sum['ticket_total'], $sum['combo_total'], $sum['discount'], $sum['total'],
                ]);
                foreach ($sum['seats'] as $seat) {
                    Database::exec('INSERT INTO booking_seats (showtime_id, seat_code, seat_type, price, booking_code) VALUES (?,?,?,?,?)',
                        [$stId, $seat['code'], $seat['type'], $seat['price'], $code]);
                }
                foreach ($sum['combos'] as $cb) {
                    Database::exec('INSERT INTO booking_combos (booking_code, combo_id, name, qty, price) VALUES (?,?,?,?,?)',
                        [$code, $cb['id'], $cb['name'], $cb['qty'], $cb['price']]);
                }
                $pdo->commit();
                self::reset();
                return [$code, null];
            } catch (PDOException $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                if (str_contains($e->getMessage(), 'booking_seats')) {
                    return [null, 'Một trong các ghế vừa có người đặt. Vui lòng chọn lại ghế.'];
                }
                if (($e->errorInfo[1] ?? 0) !== 1062) {
                    throw $e; // lỗi khác mã trùng
                }
                // trùng mã vé → thử lại với mã khác
            }
        }
        return [null, 'Không thể tạo mã đặt vé, vui lòng thử lại.'];
    }

    private static function randomSuffix(): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $out = '';
        for ($i = 0; $i < 5; $i++) {
            $out .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $out;
    }

    // ---------- Truy vấn vé ----------
    public static function find(string $code, string $visitorId): ?array
    {
        $b = Database::one('SELECT * FROM bookings WHERE code = ? AND visitor_id = ?', [$code, $visitorId]);
        return $b ? self::hydrate($b) : null;
    }

    public static function forVisitor(string $visitorId): array
    {
        $rows = Database::all('SELECT * FROM bookings WHERE visitor_id = ? ORDER BY created_at DESC, code DESC', [$visitorId]);
        return array_map([self::class, 'hydrate'], $rows);
    }

    private static function hydrate(array $b): array
    {
        $b['showtime'] = Showtime::find($b['showtime_id']);
        $b['seats'] = Database::all('SELECT seat_code AS code, seat_type AS type, price FROM booking_seats WHERE booking_code = ?', [$b['code']]);
        usort($b['seats'], fn($x, $y) => strnatcmp($x['code'], $y['code']));
        $b['combos'] = Database::all('SELECT combo_id AS id, name, qty, price FROM booking_combos WHERE booking_code = ?', [$b['code']]);
        $b['upcoming'] = $b['showtime'] && !Showtime::isPast($b['showtime']);
        return $b;
    }

    /** Xoá lịch sử demo của người dùng và trả ghế về trạng thái trống. */
    public static function clearAll(string $visitorId): void
    {
        Database::exec('DELETE FROM bookings WHERE visitor_id = ?', [$visitorId]);
    }
}
