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

    <div class="section-head"><h2>Lịch chiếu tại rạp</h2></div>
    <?php partial('date_strip', ['date' => $date, 'base' => '/cinemas/' . $c['id']]); ?>
    <div class="sched-list"><?php partial('schedule', ['sched' => $sched, 'hideCinema' => true, 'empty' => 'Rạp không có suất chiếu vào ngày này. Hãy chọn ngày khác.']); ?></div>
</div>
