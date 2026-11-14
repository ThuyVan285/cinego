<?php
declare(strict_types=1);

// Cấu hình chung của ứng dụng. Có thể ghi đè bằng file .env.
return [
    'name'      => env('APP_NAME', 'CineGo'),
    'url'       => rtrim(env('APP_URL', 'http://localhost:8000'), '/'),
    'key'       => env('APP_KEY', ''),   // khoá bí mật để ký mã QR và cổng thanh toán thử nghiệm
    'debug'     => env('APP_DEBUG', 'false') === 'true',
    'timezone'  => env('APP_TIMEZONE', 'Asia/Ho_Chi_Minh'),
    'max_seats' => 8,   // số ghế tối đa mỗi lượt đặt
    'per_page'  => 8,   // số phim mỗi trang ở /movies

    'seat_hold_minutes'       => 10,  // giữ ghế khi khách đang chọn bắp nước / điền thông tin
    'payment_timeout_minutes' => 15,  // thời gian chờ thanh toán online trước khi đơn hết hạn
    'cancel_before_minutes'   => 120, // được huỷ vé nếu còn ít nhất ngần này phút trước giờ chiếu
];
