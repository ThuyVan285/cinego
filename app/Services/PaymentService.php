<?php
declare(strict_types=1);

/**
 * Thanh toán đơn đặt vé.
 *
 * Quy trình online (MoMo hoặc cổng thử nghiệm):
 *   1. start(): tạo đơn 'pending' (ghế bị khoá) + giao dịch 'pending', trả về URL cổng thanh toán
 *      (MoMo: gọi API tạo giao dịch lấy payUrl; cổng thử nghiệm: URL nội bộ có ký HMAC-SHA512).
 *   2. Cổng chuyển khách về /payment/return/{provider} kèm tham số đã ký.
 *   3. handleResult(): kiểm tra chữ ký, số tiền → xác nhận đơn hoặc huỷ đơn và trả ghế.
 *   MoMo còn gọi IPN (server → server, POST JSON) vào /payment/ipn/momo; xử lý idempotent.
 */
final class PaymentService
{
    /** Mã phản hồi của cổng thử nghiệm (theo quy ước mã kiểu VNPay) → thông báo cho khách. */
    public const RESPONSE_MESSAGES = [
        '00' => 'Giao dịch thành công',
        '07' => 'Giao dịch bị nghi ngờ gian lận',
        '09' => 'Thẻ/Tài khoản chưa đăng ký Internet Banking',
        '10' => 'Xác thực thông tin thẻ/tài khoản sai quá 3 lần',
        '11' => 'Hết hạn chờ thanh toán',
        '12' => 'Thẻ/Tài khoản bị khoá',
        '13' => 'Nhập sai mật khẩu xác thực giao dịch (OTP)',
        '24' => 'Khách hàng huỷ giao dịch',
        '51' => 'Tài khoản không đủ số dư',
        '65' => 'Tài khoản vượt quá hạn mức giao dịch trong ngày',
        '75' => 'Ngân hàng thanh toán đang bảo trì',
        '79' => 'Nhập sai mật khẩu thanh toán quá số lần quy định',
        '99' => 'Lỗi không xác định',
    ];

    /**
     * Bắt đầu thanh toán cho đơn đang đặt.
     * @return array{0:?string,1:?string} [URL chuyển hướng, lỗi]
     */
    public static function start(string $method, string $visitorId, string $clientIp): array
    {
        if (!Payment::isAvailable($method)) {
            return [null, 'Phương thức thanh toán này chưa được kích hoạt.'];
        }
        $s = Booking::state();
        // Khách thanh toán lại sau khi lần trước bỏ dở: huỷ đơn chờ cũ để trả ghế rồi tạo đơn mới.
        if (!empty($s['pending_code'])) {
            $old = Booking::findByCode($s['pending_code']);
            if ($old && $old['status'] === 'pending') {
                // Giao dịch cũ giữ 'pending': nếu khách vẫn trả ở tab cũ, kết quả về sau sẽ được xử lý (lấy lại ghế hoặc hoàn tiền).
                Database::exec("UPDATE payments SET message = 'Khách đã tạo giao dịch mới' WHERE booking_code = ? AND status = 'pending'", [$old['code']]);
                Booking::release($old['code'], 'cancelled');
                SeatService::hold($old['showtime_id'], array_column($old['seats'], 'code'), $visitorId);
            }
        }
        $s['pay'] = $method;
        $s['pending_code'] = null;
        Booking::save($s);

        $online = Payment::isOnline($method);
        [$code, $err] = Booking::create($visitorId, $online ? 'pending' : 'confirmed');
        if ($err !== null) {
            return [null, $err];
        }
        $b = Booking::findByCode($code);
        if (!$online) {
            Payment::create($code, 'counter', (int)$b['total'], 'unpaid');
            Booking::reset();
            return ['/booking/success/' . rawurlencode($code), null];
        }

        $ref = Payment::create($code, $method, (int)$b['total']);
        $s = Booking::state();
        $s['pending_code'] = $code;
        Booking::save($s);
        if ($method === 'momo') {
            [$payUrl, $err] = MomoService::create($ref, (int)$b['total'], 'Thanh toan ve CineGo ' . $code);
            if ($err !== null) {
                // Không tạo được giao dịch MoMo: huỷ đơn chờ, giữ lại ghế để khách chọn cách thanh toán khác.
                Payment::update((int)Payment::findByRef($ref)['id'], ['status' => 'failed', 'message' => $err]);
                Booking::release($code, 'cancelled');
                SeatService::hold($b['showtime_id'], array_column($b['seats'], 'code'), $visitorId);
                $s['pending_code'] = null;
                Booking::save($s);
                return ['/booking/payment', $err]; // quay lại chọn phương thức, ghế vẫn được giữ
            }
            return [$payUrl, null];
        }
        $params = self::requestParams($method, $ref, (int)$b['total'], 'Thanh toan ve CineGo ' . $code, $clientIp, $b['expires_at']);
        return ['/payment/demo/' . rawurlencode($ref) . '?' . self::signedQuery($params, self::demoSecret()), null];
    }

