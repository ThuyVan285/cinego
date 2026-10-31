<?php
/** @var string $date @var array $f @var array $brands @var array $cities @var array $areas @var array $movieList @var array $sched */
?>
<div class="container">
    <div class="page-head"><h1>Lịch chiếu Đà Nẵng</h1><p class="muted">Chọn ngày, khu vực, rạp và suất chiếu phù hợp tại Đà Nẵng.</p></div>
    <?php partial('date_strip', ['date' => $date, 'base' => '/showtimes', 'params' => $f]); ?>
    <form class="filter-bar" method="get" action="/showtimes" data-autosubmit>
        <input type="hidden" name="date" value="<?= e($date) ?>">
        <label class="field"><span>Hệ thống rạp</span>
            <select name="brand"><option value="">Tất cả</option>
                <?php foreach ($brands as $id => $b): ?><option value="<?= e($id) ?>" <?= $f['brand'] === $id ? 'selected' : '' ?>><?= e($b['name']) ?></option><?php endforeach; ?>
            </select></label>
        <label class="field"><span>Khu vực</span>
            <select name="area"><option value="">Tất cả</option>
                <?php foreach ($areas as $a): ?><option <?= $f['area'] === $a ? 'selected' : '' ?>><?= e($a) ?></option><?php endforeach; ?>
            </select></label>
        <label class="field grow"><span>Phim</span>
            <select name="movie"><option value="">Tất cả phim đang chiếu</option>
                <?php foreach ($movieList as $m): ?><option value="<?= e($m['id']) ?>" <?= $f['movie'] === $m['id'] ? 'selected' : '' ?>><?= e($m['title']) ?></option><?php endforeach; ?>
            </select></label>
        <div class="filter-actions">
            <noscript><button class="btn btn-primary" type="submit">Lọc</button></noscript>
            <a class="btn btn-ghost" href="<?= e(url('/showtimes', ['date' => $date])) ?>">Xoá lọc</a>
        </div>
    </form>
    <p class="muted small result-count"><?= count($sched) ?> rạp có suất chiếu · <?= e(dateVi($date)) ?></p>
    <div class="card pad"><?php partial('schedule', ['sched' => $sched, 'empty' => 'Không có rạp nào có suất chiếu phù hợp bộ lọc trong ngày này. Hãy đổi ngày hoặc xoá bộ lọc.']); ?></div>
</div>
