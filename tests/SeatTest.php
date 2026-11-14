<?php
declare(strict_types=1);

// Sơ đồ ghế mẫu theo cỡ phòng
$large = Room::seatsForLayout('large');
$small = Room::seatsForLayout('small');
$types = fn(array $seats) => array_count_values(array_column($seats, 'type'));
same(133, count($large), 'Phòng lớn: 9 hàng × 14 ghế + 7 ghế đôi = 133 ghế');
same(7, $types($large)['couple'] ?? 0, 'Phòng lớn có 7 ghế đôi ở hàng cuối');
same(56, $types($large)['vip'] ?? 0, 'Phòng lớn có 4 hàng VIP (56 ghế)');
check(!isset($types($small)['couple']), 'Phòng nhỏ không có ghế đôi');
same('large', Room::layoutKeyFor(1), 'Phòng số 1 dùng sơ đồ lớn');
same('small', Room::layoutKeyFor(4), 'Phòng số 4 dùng sơ đồ nhỏ');

// Ghế theo phòng của một suất chiếu thật trong CSDL
$st = Showtime::find((string)Database::one("SELECT id FROM showtimes WHERE show_date > CURDATE() ORDER BY id LIMIT 1")['id']);
check($st !== null, 'Có suất chiếu ngày mai để test');
$map = Seat::typeMap($st);
same(count(Seat::forRoom((int)$st['room_id'])), count($map), 'Sơ đồ ghế của suất = ghế của phòng chiếu');
check(Seat::exists('A1', $st), 'A1 có trong phòng');
check(!Seat::exists('Z99', $st), 'Z99 không có trong phòng');
same((int)$st['price_vip'], Seat::price('E1', $st), 'Giá ghế VIP lấy theo suất chiếu');

// SeatService::validateSelection
$status = ['A1' => 'available', 'A2' => 'mine', 'A3' => 'booked', 'A4' => 'held'];
same(null, SeatService::validateSelection(['A1', 'A2'], $status), 'Ghế trống và ghế mình đang giữ thì hợp lệ');
check(SeatService::validateSelection([], $status) !== null, 'Không chọn ghế nào thì báo lỗi');
check(str_contains((string)SeatService::validateSelection(['A3'], $status), 'đã có người đặt'), 'Ghế đã bán thì báo lỗi');
check(str_contains((string)SeatService::validateSelection(['A4'], $status), 'đang được người khác giữ'), 'Ghế người khác giữ thì báo lỗi');
check(SeatService::validateSelection(['Z9'], $status) !== null, 'Ghế không có trong phòng thì báo lỗi');
$many = array_map(fn($i) => "B$i", range(1, MAX_SEATS + 1));
check(SeatService::validateSelection($many, array_fill_keys($many, 'available')) !== null, 'Vượt ' . MAX_SEATS . ' ghế thì báo lỗi');

// Giữ ghế: hai khách không giữ được cùng một ghế
$alice = str_repeat('a', 32);
$bob = str_repeat('b', 32);
$id = $st['id'];
SeatService::release($id, $alice);
SeatService::release($id, $bob);
[$e1, $until] = SeatService::hold($id, ['C1', 'C2'], $alice);
same(null, $e1, 'Khách A giữ được C1, C2');
check($until !== null && strtotime($until) > time(), 'Có thời hạn giữ ghế trong tương lai');
same('mine', Seat::statusMap($id, $alice)['C1'], 'Với khách A, C1 là ghế của mình');
same('held', Seat::statusMap($id, $bob)['C1'], 'Với khách B, C1 đang được giữ');
[$e2] = SeatService::hold($id, ['C2', 'C3'], $bob);
check($e2 !== null, 'Khách B không giữ được C2 khi A đang giữ');
same('available', Seat::statusMap($id, $bob)['C3'], 'Giữ thất bại thì không giữ ghế nào (C3 vẫn trống)');
SeatService::hold($id, ['C1'], $alice);
same('available', Seat::statusMap($id, $bob)['C2'], 'A đổi lựa chọn thì C2 được nhả');
Database::exec('UPDATE seat_holds SET expires_at = NOW() - INTERVAL 1 MINUTE WHERE showtime_id = ? AND visitor_id = ?', [$id, $alice]);
same('available', Seat::statusMap($id, $bob)['C1'], 'Hết hạn giữ thì ghế trống lại');
SeatService::release($id, $alice);
SeatService::release($id, $bob);
