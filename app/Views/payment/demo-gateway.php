<?php /** @var array $payment @var array $booking @var array $params */
$st = $booking['showtime'];
$left = $booking['expires_at'] ? strtotime($booking['expires_at']) : time();
?>
<div class="gateway">
    <div class="gateway-card">
        <div class="gateway-head">
            <span class="gateway-logo">CineGo<b>Pay</b></span>
            <span class="gateway-badge">MÔI TRƯỜNG THỬ NGHIỆM</span>
        </div>
        <p class="gateway-note">Đây là cổng thanh toán mô phỏng để chạy thử quy trình. Không có tiền thật được trừ.</p>
        <dl class="gateway-kv">
            <dt>Đơn vị nhận</dt><dd><?= APP_NAME ?></dd>
            <dt>Mã giao dịch</dt><dd><code><?= e($payment['txn_ref']) ?></code></dd>
            <dt>Nội dung</dt><dd><?= e($params['vnp_OrderInfo'] ?? '') ?></dd>
            <dt>Phim</dt><dd><?= e($st['movie_title']) ?> · <?= e($st['show_time']) ?> <?= e(date('d/m', strtotime($st['show_date']))) ?></dd>
            <dt>Ghế</dt><dd><?= e(implode(', ', array_column($booking['seats'], 'code'))) ?></dd>
        </dl>
        <div class="gateway-amount"><span>Số tiền</span><strong><?= e(money((int)$payment['amount'])) ?></strong></div>
        <?php if ($booking['status'] === 'pending'): ?>
            <p class="hold-timer" data-hold-until="<?= $left * 1000 ?>" data-expired-text="Đã hết thời gian thanh toán, đơn sẽ tự huỷ.">Thời gian còn lại: <b>--:--</b></p>
            <form method="post" class="gateway-actions">
                <?= csrfField() ?>
                <button class="btn btn-primary" name="result" value="success">Thanh toán thành công</button>
                <button class="btn btn-ghost" name="result" value="insufficient">Thử: tài khoản không đủ số dư</button>
                <button class="btn btn-ghost" name="result" value="cancel">Huỷ giao dịch</button>
            </form>
        <?php else: ?>
            <p class="notice notice-info">Đơn này không còn chờ thanh toán (<?= e($booking['status_label']) ?>).</p>
            <a class="btn btn-ghost" href="/tickets/<?= e($booking['code']) ?>">Xem đơn</a>
        <?php endif; ?>
    </div>
</div>
