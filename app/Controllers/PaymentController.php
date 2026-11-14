<?php
declare(strict_types=1);

final class PaymentController
{
    /** Bước 5 – chọn phương thức thanh toán. */
    public static function show(): void
    {
        $sum = BookingService::requireCurrent(true, true);
        $selected = $sum['pay'] && Payment::isAvailable($sum['pay']) ? $sum['pay'] : Payment::defaultMethod();
        render('booking/payment', [
            'title' => 'Thanh toán – ' . APP_NAME, 'nav' => 'movies', 'step' => 5, 'sum' => $sum,
            'methods' => Payment::methods(), 'selected' => $selected,
        ]);
    }

    /** Tạo đơn rồi chuyển sang cổng thanh toán (hoặc xác nhận ngay nếu trả tại quầy). */
    public static function pay(): void
    {
        checkCsrf();
        BookingService::requireCurrent(true, true);
        $m = (string)($_POST['method'] ?? '');
        if (!Payment::isAvailable($m)) {
            flash('error', 'Vui lòng chọn phương thức thanh toán đang hoạt động.');
            redirect('/booking/payment');
        }
        [$url, $err] = PaymentService::start($m, visitorId(), self::clientIp());
        if ($err !== null) {
            flash('error', $err);
            redirect($url ?? '/booking/seats');
        }
        redirect($url);
    }

    // ---------- Cổng thanh toán thử nghiệm ----------
    /** Trang cổng thử nghiệm: kiểm tra chữ ký yêu cầu (chống sửa số tiền) rồi cho khách chọn kết quả. */
    public static function demoGateway(string $ref): void
    {
        $params = self::params('demo', $_GET);
        $p = Payment::findByRef($ref);
        $valid = $p && $p['provider'] === 'demo' && ($params['vnp_TxnRef'] ?? '') === $ref
            && PaymentService::verify($params, PaymentService::demoSecret());
        if (!$valid) {
            http_response_code(400);
            render('errors/error', ['title' => 'Yêu cầu thanh toán không hợp lệ', 'message' => 'Liên kết thanh toán bị sai hoặc đã bị chỉnh sửa.']);
            return;
        }
        render('payment/demo-gateway', [
            'title' => 'Cổng thanh toán thử nghiệm', 'bare' => true,
            'payment' => $p, 'booking' => Booking::findByCode($p['booking_code']), 'params' => $params,
        ]);
    }

    /** Khách bấm kết quả trên cổng thử nghiệm → tạo tham số trả về đã ký, chuyển về /payment/return/demo. */
    public static function demoGatewaySubmit(string $ref): void
    {
        checkCsrf();
        $p = Payment::findByRef($ref);
        if (!$p || $p['provider'] !== 'demo') {
            notFound();
        }
        $rc = ['success' => '00', 'cancel' => '24', 'insufficient' => '51'][(string)($_POST['result'] ?? '')] ?? '24';
        $result = [
            'vnp_TmnCode' => 'CINEGODEMO',
            'vnp_Amount' => (string)((int)$p['amount'] * 100),
            'vnp_BankCode' => $rc === '00' ? 'CINEGOTEST' : '',
            'vnp_CardType' => $rc === '00' ? 'ATM' : '',
            'vnp_OrderInfo' => 'Thanh toan ve CineGo ' . $p['booking_code'],
            'vnp_PayDate' => date('YmdHis'),
            'vnp_ResponseCode' => $rc,
            'vnp_TransactionNo' => $rc === '00' ? (string)random_int(10000000, 99999999) : '0',
            'vnp_TransactionStatus' => $rc === '00' ? '00' : '02',
            'vnp_TxnRef' => $ref,
        ];
        redirect('/payment/return/demo?' . PaymentService::signedQuery($result, PaymentService::demoSecret()));
    }

    // ---------- Kết quả từ cổng ----------
    /** Cổng chuyển khách về đây sau khi thanh toán. */
    public static function result(string $provider): void
    {
        $r = PaymentService::handleResult($provider, self::params($provider, $_GET));
        if ($r['ok']) {
            Booking::reset();
            flash('ok', 'Thanh toán thành công. Vé của bạn đã được xác nhận.');
            redirect('/booking/success/' . rawurlencode((string)$r['code']));
        }
        $s = Booking::state();
        if ($r['code'] !== null && ($s['pending_code'] ?? null) === $r['code']) {
            // Thất bại: giữ lại các ghế cho khách để thử lại ngay.
            $s['pending_code'] = null;
            Booking::save($s);
            if (!empty($s['st']) && !empty($s['seats'])) {
                [, $until] = SeatService::hold($s['st'], $s['seats'], visitorId());
                $s['hold_until'] = $until;
                Booking::save($s);
            }
        }
        flash('error', 'Thanh toán không thành công: ' . rtrim($r['message'], '.') . '.');
        redirect(!empty($s['st']) ? '/booking/payment' : ($r['code'] ? '/tickets/' . rawurlencode($r['code']) : '/tickets'));
    }

    /**
     * MoMo IPN: MoMo gọi thẳng từ máy chủ của họ (POST JSON) – cần APP_URL truy cập được từ Internet.
     * Theo tài liệu MoMo, đối tác trả HTTP 204 khi đã nhận; trả lỗi để MoMo gửi lại khi xử lý thất bại.
     */
    public static function ipn(string $provider): void
    {
        $data = json_decode((string)file_get_contents('php://input'), true);
        try {
            $r = PaymentService::handleResult($provider, self::params($provider, is_array($data) ? $data : []));
            if ($r['rsp'] === '97') {
                http_response_code(400); // sai chữ ký
                exit;
            }
            http_response_code(204);
        } catch (Throwable $e) {
            error_log('MoMo IPN: ' . $e->getMessage());
            http_response_code(500);
        }
        exit;
    }

    /** Tham số dạng chuỗi của cổng: MoMo lấy mọi trường vô hướng; cổng thử nghiệm chỉ lấy vnp_*. */
    private static function params(string $provider, array $src): array
    {
        $out = [];
        foreach ($src as $k => $v) {
            if (!is_string($k) || !is_scalar($v)) {
                continue;
            }
            if ($provider === 'momo' || str_starts_with($k, 'vnp_')) {
                $out[$k] = (string)$v;
            }
        }
        return $out;
    }

    private static function clientIp(): string
    {
        $ip = (string)($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '127.0.0.1';
    }
}
