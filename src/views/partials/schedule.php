<?php
/**
 * @var array $sched      kết quả Showtime::schedule()
 * @var bool  $hideMovie  ẩn tên phim (khi đang ở trang chi tiết phim)
 * @var bool  $hideCinema ẩn tên rạp (khi đang ở trang chi tiết rạp)
 * @var string $empty     thông báo khi rỗng
 */
$hideMovie = $hideMovie ?? false;
$hideCinema = $hideCinema ?? false;
$empty = $empty ?? 'Không có suất chiếu phù hợp trong ngày đã chọn.';
?>
<?php if (!$sched): ?>
    <div class="empty"><strong>Không có suất chiếu</strong><span><?= e($empty) ?></span></div>
<?php else: foreach ($sched as $g): $c = $g['cinema']; ?>
    <section class="sched-cinema">
        <?php if (!$hideCinema): ?>
            <header>
                <span class="brand-dot" style="background:<?= e($c['color']) ?>"></span>
                <div>
                    <h3><a href="/cinemas/<?= e($c['id']) ?>"><?= e($c['name']) ?></a></h3>
                    <p class="muted small"><?= e($c['address']) ?>, <?= e($c['city']) ?></p>
                </div>
            </header>
        <?php endif; ?>
        <?php foreach ($g['movies'] as $mv): ?>
            <div class="sched-movie">
                <?php if (!$hideMovie): ?>
                    <a class="sched-title" href="/movies/<?= e($mv['movie']['id']) ?>"><?= e($mv['movie']['title']) ?>
                        <span class="<?= e(ageClass($mv['movie']['age'])) ?>"><?= e($mv['movie']['age']) ?></span>
                        <small class="muted"><?= (int)$mv['movie']['duration'] ?> phút</small></a>
                <?php endif; ?>
                <div class="times">
                    <?php foreach ($mv['times'] as $t): ?>
                        <?php if ($t['past']): ?>
                            <span class="time is-past" title="Đã qua giờ chiếu"><?= e($t['show_time']) ?><small>P<?= (int)$t['room'] ?></small></span>
                        <?php else: ?>
                            <a class="time" href="<?= e(url('/booking/start', ['showtime' => $t['id']])) ?>" title="Phòng <?= (int)$t['room'] ?> · từ <?= e(money((int)$t['price_standard'])) ?>"><?= e($t['show_time']) ?><small>P<?= (int)$t['room'] ?></small></a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </section>
<?php endforeach; endif; ?>
