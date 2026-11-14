<?php
declare(strict_types=1);

/**
 * Cổng thanh toán MoMo (API v2 – https://developers.momo.vn).
 *  - create():  tạo giao dịch (requestType captureWallet) → payUrl để chuyển khách sang trang thanh toán MoMo.
 *  - verifyResult(): kiểm tra chữ ký HMAC-SHA256 của kết quả MoMo gửi về (redirectUrl hoặc IPN).
 *  - refund():  hoàn tiền một giao dịch đã thành công.
 * Cấu hình trong .env: MOMO_PARTNER_CODE, MOMO_ACCESS_KEY, MOMO_SECRET_KEY, MOMO_ENDPOINT.
 */
final class MomoService
{
    /** resultCode của MoMo → thông báo cho khách. */
    public const RESULT_MESSAGES = [
        '0' => 'Giao dịch thành công',
        '9000' => 'Giao dịch đã được xác nhận',
        '1000' => 'Giao dịch đang chờ người dùng xác nhận thanh toán',
        '1001' => 'Tài khoản MoMo không đủ số dư',
        '1002' => 'Giao dịch bị từ chối bởi nhà phát hành',
        '1003' => 'Giao dịch đã bị huỷ',
        '1004' => 'Số tiền vượt quá hạn mức thanh toán',
        '1005' => 'Liên kết hoặc mã QR thanh toán đã hết hạn',
        '1006' => 'Khách hàng đã từ chối xác nhận thanh toán',
        '1007' => 'Tài khoản MoMo đang bị tạm khoá',
        '1017' => 'Giao dịch bị huỷ bởi đối tác',
        '1026' => 'Giao dịch bị hạn chế theo thể lệ khuyến mãi',
        '1080' => 'Hoàn tiền không thành công: số tiền không hợp lệ',
        '1081' => 'Hoàn tiền bị từ chối: giao dịch gốc đã được hoàn',
        '2019' => 'Yêu cầu bị từ chối',
        '4001' => 'Tài khoản MoMo bị hạn chế',
        '4100' => 'Khách hàng chưa đăng nhập ứng dụng MoMo',
        '7000' => 'Giao dịch đang được xử lý',
        '7002' => 'Giao dịch đang được nhà cung cấp xử lý',
        '11' => 'Truy cập bị từ chối (sai cấu hình đối tác)',
        '12' => 'Phiên bản API không còn được hỗ trợ',
        '13' => 'Xác thực đối tác thất bại (sai accessKey hoặc chữ ký)',
        '20' => 'Yêu cầu sai định dạng',
        '21' => 'Số tiền giao dịch không hợp lệ',
        '40' => 'Mã yêu cầu (requestId) bị trùng',
        '41' => 'Mã đơn (orderId) bị trùng',
        '42' => 'Mã đơn không hợp lệ hoặc không tồn tại',
        '43' => 'Giao dịch đang được xử lý ở một yêu cầu khác',
        '99' => 'Lỗi không xác định',
    ];

    public static function config(): array
    {
        return (array)config('payment.momo', []);
    }

    public static function isConfigured(): bool
    {
        $c = self::config();
        return ($c['partner_code'] ?? '') !== '' && ($c['access_key'] ?? '') !== '' && ($c['secret_key'] ?? '') !== '';
    }

    public static function message(string $resultCode): string
    {
        return self::RESULT_MESSAGES[$resultCode] ?? ('Giao dịch không thành công (mã MoMo ' . $resultCode . ')');
    }

    // ---------- Chữ ký ----------
    /** Chuỗi ký: các cặp key=value theo đúng thứ tự tài liệu MoMo (đã sắp xếp a→z), nối bằng &. */
    public static function rawSignature(array $fields, array $order): string
    {
        return implode('&', array_map(fn($k) => $k . '=' . ($fields[$k] ?? ''), $order));
    }

    public static function sign(array $fields, array $order, ?string $secret = null): string
    {
        return hash_hmac('sha256', self::rawSignature($fields, $order), $secret ?? (string)self::config()['secret_key']);
    }

    private const CREATE_FIELDS = ['accessKey', 'amount', 'extraData', 'ipnUrl', 'orderId', 'orderInfo', 'partnerCode', 'redirectUrl', 'requestId', 'requestType'];
    private const RESULT_FIELDS = ['accessKey', 'amount', 'extraData', 'message', 'orderId', 'orderInfo', 'orderType', 'partnerCode', 'payType', 'requestId', 'responseTime', 'resultCode', 'transId'];
    private const REFUND_FIELDS = ['accessKey', 'amount', 'description', 'orderId', 'partnerCode', 'requestId', 'transId'];

