<?php
declare(strict_types=1);

final class Promotion
{
    public static function all(): array
    {
        return Database::all('SELECT * FROM promotions ORDER BY expires_at');
    }

    public static function find(string $code): ?array
    {
        return Database::one('SELECT * FROM promotions WHERE code = ?', [strtoupper(trim($code))]);
    }

    /**
     * Tính giảm giá. Trả về [discount, error]. Giảm giá không bao giờ vượt tổng tạm tính.
     * @return array{0:int,1:?string}
     */
    public static function evaluate(?array $promo, int $ticketTotal, int $comboTotal): array
    {
        if ($promo === null) {
            return [0, 'Mã ưu đãi không tồn tại.'];
        }
        if ($promo['expires_at'] < today()) {
            return [0, 'Mã ưu đãi đã hết hạn.'];
        }
        $subtotal = $ticketTotal + $comboTotal;
        if ($subtotal < (int)$promo['min_spend']) {
            return [0, 'Đơn hàng cần tối thiểu ' . money((int)$promo['min_spend']) . ' để dùng mã này.'];
        }
        if ($promo['need_combo'] && $comboTotal <= 0) {
            return [0, 'Mã này chỉ áp dụng khi đơn có combo bắp nước.'];
        }
        $d = $promo['type'] === 'pct' ? intdiv($subtotal * (int)$promo['value'], 100) : (int)$promo['value'];
        $d = min($d, (int)$promo['max_discount'], $subtotal);
        return [max(0, $d), null];
    }
}
