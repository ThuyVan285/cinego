<?php /** @var array $sum @var array $methods @var string $selected */
$desc = [
    'wallet' => 'Quét mã bằng ví điện tử giả lập – hoàn tất ngay.',
    'transfer' => 'Giả lập chuyển khoản ngân hàng, không có giao dịch thật.',
    'counter' => 'Giữ chỗ và thanh toán khi đến rạp (mô phỏng).',
]; ?>
<div class="container">
    <?php partial('steps', ['step' => 5]); ?>
    <form method="post" action="/booking/pay" class="booking-layout" id="pay-form">
        <?= csrfField() ?>
        <section class="card pad">
            <h2>Phương thức thanh toán</h2>
            <div class="notice notice-info">Giao diện thanh toán mô phỏng – không kết nối ngân hàng và không thu tiền thật.</div>
            <div class="pay-options">
                <?php foreach ($methods as $k => $label): ?>
                    <label class="pay-option">
                        <input type="radio" name="method" value="<?= e($k) ?>" <?= $selected === $k ? 'checked' : '' ?>>
                        <span class="pay-body"><b><?= e($label) ?></b><small><?= e($desc[$k]) ?></small></span>
                    </label>
                <?php endforeach; ?>
            </div>
            <p class="muted small">Khách hàng: <?= e($sum['customer']['name']) ?> · <?= e($sum['customer']['email']) ?> · <?= e($sum['customer']['phone']) ?></p>
        </section>
        <div class="side-col">
            <?php partial('summary', ['sum' => $sum]); ?>
            <div class="action-row three">
                <a class="btn btn-ghost" href="/booking/confirm">Quay lại</a>
                <button class="btn btn-primary" type="submit">Thanh toán <?= e(money($sum['total'])) ?></button>
            </div>
        </div>
    </form>
    <div class="processing" id="processing" hidden aria-live="polite">
        <div class="processing-box"><span class="spinner"></span><strong>Đang xử lý thanh toán...</strong><small>Vui lòng đợi trong giây lát</small></div>
    </div>
</div>
