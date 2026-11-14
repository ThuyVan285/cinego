<?php
declare(strict_types=1);

/**
 * Dữ liệu bán vé MẪU cho trang thống kê: suất chiếu + đơn đặt vé + giao dịch trong N ngày đã qua.
 * Mọi đơn mẫu có visitor_id = 'dddd…d' (32 chữ d) và email @demo.cinego.vn để tách khỏi dữ liệu thật.
 *
 * Chạy:   php database/seeders/DemoSalesSeeder.php           (mặc định 60 ngày)
 *         php database/seeders/DemoSalesSeeder.php 90        (90 ngày)
 * Xoá:    php database/seeders/DemoSalesSeeder.php --clear   (xoá đơn mẫu + suất chiếu quá khứ không có đơn)
 */
require __DIR__ . '/../../app/bootstrap.php';

const DEMO_VISITOR = 'dddddddddddddddddddddddddddddddd';

$pdo = Database::pdo();
if (in_array('--clear', $argv, true)) {
    $n = Database::exec('DELETE FROM bookings WHERE visitor_id = ?', [DEMO_VISITOR]);
    $s = Database::exec('DELETE s FROM showtimes s LEFT JOIN bookings b ON b.showtime_id = s.id WHERE s.show_date < CURDATE() AND b.code IS NULL');
    echo "Đã xoá $n đơn mẫu và $s suất chiếu quá khứ không có đơn.\n";
    exit(0);
}
if ((int)Database::one('SELECT COUNT(*) n FROM bookings WHERE visitor_id = ?', [DEMO_VISITOR])['n'] > 0) {
    echo "Đã có dữ liệu mẫu. Chạy với --clear trước nếu muốn tạo lại.\n";
    exit(0);
}

$days = max(7, min(365, (int)($argv[1] ?? 60)));
mt_srand(20261114); // cố định để mỗi lần tạo ra cùng một bộ số liệu
$first = ['Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Huỳnh', 'Phan', 'Võ', 'Đặng', 'Bùi', 'Đỗ', 'Ngô'];
$mid = ['Văn', 'Thị', 'Minh', 'Ngọc', 'Gia', 'Thanh', 'Quốc', 'Bảo', 'Hữu', 'Thu'];
$last = ['An', 'Bình', 'Châu', 'Dũng', 'Giang', 'Hà', 'Khoa', 'Linh', 'Mai', 'Nam', 'Phúc', 'Quân', 'Tâm', 'Trang', 'Vy', 'Yến'];
$combos = Database::all('SELECT id, name, price FROM combos');
$roomSeats = [];

$insB = $pdo->prepare('INSERT INTO bookings (code, visitor_id, user_id, showtime_id, customer_name, email, phone, payment_method, promo_code,
    ticket_total, combo_total, discount, total, status, expires_at, cancelled_at, created_at) VALUES (?,?,NULL,?,?,?,?,?,NULL,?,?,0,?,?,NULL,?,?)');
