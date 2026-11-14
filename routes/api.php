<?php
declare(strict_types=1);

/**
 * Route API (trả JSON), cùng định dạng với routes/web.php. Tiền tố /api được thêm tự động.
 */
return [
    // Tình trạng ghế của một suất chiếu (available | mine | held | booked) – sơ đồ ghế gọi định kỳ để tự cập nhật.
    ['GET', '#^/showtimes/([\w.-]+)/seats$#', function (string $showtimeId): void {
        if (!Showtime::find($showtimeId)) {
            json(['error' => 'Không tìm thấy suất chiếu'], 404);
        }
        header('Cache-Control: no-store');
        json([
            'showtime' => $showtimeId,
            'seats' => Seat::statusMap($showtimeId, visitorId()),
            'hold_until' => SeatService::holdUntil($showtimeId, visitorId()),
            'server_time' => date('c'),
        ]);
    }],
];
