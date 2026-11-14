<?php /** @var array $c @var string $date @var array $sched */ ?>
<div class="container">
    <p class="crumbs"><a href="/cinemas">Rạp chiếu</a> / <?= e($c['name']) ?></p>

    <!-- Hero rạp thu gọn -->
    <div class="cinema-detail-hero">
        <div class="cinema-detail-icon" style="background:<?= e($c['color']) ?>22;border-color:<?= e($c['color']) ?>55">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="<?= e($c['color']) ?>" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        </div>
        <div class="cinema-detail-info">
            <span class="brand-tag" style="--c:<?= e($c['color']) ?>"><?= e($c['brand_name']) ?></span>
            <h1><?= e($c['name']) ?></h1>
            <p class="cinema-detail-address">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <?= e($c['address']) ?>, <?= e($c['city']) ?>
                <span class="cinema-detail-rooms">&nbsp;·&nbsp;<?= (int)$c['rooms'] ?> phòng chiếu</span>
            </p>
        </div>
    </div>

    <?php $mapQuery = $c['name'] . ', ' . $c['address'] . ', ' . $c['city']; ?>
    <section class="cinema-location">
        <div class="cinema-map">
            <iframe title="Bản đồ <?= e($c['name']) ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                    src="https://maps.google.com/maps?q=<?= e(rawurlencode($mapQuery)) ?>&amp;z=16&amp;output=embed"></iframe>
        </div>
        <div class="cinema-location-info">
            <h2>Vị trí rạp</h2>
            <p><?= e($c['address']) ?>, <?= e($c['city']) ?></p>
            <p class="muted small">Khu vực <?= e($c['area']) ?> · <?= (int)$c['rooms'] ?> phòng chiếu</p>
            <a class="btn btn-primary btn-sm" target="_blank" rel="noopener noreferrer"
               href="https://www.google.com/maps/dir/?api=1&amp;destination=<?= e(rawurlencode($mapQuery)) ?>">Chỉ đường</a>
            <?php if ($rooms): ?>
                <ul class="room-list">
                    <?php foreach ($rooms as $r): ?>
                        <li><b><?= e($r['name']) ?></b> <span class="muted small"><?= e($r['format']) ?> · <?= (int)$r['seat_count'] ?> ghế<?= (int)$r['couple_count'] > 0 ? ' · có ghế đôi' : '' ?></span></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </section>

    <div class="section-head"><h2>Lịch chiếu tại rạp</h2></div>
    <?php component('DateStrip', ['date' => $date, 'base' => '/cinemas/' . $c['id']]); ?>
    <div class="sched-list"><?php component('Schedule', ['sched' => $sched, 'hideCinema' => true, 'empty' => 'Rạp không có suất chiếu vào ngày này. Hãy chọn ngày khác.']); ?></div>
</div>