$insS = $pdo->prepare('INSERT INTO booking_seats (booking_code, showtime_id, seat_code, seat_type, price, active) VALUES (?,?,?,?,?,?)');
$insC = $pdo->prepare('INSERT INTO booking_combos (booking_code, combo_id, name, qty, price) VALUES (?,?,?,?,?)');
$insP = $pdo->prepare('INSERT INTO payments (booking_code, provider, txn_ref, amount, status, bank_code, provider_txn_no, response_code, message, created_at, paid_at)
    VALUES (?,?,?,?,?,?,?,?,?,?,?)');

$total = 0;
$seq = 0;
for ($i = $days; $i >= 1; $i--) {
    $date = addDays(today(), -$i);
    Showtime::generateDay($date);
    $shows = Database::all('SELECT * FROM showtimes WHERE show_date = ?', [$date]);
    if (!$shows) {
        continue;
    }
    $weekend = in_array((int)date('N', strtotime($date)), [5, 6, 7], true);
    $orders = mt_rand(18, 34) + ($weekend ? mt_rand(10, 20) : 0);
    $used = [];
    $pdo->beginTransaction();
    for ($k = 0; $k < $orders; $k++) {
        $st = $shows[mt_rand(0, count($shows) - 1)];
        $seats = $roomSeats[$st['room_id']] ??= Database::all('SELECT code, type FROM seats WHERE room_id = ? AND active = 1', [$st['room_id']]);
        $r = mt_rand(1, 100);
        $qty = $r <= 15 ? 1 : ($r <= 70 ? 2 : ($r <= 85 ? 3 : 4));
        $pick = [];
        for ($t = 0; $t < 40 && count($pick) < $qty; $t++) {
            $s = $seats[mt_rand(0, count($seats) - 1)];
            if (!isset($used[$st['id']][$s['code']]) && !isset($pick[$s['code']])) {
                $pick[$s['code']] = $s['type'];
            }
        }
        if (!$pick) {
            continue;
        }
        $ticket = 0;
        foreach ($pick as $type) {
            $ticket += (int)$st['price_' . $type];
        }
        $comboTotal = 0;
        $chosen = [];
        if ($combos && mt_rand(1, 100) <= 45) {
            $c = $combos[mt_rand(0, count($combos) - 1)];
            $chosen[] = $c;
            $comboTotal = (int)$c['price'];
        }
        $amount = $ticket + $comboTotal;
        $showAt = strtotime($st['show_date'] . ' ' . $st['show_time']);
        $created = date('Y-m-d H:i:s', $showAt - mt_rand(1800, 3 * 86400));

        // Kết quả: 80% thanh toán online thành công, 12% trả tại quầy (đã thu), 4% thất bại, 4% huỷ & hoàn tiền.
        $r = mt_rand(1, 100);
        [$method, $status, $payStatus] = match (true) {
            $r <= 52 => ['demo', 'confirmed', 'success'],
            $r <= 80 => ['momo', 'confirmed', 'success'],
            $r <= 92 => ['counter', 'confirmed', 'success'],
            $r <= 96 => [mt_rand(0, 1) ? 'demo' : 'momo', 'cancelled', 'failed'],
            default => ['demo', 'cancelled', 'refunded'],
        };
        $active = $status === 'confirmed' ? 1 : null;
        $code = 'CG-' . date('Ymd', strtotime($created)) . '-D' . strtoupper(base_convert((string)(++$seq + 1000), 10, 36));
        $name = $first[mt_rand(0, 11)] . ' ' . $mid[mt_rand(0, 9)] . ' ' . $last[mt_rand(0, 15)];
        $insB->execute([$code, DEMO_VISITOR, $st['id'], $name, 'khach' . $seq . '@demo.cinego.vn', '09' . mt_rand(10000000, 99999999), $method,
            $ticket, $comboTotal, $amount, $status, $status === 'cancelled' ? $created : null, $created]);
        foreach ($pick as $sc => $type) {
            $insS->execute([$code, $st['id'], $sc, $type, (int)$st['price_' . $type], $active]);
            if ($active) {
                $used[$st['id']][$sc] = true;
            }
        }
        foreach ($chosen as $c) {
            $insC->execute([$code, $c['id'], $c['name'], 1, (int)$c['price']]);
        }
        $paidAt = in_array($payStatus, ['success', 'refunded'], true)
            ? ($method === 'counter' ? date('Y-m-d H:i:s', $showAt - mt_rand(900, 3600)) : date('Y-m-d H:i:s', strtotime($created) + mt_rand(30, 400)))
            : null;
        $insP->execute([$code, $method, str_replace('-', '', $code) . mt_rand(10, 99), $amount, $payStatus,
            $payStatus === 'failed' ? null : ($method === 'counter' ? null : 'MOMO'), $payStatus === 'failed' ? null : (string)mt_rand(10000000, 99999999),
            $payStatus === 'failed' ? '24' : '00', $payStatus === 'failed' ? 'Khách hàng huỷ giao dịch' : ($payStatus === 'refunded' ? 'Đã hoàn tiền (khách huỷ vé)' : null),
            $created, $paidAt]);
        $total++;
    }
    $pdo->commit();
}
echo "Đã tạo $total đơn đặt vé mẫu trong $days ngày qua.\n";
