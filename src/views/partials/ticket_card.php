<?php
/** @var array $b  booking đã hydrate */
$st = $b['showtime'];
$mv = Movie::find($st['movie_id']);
?>
<div class="ticket" id="ticket">
    <div class="ticket-main">
        <p class="eyebrow">Vé xem phim</p>
        <h2><?= e($st['movie_title']) ?></h2>
        <dl class="kv">
            <dt>Mã đặt vé</dt><dd><b class="code"><?= e($b['code']) ?></b></dd>
            <dt>Rạp</dt><dd><?= e($st['cinema_name']) ?><br><span class="muted small"><?= e($st['cinema_address']) ?></span></dd>
            <dt>Suất chiếu</dt><dd><?= e($st['show_time']) ?> · <?= e(dateVi($st['show_date'])) ?></dd>
            <dt>Phòng</dt><dd>Phòng <?= (int)$st['room'] ?></dd>
            <dt>Ghế</dt><dd><?= e(implode(', ', array_column($b['seats'], 'code'))) ?></dd>
            <dt>Bắp nước</dt><dd><?php if ($b['combos']): foreach ($b['combos'] as $i => $c): ?><?= $i ? ', ' : '' ?><?= e($c['name']) ?> × <?= (int)$c['qty'] ?><?php endforeach; else: ?><span class="muted">Không</span><?php endif; ?></dd>
            <dt>Người đặt</dt><dd><?= e($b['customer_name']) ?> · <?= e($b['phone']) ?></dd>
            <dt>Thanh toán</dt><dd><?= e(Booking::PAYMENTS[$b['payment_method']] ?? $b['payment_method']) ?></dd>
            <dt>Đặt lúc</dt><dd><?= e(date('H:i d/m/Y', strtotime($b['created_at']))) ?></dd>
        </dl>
        <ul class="lines">
            <li><span>Tiền vé</span><b><?= e(money((int)$b['ticket_total'])) ?></b></li>
            <li><span>Bắp nước</span><b><?= e(money((int)$b['combo_total'])) ?></b></li>
            <?php if ((int)$b['discount'] > 0): ?><li class="discount"><span>Ưu đãi <?= e((string)$b['promo_code']) ?></span><b>−<?= e(money((int)$b['discount'])) ?></b></li><?php endif; ?>
        </ul>
        <div class="total"><span>Tổng tiền</span><strong><?= e(money((int)$b['total'])) ?></strong></div>
    </div>
    <div class="ticket-stub">
        <?php partial('qr', ['code' => $b['code']]); ?>
        <p class="small muted">Mã QR minh hoạ – không phải vé hợp lệ tại rạp.</p>
    </div>
</div>
