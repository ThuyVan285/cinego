<?php
/** @var array $m @var string $date @var string $cinemaId @var array $sched @var array $cinemaOptions @var array $related */
$soon = $m['status'] === 'soon';
?>
<section class="detail-hero">
    <?= img(artUrl('backdrop', $m['id']), 'Ảnh nền ' . $m['title'], 'hero-bg', 'backdrop') ?>
    <div class="hero-shade"></div>
    <div class="container detail-grid">
        <div class="detail-poster"><?= img(artUrl('poster', $m['id']), 'Poster ' . $m['title']) ?></div>
        <div class="detail-main">
            <p class="eyebrow"><?= $soon ? 'Sắp chiếu' : 'Đang chiếu' ?></p>
            <h1><?= e($m['title']) ?></h1>
            <p class="hero-meta">
                <span class="<?= e(ageClass($m['age'])) ?>"><?= e($m['age']) ?></span>
                <span><?= (int)$m['duration'] ?> phút</span>
                <?php if (!$soon): ?><span class="rate">★ <?= number_format($m['rating'], 1) ?>/10</span><?php endif; ?>
            </p>
            <div class="tag-row"><?php foreach ($m['genres'] as $g): ?><span class="tag"><?= e($g) ?></span><?php endforeach; ?></div>
            <p class="hero-desc"><?= e($m['synopsis']) ?></p>
            <dl class="kv kv-light">
                <dt>Đạo diễn</dt><dd><?= e($m['director']) ?></dd>
                <dt>Diễn viên</dt><dd><?= e(implode(', ', $m['cast'])) ?></dd>
                <dt>Khởi chiếu</dt><dd><?= e(date('d/m/Y', strtotime($m['release']))) ?></dd>
                <dt>Phân loại</dt><dd><?= e($m['age']) ?> – <?= ['P' => 'Mọi lứa tuổi', 'T13' => 'Từ 13 tuổi', 'T16' => 'Từ 16 tuổi', 'T18' => 'Từ 18 tuổi'][$m['age']] ?? '' ?></dd>
            </dl>
            <div class="hero-actions">
                <?php if (!$soon): ?><a class="btn btn-primary btn-lg" href="#lich">Đặt vé ngay</a><?php endif; ?>
                <?php if ($m['trailer']): ?><a class="btn btn-light btn-lg" href="<?= e($m['trailer']) ?>" target="_blank" rel="noopener noreferrer">▶ Xem trailer</a><?php endif; ?>
            </div>
        </div>
    </div>
</section>

<div class="container">
    <section class="section" id="lich">
        <div class="section-head"><h2>Lịch chiếu</h2></div>
        <?php if ($soon): ?>
            <div class="empty"><strong>Phim chưa mở bán vé</strong>
                <span><?= e($m['title']) ?> khởi chiếu từ <?= e(date('d/m/Y', strtotime($m['release']))) ?>. Hiện chưa có suất chiếu để đặt vé.</span>
                <a class="btn btn-ghost" href="/movies?status=now">Xem phim đang chiếu</a></div>
        <?php else: ?>
            <div class="card pad">
                <?php partial('date_strip', ['date' => $date, 'base' => '/movies/' . $m['id'], 'params' => ['cinema' => $cinemaId]]); ?>
                <form class="inline-filter" method="get" action="/movies/<?= e($m['id']) ?>#lich" data-autosubmit>
                    <input type="hidden" name="date" value="<?= e($date) ?>">
                    <label class="field"><span>Rạp</span>
                        <select name="cinema">
                            <option value="">Tất cả rạp có suất chiếu</option>
                            <?php foreach ($cinemaOptions as $c): ?><option value="<?= e($c['id']) ?>" <?= $cinemaId === $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?> – <?= e($c['city']) ?></option><?php endforeach; ?>
                        </select></label>
                    <noscript><button class="btn btn-ghost" type="submit">Lọc</button></noscript>
                </form>
                <?php partial('schedule', ['sched' => $sched, 'hideMovie' => true, 'empty' => 'Phim chưa có suất chiếu vào ngày này. Hãy chọn ngày khác.']); ?>
            </div>
        <?php endif; ?>
    </section>

    <?php if ($related): ?>
        <section class="section">
            <div class="section-head"><h2>Phim cùng thể loại</h2></div>
            <div class="movie-grid"><?php foreach ($related as $r) { partial('movie_card', ['m' => $r]); } ?></div>
        </section>
    <?php endif; ?>
</div>
