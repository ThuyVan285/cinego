<?php /** @var array $b */
$unpaid = ($b['payment']['status'] ?? '') === 'unpaid'; ?>
<div class="container narrow">
    <?php component('Steps', ['step' => 6]); ?>
    <?php if ($b['status'] === 'confirmed'): ?>
        <div class="success-head">
            <span class="success-icon"><svg viewBox="0 0 24 24" width="34" height="34" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg></span>
            <h1>Đặt vé thành công!</h1>
            <p class="muted">
                <?= $unpaid ? 'Vé đã được giữ – vui lòng thanh toán tại quầy trước giờ chiếu 30 phút.' : 'Thanh toán đã hoàn tất.' ?>
                Mã QR bên dưới là vé điện tử của bạn, vé cũng được lưu trong mục “Vé của tôi”.
            </p>
        </div>
    <?php else: ?>
        <div class="success-head">
            <h1><?= e($b['status_label']) ?></h1>
            <p class="muted">Đơn <?= e($b['code']) ?> chưa được xác nhận.</p>
        </div>
    <?php endif; ?>
    <?php component('TicketCard', ['b' => $b]); ?>
    <div class="action-row center no-print">
        <a class="btn btn-primary" href="/tickets">Xem vé của tôi</a>
        <?php if ($b['status'] === 'confirmed'): ?><button class="btn btn-ghost" type="button" data-print>In vé</button><?php endif; ?>
        <a class="btn btn-ghost" href="/">Quay về trang chủ</a>
    </div>
</div>
