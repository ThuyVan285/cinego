<?php /** @var array $c @var string $date @var array $sched */ ?>
<div class="container">
    <p class="crumbs"><a href="/cinemas">Rạp chiếu</a> / <?= e($c['name']) ?></p>
    <div class="cinema-hero card">
        <?= img(artUrl('cinema', $c['id']), $c['name'], '', 'cinema') ?>
        <div class="cinema-body">
            <span class="brand-tag" style="--c:<?= e($c['color']) ?>"><?= e($c['brand_name']) ?></span>
            <h1><?= e($c['name']) ?></h1>
            <p class="muted"><?= e($c['address']) ?>, <?= e($c['city']) ?> · <?= (int)$c['rooms'] ?> phòng chiếu</p>
        </div>
    </div>
    <div class="section-head"><h2>Lịch chiếu tại rạp</h2></div>
    <?php partial('date_strip', ['date' => $date, 'base' => '/cinemas/' . $c['id']]); ?>
    <div class="card pad"><?php partial('schedule', ['sched' => $sched, 'hideCinema' => true, 'empty' => 'Rạp không có suất chiếu vào ngày này. Hãy chọn ngày khác.']); ?></div>
</div>
