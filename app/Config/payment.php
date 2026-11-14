<?php
declare(strict_types=1);

/**
 * Phương thức thanh toán.
 * - momo: ví MoMo (API v2, môi trường test hoặc production) – chỉ bật khi đã điền MOMO_PARTNER_CODE,
 *         MOMO_ACCESS_KEY, MOMO_SECRET_KEY trong .env. Khoá môi trường test lấy tại https://developers.momo.vn
 * - demo: cổng thanh toán thử nghiệm của CineGo – cùng quy trình chuyển hướng/ký số/xác thực, không thu tiền.
 * - counter: giữ vé, thanh toán khi đến quầy.
 */
return [
    'methods' => [
        'momo'    => 'Ví MoMo',
        'demo'    => 'Cổng thanh toán thử nghiệm (không thu tiền)',
        'counter' => 'Thanh toán tại quầy',
    ],
    'descriptions' => [
        'momo'    => 'Chuyển sang MoMo để quét QR hoặc mở ứng dụng MoMo thanh toán, vé được xác nhận ngay khi giao dịch thành công.',
        'demo'    => 'Mô phỏng cổng thanh toán để chạy thử toàn bộ quy trình: bạn tự chọn kết quả thành công hoặc thất bại.',
        'counter' => 'Vé được giữ, bạn thanh toán tại quầy vé trước giờ chiếu ít nhất 30 phút.',
    ],
    'default' => 'demo',

    'momo' => [
        'partner_code' => env('MOMO_PARTNER_CODE'),
        'access_key'   => env('MOMO_ACCESS_KEY'),
        'secret_key'   => env('MOMO_SECRET_KEY'),
        'endpoint'     => env('MOMO_ENDPOINT', 'https://test-payment.momo.vn'), // production: https://payment.momo.vn
        'request_type' => env('MOMO_REQUEST_TYPE', 'captureWallet'),           // captureWallet: QR / app MoMo
    ],
];
