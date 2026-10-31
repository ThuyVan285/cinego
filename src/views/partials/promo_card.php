<?php /** @var array $p */ ?>
<article class="promo-card card">
    <?= img(artUrl('promo', $p['code']), 'Ưu đãi ' . $p['title'], '', 'promo') ?>
    <div class="promo-body">
        <h3><?= e($p['title']) ?></h3>
        <p><?= e($p['description']) ?></p>
        <p class="muted small">Điều kiện: <?= e($p['conditions']) ?></p>
        <p class="muted small">Hạn dùng: <?= e(date('d/m/Y', strtotime($p['expires_at']))) ?> · Mã: <b class="code-pill" data-copy="<?= e($p['code']) ?>"><?= e($p['code']) ?></b></p>
        <div class="card-actions">
            <a class="btn btn-ghost btn-sm" href="/promotions#<?= e($p['code']) ?>">Xem chi tiết</a>
            <a class="btn btn-primary btn-sm" href="/booking">Đặt vé &amp; dùng mã</a>
        </div>
    </div>
</article>
