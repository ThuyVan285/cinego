<?php
/** @var array $heroes @var array $f @var bool $filtered @var array $all @var array $now @var array $soon @var array $top
 *  @var string $date @var string $brand @var array $brands @var array $quick @var string $cinemaId @var array $promos */
$featured = $heroes[0];
$activeSchedule = $cinemaId !== '' && isset($quick[$cinemaId]) ? $quick[$cinemaId] : null;
?>

<section class="cinema-intro">
    <div class="container cinema-intro-grid">
        <div class="cinema-intro-copy">
            <p class="eyebrow">CineGo Movies · Đà Nẵng</p>
            <h1>Đặt vé xem phim online<br>nhanh và dễ dàng</h1>
            <p class="cinema-intro-lead">Chọn phim hay, rạp gần bạn và ghế ngồi yêu thích chỉ trong vài phút. Lịch chiếu được cập nhật mỗi ngày tại Đà Nẵng.</p>
            <ul class="benefit-list">
                <li><span>✓</span><strong>Đặt vé trực tuyến</strong>, không cần xếp hàng</li>
                <li><span>✓</span>Tự chọn <strong>ghế ngồi và bắp nước</strong> tiện lợi</li>
                <li><span>✓</span>Vé điện tử được <strong>lưu ngay trên trình duyệt</strong></li>
                <li><span>✓</span>Nhiều <strong>mã ưu đãi</strong> dành riêng cho bạn</li>
            </ul>
            <div class="hero-actions">
                <a class="btn btn-primary btn-lg" href="/booking">Đặt vé ngay</a>
                <a class="btn btn-ghost btn-lg" href="/showtimes">Xem lịch chiếu</a>
            </div>
        </div>
        <div class="cinema-showcase" aria-label="Phim nổi bật <?= e($featured['title']) ?>">
            <?= img(artUrl('backdrop', $featured['id']), 'Phim nổi bật ' . $featured['title'], 'cinema-showcase-bg', 'backdrop') ?>
            <div class="cinema-showcase-shade"></div>
            <div class="cinema-showcase-content">
                <span class="showcase-kicker">Phim nổi bật hôm nay</span>
                <h2><?= e($featured['title']) ?></h2>
                <p><span class="<?= e(ageClass($featured['age'])) ?>"><?= e($featured['age']) ?></span> <?= e(implode(' · ', $featured['genres'])) ?> · <?= (int)$featured['duration'] ?> phút</p>
                <a href="<?= e(url('/booking', ['movie' => $featured['id']])) ?>">Mua vé phim này →</a>
            </div>
            <div class="floating-ticket" aria-hidden="true">
                <span>Ưu đãi CineGo</span>
                <strong>GIẢM 10%</strong>
                <small>Mã CINEGO10</small>
            </div>
        </div>
    </div>
</section>

<?php if (!$filtered): ?>
<section class="cinema-now-section">
    <div class="container">
        <div class="cinema-section-title">
            <span class="film-line"></span>
            <h2>Phim đang chiếu</h2>
            <a href="/movies?status=now">Xem tất cả →</a>
        </div>
        <div class="movie-grid movie-grid-showcase">
            <?php foreach (array_slice($now, 0, 5) as $i => $m): ?>
                <article class="ranked-movie-card">
                    <a class="ranked-poster" href="/movies/<?= e($m['id']) ?>" aria-label="Xem chi tiết <?= e($m['title']) ?>">
                        <?= img(artUrl('poster', $m['id']), 'Poster ' . $m['title']) ?>
                        <span class="<?= e(ageClass($m['age'])) ?>"><?= e($m['age']) ?></span>
                        <span class="poster-play" aria-hidden="true"><svg viewBox="0 0 24 24" width="28" height="28"><path d="M9 6l9 6-9 6z" fill="currentColor"/></svg></span>
                        <span class="movie-rank" aria-hidden="true"><?= $i + 1 ?></span>
                    </a>
                    <div class="ranked-movie-info">
                        <h3><a href="/movies/<?= e($m['id']) ?>"><?= e($m['title']) ?></a></h3>
                        <p><?= e(implode(', ', $m['genres'])) ?></p>
                        <strong><span>★</span> <?= number_format($m['rating'], 1) ?></strong>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <a class="showcase-next" href="/movies?status=now" aria-label="Xem thêm phim đang chiếu">›</a>
    </div>
    <div class="theater-seats" aria-hidden="true"></div>
