<?php
/** @var array $b  booking đã hydrate */
$st = $b['showtime'];
$p = $b['payment'];
$valid = $b['status'] === 'confirmed';
?>
<div class="ticket<?= $valid ? '' : ' is-void' ?>" id="ticket">
    <div class="ticket-main">
        <p class="eyebrow">Vé xem phim · <?php component('TicketStatus', ['b' => $b]); ?></p>
        <h2><?= e($st['movie_title']) ?></h2>
        <dl class="kv">
            <dt>Mã đặt vé</dt><dd><b class="code"><?= e($b['code']) ?></b></dd>
            <dt>Rạp</dt><dd><?= e($st['cinema_name']) ?><br><span class="muted small"><?= e($st['cinema_address']) ?></span></dd>
            <dt>Suất chiếu</dt><dd><?= e($st['show_time']) ?> · <?= e(dateVi($st['show_date'])) ?></dd>
            <dt>Phòng</dt><dd><?= e($st['room_name']) ?> (<?= e($st['room_format']) ?>)</dd>
            <dt>Ghế</dt><dd><?php foreach ($b['seats'] as $i => $s): ?><?= $i ? ', ' : '' ?><?= e($s['code']) ?> <span class="muted small">(<?= e(Seat::TYPE_LABEL[$s['type']] ?? $s['type']) ?>)</span><?php endforeach; ?></dd>
            <dt>Bắp nước</dt><dd><?php if ($b['combos']): foreach ($b['combos'] as $i => $c): ?><?= $i ? ', ' : '' ?><?= e($c['name']) ?> × <?= (int)$c['qty'] ?><?php endforeach; else: ?><span class="muted">Không</span><?php endif; ?></dd>
            <dt>Người đặt</dt><dd><?= e($b['customer_name']) ?> · <?= e($b['phone']) ?></dd>
            <dt>Đặt lúc</dt><dd><?= e(date('H:i d/m/Y', strtotime($b['created_at']))) ?></dd>
        </dl>
        <ul class="lines">
            <li><span>Tiền vé</span><b><?= e(money((int)$b['ticket_total'])) ?></b></li>
            <li><span>Bắp nước</span><b><?= e(money((int)$b['combo_total'])) ?></b></li>
            <?php if ((int)$b['discount'] > 0): ?><li class="discount"><span>Ưu đãi <?= e((string)$b['promo_code']) ?></span><b>−<?= e(money((int)$b['discount'])) ?></b></li><?php endif; ?>
        </ul>
        <div class="total"><span>Tổng tiền</span><strong><?= e(money((int)$b['total'])) ?></strong></div>

        <?php if ($p): ?>
            <h3 class="ticket-sub">Thông tin giao dịch</h3>
            <dl class="kv">
                <dt>Phương thức</dt><dd><?= e(Payment::label($b['payment_method'])) ?></dd>
                <dt>Trạng thái</dt><dd><span class="pay-status pay-<?= e($b['pay_status']) ?>"><?= e($b['pay_label']) ?></span></dd>
                <dt>Mã giao dịch</dt><dd><code><?= e($p['txn_ref']) ?></code></dd>
                <?php if ($p['provider_txn_no'] && $p['provider_txn_no'] !== '0'): ?><dt>Mã GD cổng</dt><dd><code><?= e($p['provider_txn_no']) ?></code><?= $p['bank_code'] ? ' · ' . e($p['bank_code']) : '' ?></dd><?php endif; ?>
                <?php if ($p['paid_at']): ?><dt>Thanh toán lúc</dt><dd><?= e(date('H:i:s d/m/Y', strtotime($p['paid_at']))) ?></dd><?php endif; ?>
                <?php if ($p['message'] && $p['status'] !== 'success'): ?><dt>Ghi chú</dt><dd><?= e($p['message']) ?></dd><?php endif; ?>
            </dl>
        <?php endif; ?>
    </div>
    <div class="ticket-stub">
        <?php if ($valid): ?>
            <?php component('QRCode', ['code' => $b['code']]); ?>
            <p class="small muted">Đưa mã QR này cho nhân viên soát vé.<?= ($p['status'] ?? '') === 'unpaid' ? ' Thanh toán tại quầy trước giờ chiếu 30 phút.' : '' ?></p>
        <?php elseif ($b['status'] === 'pending'): ?>
            <p class="small"><b>Đang chờ thanh toán</b></p>
            <p class="small muted">Mã QR xuất hiện sau khi thanh toán thành công.</p>
        <?php else: ?>
            <p class="small"><b><?= e($b['status_label']) ?></b></p>
            <p class="small muted">Vé không còn hiệu lực.</p>
        <?php endif; ?>
    </div>
</div>