    /** Tham số yêu cầu gửi cổng thử nghiệm (định dạng tham số vnp_* kiểu VNPay 2.1.0). */
    public static function requestParams(string $provider, string $ref, int $amount, string $info, string $ip, ?string $expiresAt): array
    {
        return [
            'vnp_Version' => '2.1.0',
            'vnp_Command' => 'pay',
            'vnp_Amount' => (string)($amount * 100), // đơn vị 1/100 đồng
            'vnp_CurrCode' => 'VND',
            'vnp_TxnRef' => $ref,
            'vnp_OrderInfo' => $info,
            'vnp_OrderType' => 'other',
            'vnp_Locale' => 'vn',
            'vnp_ReturnUrl' => config('app.url') . '/payment/return/' . $provider,
            'vnp_IpAddr' => $ip,
            'vnp_CreateDate' => date('YmdHis'),
            'vnp_ExpireDate' => date('YmdHis', $expiresAt ? strtotime($expiresAt) : time() + 900),
        ];
    }

    // ---------- Ký số / xác thực của cổng thử nghiệm (HMAC-SHA512) ----------
    /** Chuỗi dữ liệu để ký: mọi tham số vnp_* (trừ chữ ký) sắp xếp theo tên, urlencode từng khoá và giá trị. */
    public static function hashData(array $params): string
    {
        unset($params['vnp_SecureHash'], $params['vnp_SecureHashType']);
        $params = array_filter($params, fn($v, $k) => str_starts_with((string)$k, 'vnp_') && $v !== null, ARRAY_FILTER_USE_BOTH);
        ksort($params);
        $parts = [];
        foreach ($params as $k => $v) {
            $parts[] = urlencode((string)$k) . '=' . urlencode((string)$v);
        }
        return implode('&', $parts);
    }

    public static function sign(array $params, string $secret): string
    {
        return hash_hmac('sha512', self::hashData($params), $secret);
    }

    /** Query string kèm chữ ký vnp_SecureHash. */
    public static function signedQuery(array $params, string $secret): string
    {
        return self::hashData($params) . '&vnp_SecureHash=' . self::sign($params, $secret);
    }

    public static function verify(array $params, string $secret): bool
    {
        $given = (string)($params['vnp_SecureHash'] ?? '');
        return $given !== '' && $secret !== '' && hash_equals(self::sign($params, $secret), strtolower($given));
    }

    /** Khoá ký cho cổng thử nghiệm: APP_KEY trong .env (dự phòng: suy ra từ cấu hình máy, chỉ dùng khi chưa đặt APP_KEY). */
    public static function demoSecret(): string
    {
        $key = (string)config('app.key', '');
        return $key !== '' ? $key : hash('sha256', 'cinego-demo|' . BASE_PATH . '|' . (string)config('database.db_name'));
    }