</section>
<?php endif; ?>

<?php if (!$filtered): ?>
<section class="upcoming-movie-section">
    <div class="container">
        <h2>Phim sắp chiếu</h2>
        <div class="upcoming-carousel">
            <div class="upcoming-grid">
                <?php foreach ($soon as $m): ?>
                    <article class="upcoming-movie-card">
                        <a class="upcoming-poster" href="/movies/<?= e($m['id']) ?>" aria-label="Xem chi tiết <?= e($m['title']) ?>">
                            <?= img(artUrl('poster', $m['id']), 'Poster ' . $m['title']) ?>
                            <span class="<?= e(ageClass($m['age'])) ?>"><?= e($m['age']) ?></span>
                            <span class="poster-play" aria-hidden="true"><svg viewBox="0 0 24 24" width="28" height="28"><path d="M9 6l9 6-9 6z" fill="currentColor"/></svg></span>
                        </a>
                        <div class="upcoming-movie-info">
                            <h3><a href="/movies/<?= e($m['id']) ?>"><?= e($m['title']) ?></a></h3>
                            <p><?= e(implode(', ', $m['genres'])) ?></p>
                            <strong><span>◆</span> Khởi chiếu <?= e(date('d/m', strtotime($m['release']))) ?></strong>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <a class="upcoming-next" href="/movies?status=soon" aria-label="Xem thêm phim sắp chiếu">›</a>
        </div>
        <div class="upcoming-actions"><a class="btn btn-primary btn-lg" href="/movies">Tìm phim chiếu rạp</a></div>
    </div>
</section>
<?php endif; ?>

