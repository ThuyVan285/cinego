<?php /** @var array $sum @var array $methods @var string $selected */ ?>
<div class="container">
    <?php component('Steps', ['step' => 5]); ?>
    <form method="post" action="/booking/pay" class="booking-layout" id="pay-form">
        <?= csrfField() ?>
        <section class="card pad">
            <h2>Phương thức thanh toán</h2>
            <div class="pay-options">
                <?php foreach ($methods as $k => $label):
                    $on = Payment::isAvailable($k); ?>
                    <label class="pay-option<?= $on ? '' : ' is-disabled' ?>">
                        <input type="radio" name="method" value="<?= e($k) ?>" <?= $selected === $k && $on ? 'checked' : '' ?> <?= $on ? '' : 'disabled' ?>>
                        <span class="pay-body">
                            <b><?= e($label) ?></b>
                            <small><?= e(Payment::description($k)) ?></small>
                            <?php if (!$on): ?><small class="pay-off">Chưa kích hoạt – quản trị viên cần điền khoá MoMo trong file .env.</small><?php endif; ?>
                        </span>
                    </label>
                <?php endforeach; ?>
            </div>
            <p class="muted small">
                Với thanh toán online, ghế được giữ thêm <?= (int)config('app.payment_timeout_minutes', 15) ?> phút trong lúc bạn thanh toán.
                Quá thời gian này, đơn tự huỷ và ghế được trả lại.
            </p>
            <p class="muted small">Khách hàng: <?= e($sum['customer']['name']) ?> · <?= e($sum['customer']['email']) ?> · <?= e($sum['customer']['phone']) ?></p>
        </section>
        <div class="side-col">
            <?php component('BookingSummary', ['sum' => $sum]); ?>
            <div class="action-row three">
                <a class="btn btn-ghost" href="/booking/confirm">Quay lại</a>
                <button class="btn btn-primary" type="submit">Thanh toán <?= e(money($sum['total'])) ?></button>
            </div>
        </div>
    </form>
    <div class="processing" id="processing" hidden aria-live="polite">
        <div class="processing-box"><span class="spinner"></span><strong>Đang chuyển tới cổng thanh toán...</strong><small>Vui lòng không đóng trang</small></div>
    </div>
</div>
