<?php
declare(strict_types=1);

/**
 * Phương thức thanh toán (app/Config/payment.php) và giao dịch thanh toán (bảng payments).
 */
final class Payment
{
    public const STATUS_LABEL = [
        'pending' => 'Đang chờ thanh toán',
        'success' => 'Thanh toán thành công',
        'failed' => 'Thanh toán thất bại',
        'unpaid' => 'Chưa thanh toán (trả tại quầy)',
        'refund_pending' => 'Đang hoàn tiền',
        'refunded' => 'Đã hoàn tiền',
    ];

    // ---------- Phương thức ----------
    /** @return array<string,string> mã → tên hiển thị (kể cả phương thức đang tắt) */
    public static function methods(): array
    {
        return (array)config('payment.methods', []);
    }

    public static function description(string $method): string
    {
        return (string)(config('payment.descriptions', [])[$method] ?? '');
    }

    public static function defaultMethod(): string
    {
        return (string)config('payment.default', 'demo');
    }

    /** MoMo chỉ dùng được khi đã cấu hình partnerCode + accessKey + secretKey. */
    public static function isAvailable(string $method): bool
    {
        if (!isset(self::methods()[$method])) {
            return false;
        }
        if ($method === 'momo') {
            return MomoService::isConfigured();
        }
        return true;
    }

    /** Hợp lệ để lưu vào đơn (phương thức có trong cấu hình). */
    public static function isValid(string $method): bool
    {
        return isset(self::methods()[$method]);
    }

    public static function label(string $method): string
    {
        return self::methods()[$method] ?? $method;
    }

    public static function isOnline(string $method): bool
    {
        return $method !== 'counter';
    }

    // ---------- Giao dịch ----------
    /** Tạo giao dịch mới cho đơn, trả về mã tham chiếu (txn_ref) gửi sang cổng thanh toán. */
    public static function create(string $bookingCode, string $provider, int $amount, string $status = 'pending'): string
    {
        $ref = str_replace('-', '', $bookingCode) . date('His') . random_int(10, 99);
        Database::exec('INSERT INTO payments (booking_code, provider, txn_ref, amount, status, created_at) VALUES (?,?,?,?,?,NOW())',
            [$bookingCode, $provider, $ref, $amount, $status]);
        return $ref;
    }

    public static function findByRef(string $ref, bool $forUpdate = false): ?array
    {
        return Database::one('SELECT * FROM payments WHERE txn_ref = ?' . ($forUpdate ? ' FOR UPDATE' : ''), [$ref]);
    }

    /** @return array[] giao dịch của đơn, mới nhất trước */
    public static function forBooking(string $bookingCode): array
    {
        return Database::all('SELECT * FROM payments WHERE booking_code = ? ORDER BY id DESC', [$bookingCode]);
    }

    public static function update(int $id, array $fields): void
    {
        $allowed = ['status', 'bank_code', 'provider_txn_no', 'response_code', 'message', 'raw_response', 'paid_at'];
        $fields = array_intersect_key($fields, array_flip($allowed));
        if (!$fields) {
            return;
        }
        $set = implode(', ', array_map(fn($k) => "$k = ?", array_keys($fields)));
        Database::exec("UPDATE payments SET $set WHERE id = ?", [...array_values($fields), $id]);
    }
}
