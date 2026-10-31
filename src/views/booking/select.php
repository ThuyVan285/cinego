<?php /** @var array $movies @var ?array $movie @var string $date @var array $sched @var ?array $current */
$base = '/booking'; ?>
<div class="container">
    <div class="page-head"><h1>Đặt vé</h1><p class="muted">Chọn phim, ngày, rạp và suất chiếu để bắt đầu.</p></div>
    <?php partial('steps', ['step' => 1]); ?>

    <?php if ($current): ?>
        <div class="notice notice-info">Bạn đang đặt dở: <b><?= e($current['showtime']['movie_title']) ?></b> lúc <?= e($current['showtime']['show_time']) ?> ngày <?= e(date('d/m', strtotime($current['showtime']['show_date']))) ?>.
            <a href="/booking/seats">Tiếp tục →</a></div>
    <?php endif; ?>

    <div class="card pad">
        <form method="get" action="/booking" class="inline-filter" data-autosubmit>
            <input type="hidden" name="date" value="<?= e($date) ?>">
            <label class="field grow"><span>1. Chọn phim</span>
                <select name="movie">
                    <option value="">— Chọn phim đang chiếu —</option>
                    <?php foreach ($movies as $m): ?><option value="<?= e($m['id']) ?>" <?= $movie && $movie['id'] === $m['id'] ? 'selected' : '' ?>><?= e($m['title']) ?> (<?= e($m['age']) ?>)</option><?php endforeach; ?>
                </select></label>
            <noscript><button class="btn btn-primary" type="submit">Chọn</button></noscript>
        </form>

        <?php if (!$movie): ?>
            <div class="empty"><strong>Chưa chọn phim</strong><span>Hãy chọn một phim để xem các suất chiếu có thể đặt.</span></div>
        <?php elseif ($movie['status'] === 'soon'): ?>
            <div class="empty"><strong>Phim chưa mở bán vé</strong><span><?= e($movie['title']) ?> khởi chiếu ngày <?= e(date('d/m/Y', strtotime($movie['release']))) ?>.</span></div>
        <?php else: ?>
            <p class="step-label">2. Chọn ngày, rạp và suất chiếu – <b><?= e($movie['title']) ?></b></p>
            <?php partial('date_strip', ['date' => $date, 'base' => '/booking', 'params' => ['movie' => $movie['id']]]); ?>
            <?php partial('schedule', ['sched' => $sched, 'hideMovie' => true, 'empty' => 'Phim chưa có suất chiếu vào ngày này. Hãy chọn ngày khác.']); ?>
        <?php endif; ?>
    </div>
</div>
