<?php
/**
 * @var array $sched      kết quả Showtime::schedule()
 * @var bool  $hideMovie  ẩn tên phim (khi đang ở trang chi tiết phim)
 * @var bool  $hideCinema ẩn tên rạp (khi đang ở trang chi tiết rạp)
 * @var string $empty     thông báo khi rỗng
 */
$hideMovie  = $hideMovie  ?? false;
$hideCinema = $hideCinema ?? false;
$empty      = $empty      ?? 'Không có suất chiếu phù hợp trong ngày đã chọn.';
?>
<?php if (!$sched): ?>
    <div class="empty"><strong>Không có suất chiếu</strong><span><?= e($empty) ?></span></div>
<?php else: foreach ($sched as $g): $c = $g['cinema']; ?>
    <section class="sched-cinema-card">

        <?php if (!$hideCinema): ?>
        <header class="sched-cinema-header">
            <span class="sched-brand-dot" style="background:<?= e($c['color'] ?? '#ff7a59') ?>"></span>
            <div class="sched-cinema-info">
                <h3 class="sched-cinema-name">
                    <a href="/cinemas/<?= e($c['id']) ?>"><?= e($c['name']) ?></a>
                </h3>
                <p class="sched-cinema-address">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    <?= e($c['address']) ?>, <?= e($c['city']) ?>
                </p>
            </div>
        </header>
        <?php endif; ?>

        <div class="sched-movie-list">
            <?php foreach ($g['movies'] as $mv): ?>
            <div class="sched-movie-row">

                <?php if (!$hideMovie): ?>
                <a class="sched-movie-poster-link" href="/movies/<?= e($mv['movie']['id']) ?>" aria-label="Xem chi tiết <?= e($mv['movie']['title']) ?>">
                    <?= img(artUrl('poster', $mv['movie']['id']), 'Poster ' . $mv['movie']['title'], 'sched-movie-thumb') ?>
                </a>
                <?php endif; ?>

                <div class="sched-movie-body">
                    <?php if (!$hideMovie): ?>
                    <div class="sched-movie-meta">
                        <a class="sched-movie-title" href="/movies/<?= e($mv['movie']['id']) ?>">
                            <?= e($mv['movie']['title']) ?>
                        </a>
                        <div class="sched-movie-badges">
                            <?= ageBadge($mv['movie']['age']) ?>
                            <span class="sched-duration">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <?= e(durationText((int)$mv['movie']['duration'])) ?>
                            </span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="sched-times">
                        <?php foreach ($mv['times'] as $t): ?>
                            <?php if ($t['past']): ?>
                                <span class="sched-time-btn is-past" title="Đã qua giờ chiếu">
                                    <span class="sched-time-hour"><?= e($t['show_time']) ?></span>
                                    <span class="sched-time-room">P<?= (int)$t['room'] ?></span>
                                </span>
                            <?php else: ?>
                                <a class="sched-time-btn" href="<?= e(url('/booking/start', ['showtime' => $t['id']])) ?>" title="Phòng <?= (int)$t['room'] ?> · từ <?= e(money((int)$t['price_standard'])) ?>">
                                    <span class="sched-time-hour"><?= e($t['show_time']) ?></span>
                                    <span class="sched-time-room">P<?= (int)$t['room'] ?></span>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
            <?php endforeach; ?>
        </div>

    </section>
<?php endforeach; endif; ?>
