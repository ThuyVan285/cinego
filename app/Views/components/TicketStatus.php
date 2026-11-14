<?php
/**
 * Nhãn trạng thái vé: trạng thái đơn + (với vé hợp lệ) sắp chiếu / đã chiếu.
 * @var array $b booking đã hydrate
 */
$cls = ['pending' => 'is-pending', 'confirmed' => 'is-up', 'cancelled' => 'is-cancelled', 'expired' => 'is-cancelled'][$b['status']] ?? '';
if ($b['status'] === 'confirmed') {
    $label = $b['upcoming'] ? 'Sắp chiếu' : 'Đã chiếu';
    $cls = $b['upcoming'] ? 'is-up' : 'is-past';
    if (($b['payment']['status'] ?? '') === 'unpaid') {
        $label .= ' · Thanh toán tại quầy';
    }
} else {
    $label = $b['status_label'];
}
?>
<span class="status <?= $cls ?>"><?= e($label) ?></span>