    // ---------- Xử lý kết quả ----------
    /**
     * Xử lý tham số cổng thanh toán gửi về (return URL hoặc IPN). Idempotent: gọi lại nhiều lần vẫn an toàn.
     * @return array{ok:bool, code:?string, message:string, rsp:string} rsp: 00 xử lý xong, 01 không thấy GD, 02 đã xử lý, 04 sai tiền, 97 sai chữ ký
     */
    public static function handleResult(string $provider, array $params): array
    {
        $r = self::normalize($provider, $params);
        if ($r === null) {
            return ['ok' => false, 'code' => null, 'message' => 'Chữ ký giao dịch không hợp lệ.', 'rsp' => '97'];
        }
        $pdo = Database::pdo();
        $pdo->beginTransaction();
        try {
            $p = Payment::findByRef($r['ref'], true);
            if (!$p || $p['provider'] !== $provider) {
                $pdo->commit();
                return ['ok' => false, 'code' => null, 'message' => 'Không tìm thấy giao dịch.', 'rsp' => '01'];
            }
            if ($r['amount'] !== (int)$p['amount']) {
                $pdo->commit();
                return ['ok' => false, 'code' => $p['booking_code'], 'message' => 'Số tiền thanh toán không khớp với đơn hàng.', 'rsp' => '04'];
            }
            if ($p['status'] !== 'pending') {
                // Đã xử lý trước đó (vd. IPN đến trước return URL).
                $pdo->commit();
                $ok = $p['status'] === 'success';
                return ['ok' => $ok, 'code' => $p['booking_code'], 'message' => $p['message'] ?: ($ok ? 'Giao dịch thành công' : 'Giao dịch không thành công'), 'rsp' => '02'];
            }

            $success = $r['success'];
            $msg = $r['message'];
            $fields = [
                'response_code' => $r['rc'],
                'bank_code' => $r['bank'],
                'provider_txn_no' => $r['txn_no'],
                'raw_response' => json_encode($params, JSON_UNESCAPED_UNICODE),
                'message' => $msg,
            ];
            if ($success) {
                $fields['status'] = 'success';
                $fields['paid_at'] = date('Y-m-d H:i:s');
                Payment::update((int)$p['id'], $fields);
                if (!Booking::confirm($p['booking_code'])) {
                    // Tiền đã về nhưng ghế đã thuộc người khác (đơn từng hết hạn) → huỷ đơn, chờ hoàn tiền.
                    Booking::release($p['booking_code'], 'cancelled');
                    Payment::update((int)$p['id'], ['status' => 'refund_pending', 'message' => 'Ghế đã được người khác đặt sau khi hết hạn giữ chỗ – đang hoàn tiền']);
                    $pdo->commit();
                    return ['ok' => false, 'code' => $p['booking_code'], 'message' => 'Thanh toán thành công nhưng ghế đã hết hạn giữ chỗ và được người khác đặt. Đơn đã được huỷ, tiền sẽ được hoàn lại.', 'rsp' => '00'];
                }
            } else {
                $fields['status'] = 'failed';
                Payment::update((int)$p['id'], $fields);
                $b = Database::one('SELECT status FROM bookings WHERE code = ?', [$p['booking_code']]);
                if ($b && $b['status'] === 'pending') {
                    Booking::release($p['booking_code'], 'cancelled');
                }
            }
            $pdo->commit();
            return ['ok' => $success, 'code' => $p['booking_code'], 'message' => $msg, 'rsp' => '00'];
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Chuẩn hoá kết quả của từng cổng về một dạng chung; null nếu sai chữ ký.
     * @return ?array{ref:string, amount:int, success:bool, rc:string, message:string, txn_no:?string, bank:?string}
     */
    private static function normalize(string $provider, array $p): ?array
    {
        if ($provider === 'momo') {
            if (!MomoService::verifyResult($p)) {
                return null;
            }
            $rc = (string)($p['resultCode'] ?? '99');
            return [
                'ref' => (string)($p['orderId'] ?? ''), 'amount' => (int)($p['amount'] ?? -1),
                'success' => $rc === '0' || $rc === '9000', 'rc' => $rc, 'message' => MomoService::message($rc),
                'txn_no' => isset($p['transId']) && (string)$p['transId'] !== '' ? (string)$p['transId'] : null,
                'bank' => 'MOMO' . (isset($p['payType']) && $p['payType'] !== '' ? '-' . strtoupper((string)$p['payType']) : ''),
            ];
        }
        if (!self::verify($p, self::demoSecret())) {
            return null;
        }
        $rc = (string)($p['vnp_ResponseCode'] ?? '99');
        return [
            'ref' => (string)($p['vnp_TxnRef'] ?? ''), 'amount' => intdiv((int)($p['vnp_Amount'] ?? -100), 100),
            'success' => $rc === '00' && ($p['vnp_TransactionStatus'] ?? '00') === '00', 'rc' => $rc,
            'message' => self::RESPONSE_MESSAGES[$rc] ?? ('Giao dịch không thành công (mã ' . $rc . ')'),
            'txn_no' => $p['vnp_TransactionNo'] ?? null, 'bank' => ($p['vnp_BankCode'] ?? '') !== '' ? $p['vnp_BankCode'] : null,
        ];
    }

    /**
     * Hoàn tiền khi khách huỷ vé. Cổng thử nghiệm: hoàn ngay. MoMo: gọi API hoàn tiền; nếu MoMo chưa hoàn được
     * thì ghi nhận 'refund_pending' để bộ phận hỗ trợ xử lý. Đã thu tại quầy: 'refund_pending' (quầy hoàn tiền mặt).
     * Trả tại quầy chưa thu: không cần hoàn.
     */
    public static function refund(array $booking): string
    {
        $p = $booking['payment'] ?? null;
        if (!$p || $p['status'] !== 'success') {
            if ($p && $p['status'] === 'unpaid') {
                Payment::update((int)$p['id'], ['status' => 'failed', 'message' => 'Khách huỷ vé trước khi thanh toán tại quầy']);
            }
            return 'Vé chưa thanh toán nên không phát sinh hoàn tiền.';
        }
        if ($p['provider'] === 'demo') {
            Payment::update((int)$p['id'], ['status' => 'refunded', 'message' => 'Đã hoàn tiền (cổng thử nghiệm)']);
            return 'Đã hoàn ' . money((int)$p['amount']) . ' về phương thức thanh toán ban đầu.';
        }
        if ($p['provider'] === 'momo' && $p['provider_txn_no'] && MomoService::isConfigured()) {
            [$ok, $msg] = MomoService::refund($p['txn_ref'], (int)$p['amount'], (string)$p['provider_txn_no'], 'Hoan tien ve CineGo ' . $p['booking_code']);
            if ($ok) {
                Payment::update((int)$p['id'], ['status' => 'refunded', 'message' => $msg]);
                return 'Đã hoàn ' . money((int)$p['amount']) . ' về ví MoMo của khách.';
            }
            Payment::update((int)$p['id'], ['status' => 'refund_pending', 'message' => 'MoMo chưa hoàn được: ' . $msg]);
            return 'Đã ghi nhận yêu cầu hoàn ' . money((int)$p['amount']) . ' nhưng MoMo chưa xử lý được (' . $msg . '); bộ phận hỗ trợ sẽ hoàn thủ công.';
        }
        Payment::update((int)$p['id'], ['status' => 'refund_pending', 'message' => 'Đã ghi nhận yêu cầu hoàn tiền']);
        return 'Yêu cầu hoàn ' . money((int)$p['amount']) . ' đã được ghi nhận, tiền sẽ về tài khoản trong 3–7 ngày làm việc.';
    }
}