<div class="container home-content">
    <section class="section movie-search-section" id="phim">
        <div class="section-head"><div><p class="section-kicker">Khám phá</p><h2>Tìm bộ phim dành cho bạn</h2></div></div>
        <?php partial('movie_filter', ['f' => $f, 'action' => '/', 'anchor' => 'phim']); ?>

        <?php if ($filtered): ?>
            <h3 class="sub-head">Kết quả (<?= count($all) ?>)</h3>
            <?php if (!$all): ?>
                <div class="empty"><strong>Không tìm thấy phim phù hợp</strong><span>Hãy thử từ khoá khác hoặc xoá bộ lọc.</span><a class="btn btn-ghost" href="/#phim">Xoá bộ lọc</a></div>
            <?php else: ?>
                <div class="movie-grid"><?php foreach ($all as $m) { partial('movie_card', ['m' => $m]); } ?></div>
            <?php endif; ?>
        <?php else: ?>
            <div class="section-head"><div><p class="section-kicker">Được yêu thích</p><h2>Phim nổi bật</h2></div><a href="/movies?sort=rating_desc">Điểm cao nhất →</a></div>
            <div class="movie-grid"><?php foreach (array_slice($top, 0, 5) as $m) { partial('movie_card', ['m' => $m]); } ?></div>

        <?php endif; ?>
    </section>

    <section class="section cinema-schedule-section" id="lich-chieu-nhanh">
        <h2 class="cinema-schedule-title">Lịch chiếu phim</h2>
        <div class="cinema-schedule-shell">
            <div class="schedule-toolbar">
                <div class="schedule-place"><span>Vị trí</span><strong>⌖ Đà Nẵng</strong></div>
                <a class="nearby-btn" href="/cinemas">◎ Rạp gần bạn</a>
            </div>
            <div class="brand-rail" role="tablist" aria-label="Hệ thống rạp">
                <?php foreach ($brands as $id => $b): ?>
                    <a class="brand-rail-item <?= $id === $brand ? 'is-active' : '' ?>" href="<?= e(url('/', ['date' => $date, 'brand' => $id]) . '#lich-chieu-nhanh') ?>">
                        <span class="brand-logo-box" style="--brand:<?= e($b['color']) ?>"><?= e(strtoupper(substr($id, 0, 3))) ?></span>
                        <small><?= e($b['name']) ?></small>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if (!$activeSchedule): ?>
                <div class="empty schedule-empty"><strong>Chưa có lịch chiếu</strong><span>Hệ thống rạp này chưa có suất chiếu trong ngày đã chọn.</span></div>
            <?php else: ?>
                <div class="schedule-browser">
                    <aside class="cinema-sidebar">
                        <form class="cinema-search" action="/cinemas" method="get">
                            <input type="search" name="q" placeholder="Tìm theo tên rạp..." aria-label="Tìm theo tên rạp">
                            <button type="submit" aria-label="Tìm rạp">⌕</button>
                        </form>
                        <div class="cinema-list">
                            <?php foreach ($quick as $cid => $group): $cinema = $group['cinema']; ?>
                                <a class="cinema-list-item <?= $cid === $cinemaId ? 'is-active' : '' ?>" href="<?= e(url('/', ['date' => $date, 'brand' => $brand, 'cinema' => $cid]) . '#lich-chieu-nhanh') ?>">
                                    <span class="cinema-brand-mark" style="--brand:<?= e($cinema['color']) ?>"><?= e(strtoupper(substr($cinema['brand'], 0, 3))) ?></span>
                                    <span><?= e($cinema['name']) ?></span><b>›</b>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <a class="cinema-more" href="<?= e(url('/cinemas', ['brand' => $brand])) ?>">Xem thêm</a>
                    </aside>

                    <div class="cinema-schedule-main">
                        <?php $activeCinema = $activeSchedule['cinema']; ?>
                        <header class="active-cinema-head">
                            <span class="cinema-brand-mark" style="--brand:<?= e($activeCinema['color']) ?>"><?= e(strtoupper(substr($activeCinema['brand'], 0, 3))) ?></span>
                            <div><h3>Lịch chiếu phim <?= e($activeCinema['name']) ?></h3><p><?= e($activeCinema['address']) ?>, <?= e($activeCinema['city']) ?></p></div>
                        </header>
                        <?php partial('date_strip', ['date' => $date, 'base' => '/', 'params' => ['brand' => $brand, 'cinema' => $cinemaId]]); ?>
                        <div class="schedule-movie-list">
                            <?php foreach (array_slice($activeSchedule['movies'], 0, 3, true) as $mv): $movieDetails = Movie::find($mv['movie']['id']); ?>
                                <article class="schedule-movie-row">
                                    <a href="/movies/<?= e($mv['movie']['id']) ?>" class="schedule-movie-poster"><?= img(artUrl('poster', $mv['movie']['id']), 'Poster ' . $mv['movie']['title']) ?></a>
                                    <div class="schedule-movie-body">
                                        <span class="<?= e(ageClass($mv['movie']['age'])) ?>"><?= e($mv['movie']['age']) ?></span>
                                        <h3><a href="/movies/<?= e($mv['movie']['id']) ?>"><?= e($mv['movie']['title']) ?></a></h3>
                                        <p><?= e($movieDetails ? implode(', ', $movieDetails['genres']) : '') ?> · <?= (int)$mv['movie']['duration'] ?> phút</p>
                                        <strong>2D Phụ đề</strong>
                                        <div class="schedule-time-list">
                                            <?php foreach ($mv['times'] as $time): $end = date('H:i', strtotime($time['show_time']) + $mv['movie']['duration'] * 60); ?>
                                                <?php if ($time['past']): ?>
                                                    <span class="schedule-time is-past"><?= e($time['show_time']) ?> <small>~ <?= e($end) ?></small></span>
                                                <?php else: ?>
                                                    <a class="schedule-time" href="<?= e(url('/booking', ['showtime' => $time['id']])) ?>"><?= e($time['show_time']) ?> <small>~ <?= e($end) ?></small></a>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="schedule-all"><a class="btn btn-primary btn-lg" href="<?= e(url('/showtimes', ['date' => $date, 'brand' => $brand])) ?>">Xem tất cả</a></div>
    </section>

    <section class="section" id="uu-dai">
        <div class="section-head"><div><p class="section-kicker">Dành riêng cho bạn</p><h2>Ưu đãi nổi bật</h2></div><a href="/promotions">Tất cả ưu đãi →</a></div>
        <div class="promo-grid">
            <?php foreach ($promos as $p) { partial('promo_card', ['p' => $p]); } ?>
        </div>
    </section>
</div>