    // ---------- Tạo giao dịch ----------
    /**
     * @return array{0:?string,1:?string} [payUrl, lỗi]
     */
    public static function create(string $orderId, int $amount, string $orderInfo): array
    {
        $c = self::config();
        $base = rtrim((string)config('app.url'), '/');
        $f = [
            'partnerCode' => $c['partner_code'],
            'accessKey' => $c['access_key'],
            'requestId' => $orderId,
            'amount' => (string)$amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $base . '/payment/return/momo',
            'ipnUrl' => $base . '/payment/ipn/momo',
            'requestType' => $c['request_type'] ?? 'captureWallet',
            'extraData' => '',
        ];
        $body = $f + ['partnerName' => APP_NAME, 'storeId' => APP_NAME, 'lang' => 'vi', 'autoCapture' => true, 'signature' => self::sign($f, self::CREATE_FIELDS)];
        unset($body['accessKey']); // accessKey chỉ dùng để ký, không gửi trong body
        [$res, $err] = self::post('/v2/gateway/api/create', $body);
        if ($err !== null) {
            return [null, $err];
        }
        if ((string)($res['resultCode'] ?? '') !== '0' || empty($res['payUrl'])) {
            return [null, 'MoMo từ chối tạo giao dịch: ' . self::message((string)($res['resultCode'] ?? '99'))
                . (isset($res['message']) ? ' (' . $res['message'] . ')' : '')];
        }
        return [(string)$res['payUrl'], null];
    }

    /** Kiểm tra chữ ký kết quả MoMo gửi về (dùng chung cho redirectUrl và IPN). */
    public static function verifyResult(array $p, ?array $config = null): bool
    {
        $c = $config ?? self::config();
        $given = (string)($p['signature'] ?? '');
        if ($given === '' || ($c['secret_key'] ?? '') === '' || (string)($p['partnerCode'] ?? '') !== (string)$c['partner_code']) {
            return false;
        }
        $fields = ['accessKey' => $c['access_key']] + array_map(fn($v) => is_scalar($v) ? (string)$v : '', $p);
        return hash_equals(self::sign($fields, self::RESULT_FIELDS, (string)$c['secret_key']), strtolower($given));
    }

    // ---------- Hoàn tiền ----------
    /**
     * Hoàn toàn bộ số tiền của giao dịch $transId.
     * @return array{0:bool,1:string} [thành công?, thông báo]
     */
    public static function refund(string $originalRef, int $amount, string $transId, string $description): array
    {
        $c = self::config();
        $refundId = $originalRef . 'R' . date('His');
        $f = [
            'partnerCode' => $c['partner_code'], 'accessKey' => $c['access_key'], 'orderId' => $refundId, 'requestId' => $refundId,
            'amount' => (string)$amount, 'transId' => $transId, 'description' => $description,
        ];
        $body = $f + ['lang' => 'vi', 'signature' => self::sign($f, self::REFUND_FIELDS)];
        unset($body['accessKey']);
        [$res, $err] = self::post('/v2/gateway/api/refund', $body);
        if ($err !== null) {
            return [false, $err];
        }
        $rc = (string)($res['resultCode'] ?? '99');
        return [$rc === '0', $rc === '0' ? 'MoMo đã hoàn tiền (mã GD hoàn ' . ($res['transId'] ?? $refundId) . ')' : self::message($rc)];
    }

    // ---------- HTTP ----------
    /** @return array{0:?array,1:?string} [JSON trả về, lỗi] */
    private static function post(string $path, array $body): array
    {
        $url = rtrim((string)(self::config()['endpoint'] ?? 'https://test-payment.momo.vn'), '/') . $path;
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json; charset=UTF-8'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 30,
        ]);
        $raw = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($raw === false || $errno) {
            error_log("MoMo $path: $error");
            return [null, 'Không kết nối được tới MoMo, vui lòng thử lại sau.'];
        }
        $res = json_decode((string)$raw, true);
        if (!is_array($res)) {
            error_log("MoMo $path: phản hồi không hợp lệ: " . substr((string)$raw, 0, 300));
            return [null, 'MoMo trả về dữ liệu không hợp lệ.'];
        }
        return [$res, null];
    }
}
