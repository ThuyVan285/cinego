<?php
declare(strict_types=1);

/**
 * Đơn đặt vé: trạng thái đang đặt (session), tính tiền thống nhất, lưu đơn và vòng đời đơn (MySQL).
 * Vòng đời: pending (chờ thanh toán online) → confirmed | cancelled | expired.
 */
final class Booking
{
    public const STATUS_LABEL = [
        'pending' => 'Chờ thanh toán',
        'confirmed' => 'Đã xác nhận',
        'cancelled' => 'Đã huỷ',
        'expired' => 'Hết hạn thanh toán',
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

    /** Bỏ đơn đang đặt và nhả các ghế đang giữ. */
    public static function reset(): void
    {
        $s = self::state();
        if (!empty($s['st'])) {
            SeatService::release($s['st'], visitorId());
        }
        unset($_SESSION['bk']);
    }

    public static function start(string $showtimeId): void
    {
        $old = self::state();
        if (($old['st'] ?? null) === $showtimeId) {
            return; // giữ lựa chọn nếu cùng suất
        }
        if (!empty($old['st'])) {
            SeatService::release($old['st'], visitorId());
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
        $types = Seat::typeMap($st);
        $seats = [];
        $ticketTotal = 0;
        foreach (array_unique($s['seats'] ?? []) as $code) {
            if (!isset($types[$code])) {
                continue;
            }
            $price = (int)$st['price_' . $types[$code]];
            $seats[] = ['code' => $code, 'type' => $types[$code], 'price' => $price];
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
            'hold_until' => $s['hold_until'] ?? null,
        ];
    }

    public static function currentSummary(): ?array
    {
        return self::summary(self::state());
    }

    // ---------- Lưu đơn ----------
    /**
     * Tạo đơn từ phiên đang đặt. Ghế được khoá ngay bằng booking_seats (active = 1).
     * @param string $status 'pending' (chờ thanh toán online) hoặc 'confirmed' (trả tại quầy)
     * @return array{0:?string,1:?string} [mã đơn, lỗi]
     */
    public static function create(string $visitorId, string $status = 'pending'): array
    {
        $s = self::state();
        $sum = self::summary($s);
        if (!$sum || !$sum['seats'] || !Showtime::isBookable($sum['showtime'])) {
            return [null, 'Phiên đặt vé không hợp lệ hoặc suất chiếu đã qua giờ.'];
        }
        $c = $sum['customer'];
        if (empty($c['name']) || empty($c['email']) || empty($c['phone']) || !Payment::isValid((string)($s['pay'] ?? ''))) {
            return [null, 'Thiếu thông tin đặt vé hoặc phương thức thanh toán.'];
        }
        $stId = $sum['showtime']['id'];
        $codes = array_column($sum['seats'], 'code');
        $err = SeatService::validateSelection($codes, Seat::statusMap($stId, $visitorId));
        if ($err !== null) {
            return [null, $err];
        }

        $expires = $status === 'pending' ? date('Y-m-d H:i:s', time() + (int)config('app.payment_timeout_minutes', 15) * 60) : null;
        $pdo = Database::pdo();
        $userId = User::id();
        for ($try = 0; $try < 5; $try++) {
            $code = 'CG-' . date('Ymd') . '-' . self::randomSuffix();
            try {
                $pdo->beginTransaction();
                // Ghế người khác đang giữ (khoá dòng để không ai chen vào giữa lúc kiểm tra và ghi).
                $in = implode(',', array_fill(0, count($codes), '?'));
                $others = Database::all("SELECT seat_code FROM seat_holds WHERE showtime_id = ? AND seat_code IN ($in)
                    AND visitor_id <> ? AND expires_at > NOW() FOR UPDATE", [$stId, ...$codes, $visitorId]);
                if ($others) {
                    $pdo->rollBack();
                    return [null, 'Ghế ' . $others[0]['seat_code'] . ' đang được người khác giữ. Vui lòng chọn lại ghế.'];
                }
                Database::exec('INSERT INTO bookings (code, visitor_id, user_id, showtime_id, customer_name, email, phone, payment_method, promo_code,
                    ticket_total, combo_total, discount, total, status, expires_at, created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())', [
                    $code, $visitorId, $userId, $stId, $c['name'], $c['email'], $c['phone'], $s['pay'], $sum['promo']['code'] ?? null,
                    $sum['ticket_total'], $sum['combo_total'], $sum['discount'], $sum['total'], $status, $expires,
                ]);
                foreach ($sum['seats'] as $seat) {
                    Database::exec('INSERT INTO booking_seats (booking_code, showtime_id, seat_code, seat_type, price, active) VALUES (?,?,?,?,?,1)',
                        [$code, $stId, $seat['code'], $seat['type'], $seat['price']]);
                }
                foreach ($sum['combos'] as $cb) {
                    Database::exec('INSERT INTO booking_combos (booking_code, combo_id, name, qty, price) VALUES (?,?,?,?,?)',
                        [$code, $cb['id'], $cb['name'], $cb['qty'], $cb['price']]);
                }
                Database::exec("DELETE FROM seat_holds WHERE showtime_id = ? AND visitor_id = ? AND seat_code IN ($in)", [$stId, $visitorId, ...$codes]);
                $pdo->commit();
                return [$code, null];
            } catch (PDOException $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                if (str_contains($e->getMessage(), 'uq_active_seat')) {
                    return [null, 'Một trong các ghế vừa có người đặt. Vui lòng chọn lại ghế.'];
                }
                if (($e->errorInfo[1] ?? 0) !== 1062) {
                    throw $e; // lỗi khác mã trùng
                }
                // trùng mã đơn → thử lại với mã khác
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

    // ---------- Vòng đời đơn ----------
    /** Đơn chờ thanh toán → đã xác nhận. Trả về false nếu ghế đã bị người khác lấy (đơn từng hết hạn). */
    public static function confirm(string $code): bool
    {
        $b = Database::one('SELECT status FROM bookings WHERE code = ?', [$code]);
        if (!$b) {
            return false;
        }
        if ($b['status'] === 'confirmed') {
            return true;
        }
        try {
            // Đơn đã hết hạn nhưng tiền vẫn về: lấy lại ghế nếu còn trống.
            Database::exec('UPDATE booking_seats SET active = 1 WHERE booking_code = ?', [$code]);
        } catch (PDOException $e) {
            return false;
        }
        Database::exec("UPDATE bookings SET status = 'confirmed', expires_at = NULL, cancelled_at = NULL WHERE code = ?", [$code]);
        return true;
    }

    /** Huỷ / hết hạn đơn và trả ghế về trống (lịch sử ghế vẫn được giữ). */
    public static function release(string $code, string $status = 'cancelled'): void
    {
        Database::exec('UPDATE booking_seats SET active = NULL WHERE booking_code = ?', [$code]);
        Database::exec('UPDATE bookings SET status = ?, expires_at = NULL, cancelled_at = NOW() WHERE code = ?', [$status, $code]);
    }

    /** Đơn quá hạn thanh toán → expired, trả ghế; dọn ghế giữ tạm đã hết hạn. Chạy tối đa một lần mỗi request. */
    public static function expireStale(): void
    {
        static $done = false;
        if ($done) {
            return;
        }
        $done = true;
        $stale = Database::all("SELECT code FROM bookings WHERE status = 'pending' AND expires_at < NOW()");
        foreach ($stale as $r) {
            // Giao dịch vẫn để 'pending': nếu cổng báo thành công muộn, PaymentService sẽ lấy lại ghế hoặc hoàn tiền.
            Database::exec("UPDATE payments SET message = 'Hết thời gian thanh toán' WHERE booking_code = ? AND status = 'pending'", [$r['code']]);
            self::release($r['code'], 'expired');
        }
        Database::exec('DELETE FROM seat_holds WHERE expires_at <= NOW()');
    }

    /** Khách có được huỷ vé này không: vé đã xác nhận, còn đủ thời gian trước giờ chiếu. */
    public static function canCancel(array $b): bool
    {
        if ($b['status'] !== 'confirmed' || !$b['showtime']) {
            return false;
        }
        $start = strtotime($b['showtime']['show_date'] . ' ' . $b['showtime']['show_time']);
        return $start - time() >= (int)config('app.cancel_before_minutes', 120) * 60;
    }

    // ---------- Truy vấn đơn ----------
    public static function find(string $code, string $visitorId, ?int $userId = null): ?array
    {
        self::expireStale();
        $userId ??= User::id();
        if ($userId !== null) {
            $b = Database::one('SELECT * FROM bookings WHERE code = ? AND (visitor_id = ? OR user_id = ?)', [$code, $visitorId, $userId]);
        } else {
            $b = Database::one('SELECT * FROM bookings WHERE code = ? AND visitor_id = ?', [$code, $visitorId]);
        }
        return $b ? self::hydrate($b) : null;
    }

    /** Tìm đơn theo mã, không kiểm tra chủ đơn (dùng cho cổng thanh toán gọi về). */
    public static function findByCode(string $code): ?array
    {
        $b = Database::one('SELECT * FROM bookings WHERE code = ?', [$code]);
        return $b ? self::hydrate($b) : null;
    }

    public static function forVisitor(string $visitorId, ?int $userId = null): array
    {
        self::expireStale();
        $userId ??= User::id();
        if ($userId !== null) {
            $rows = Database::all('SELECT * FROM bookings WHERE visitor_id = ? OR user_id = ? ORDER BY created_at DESC, code DESC', [$visitorId, $userId]);
        } else {
            $rows = Database::all('SELECT * FROM bookings WHERE visitor_id = ? ORDER BY created_at DESC, code DESC', [$visitorId]);
        }
        return array_map([self::class, 'hydrate'], $rows);
    }

    private static function hydrate(array $b): array
    {
        $b['showtime'] = Showtime::find($b['showtime_id']);
        $b['seats'] = Database::all('SELECT seat_code AS code, seat_type AS type, price FROM booking_seats WHERE booking_code = ?', [$b['code']]);
        usort($b['seats'], fn($x, $y) => strnatcmp($x['code'], $y['code']));
        $b['combos'] = Database::all('SELECT combo_id AS id, name, qty, price FROM booking_combos WHERE booking_code = ?', [$b['code']]);
        $b['payments'] = Payment::forBooking($b['code']);
        $b['payment'] = $b['payments'][0] ?? null; // giao dịch mới nhất
        // Trạng thái thanh toán để hiển thị: đơn đã huỷ/hết hạn mà cổng chưa trả kết quả → "Không hoàn tất".
        $b['pay_status'] = $b['payment']['status'] ?? null;
        $b['pay_label'] = $b['pay_status'] ? (Payment::STATUS_LABEL[$b['pay_status']] ?? $b['pay_status']) : '';
        if ($b['pay_status'] === 'pending' && in_array($b['status'], ['cancelled', 'expired'], true)) {
            $b['pay_status'] = 'failed';
            $b['pay_label'] = 'Không hoàn tất';
        }
        $b['upcoming'] = $b['showtime'] && !Showtime::isPast($b['showtime']);
        $b['status_label'] = self::STATUS_LABEL[$b['status']] ?? $b['status'];
        $b['can_cancel'] = self::canCancel($b);
        return $b;
    }

    /** Xoá lịch sử demo của người dùng và trả ghế về trạng thái trống. */
    public static function clearAll(string $visitorId, ?int $userId = null): void
    {
        $userId ??= User::id();
        if ($userId !== null) {
            Database::exec('DELETE FROM bookings WHERE visitor_id = ? OR user_id = ?', [$visitorId, $userId]);
        } else {
            Database::exec('DELETE FROM bookings WHERE visitor_id = ?', [$visitorId]);
        }
    }
}
