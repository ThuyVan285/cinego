<?php
/** @var array $sum  kết quả Booking::summary() */
$st = $sum['showtime'];
$mv = Movie::find($st['movie_id']);
?>
<aside class="summary card" id="summary" aria-label="Tóm tắt đặt vé">
    <div class="summary-movie">
        <?= img(artUrl('poster', $st['movie_id']), 'Poster ' . $st['movie_title']) ?>
        <div>
            <h3><?= e($st['movie_title']) ?></h3>
            <p class="muted small"><?= e(implode(', ', $mv['genres'] ?? [])) ?> · <span class="<?= e(ageClass($st['movie_age'])) ?>"><?= e($st['movie_age']) ?></span></p>
        </div>
    </div>
    <dl class="kv">
        <dt>Rạp</dt><dd><?= e($st['cinema_name']) ?><br><span class="muted small"><?= e($st['cinema_address']) ?></span></dd>
        <dt>Suất chiếu</dt><dd><?= e($st['show_time']) ?> · <?= e(dateVi($st['show_date'])) ?></dd>
        <dt>Phòng</dt><dd>Phòng <?= (int)$st['room'] ?> (2D)</dd>
        <dt>Ghế</dt><dd id="sum-seats"><?= $sum['seats'] ? e(implode(', ', array_column($sum['seats'], 'code'))) : '<span class="muted">Chưa chọn</span>' ?></dd>
    </dl>
    <ul class="lines">
        <li class="line-tickets"><span>Vé (<span id="sum-count"><?= count($sum['seats']) ?></span>)</span><b id="sum-tickets"><?= e(money($sum['ticket_total'])) ?></b></li>
        <?php foreach ($sum['combos'] as $c): ?>
            <li class="combo-line"><span><?= e($c['name']) ?> × <?= (int)$c['qty'] ?></span><b><?= e(money($c['qty'] * $c['price'])) ?></b></li>
        <?php endforeach; ?>
        <?php if ($sum['discount'] > 0): ?>
            <li class="discount"><span>Ưu đãi <?= e($sum['promo']['code']) ?></span><b>−<?= e(money($sum['discount'])) ?></b></li>
        <?php endif; ?>
    </ul>
    <div class="total"><span>Tổng cộng</span><strong id="sum-total" data-combo="<?= (int)$sum['combo_total'] ?>"><?= e(money($sum['total'])) ?></strong></div>
</aside>
