<?php /** @var array $s @var int $bookings @var array $errors @var array $movies @var array $rooms @var string $heading */
$err = fn(string $k) => isset($errors[$k]) ? '<span class="field-error">' . e($errors[$k]) . '</span>' : '';
$locked = $bookings > 0;
$byCinema = [];
foreach ($rooms as $r) {
    $byCinema[$r['cinema_name']][] = $r;
} ?>
<div class="admin-head">
    <h1><?= e($heading) ?></h1>
    <a class="btn btn-ghost btn-sm" href="/admin/showtimes"><?= Icon::svg('arrow-left', 16) ?> Danh sách suất chiếu</a>
</div>
<?php if ($locked): ?>
    <p class="notice notice-info">Suất này đã có <?= $bookings ?> đơn đặt vé nên chỉ đổi được giá vé (áp dụng cho lượt đặt mới). Phim, phòng và giờ chiếu được giữ nguyên.</p>
<?php endif; ?>
<form method="post" action="/admin/showtimes/save" class="admin-form">
    <?= csrfField() ?>
    <input type="hidden" name="id" value="<?= e($s['id']) ?>">
    <div class="form-grid">
        <label class="span-2">Phim *
            <select name="movie_id" required <?= $locked ? 'disabled' : '' ?>>
                <option value="">— Chọn phim —</option>
                <?php foreach ($movies as $m): ?>
                    <option value="<?= e($m['id']) ?>" <?= $s['movie_id'] === $m['id'] ? 'selected' : '' ?>><?= e($m['title']) ?> (<?= e(durationText((int)$m['duration'])) ?><?= $m['status'] === 'soon' ? ', sắp chiếu' : '' ?>)</option>
                <?php endforeach; ?>
            </select><?= $err('movie_id') ?>
        </label>
        <label class="span-2">Rạp · phòng chiếu *
            <select name="room_id" required <?= $locked ? 'disabled' : '' ?>>
                <option value="">— Chọn phòng —</option>
                <?php foreach ($byCinema as $cinemaName => $list): ?>
                    <optgroup label="<?= e($cinemaName) ?>">
                        <?php foreach ($list as $r): ?>
                            <option value="<?= (int)$r['id'] ?>" <?= (int)$s['room_id'] === (int)$r['id'] || (!$s['room_id'] && $s['cinema_id'] === $r['cinema_id'] && $r === $list[0]) ? 'selected' : '' ?>><?= e($r['name']) ?> (<?= e($r['format']) ?>)</option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endforeach; ?>
            </select><?= $err('room_id') ?>
        </label>
        <label>Ngày chiếu *<input type="date" name="show_date" value="<?= e($s['show_date']) ?>" min="<?= $locked ? '' : today() ?>" required <?= $locked ? 'disabled' : '' ?>><?= $err('show_date') ?></label>
        <label>Giờ chiếu *<input type="time" name="show_time" value="<?= e($s['show_time']) ?>" required <?= $locked ? 'disabled' : '' ?>><?= $err('show_time') ?>
            <small class="muted">Kiểm tra trùng giờ trong cùng phòng (thời lượng phim + <?= Admin\ShowtimeController::CLEANUP_MINUTES ?> phút dọn phòng).</small></label>
        <?php foreach (['price_standard' => 'Giá ghế thường', 'price_vip' => 'Giá ghế VIP', 'price_couple' => 'Giá ghế đôi'] as $k => $label): ?>
            <label><?= $label ?> (đ)<input type="number" name="<?= $k ?>" value="<?= (int)$s[$k] ?>" min="10000" step="1000" required><?= $err($k) ?></label>
        <?php endforeach; ?>
    </div>
    <div class="form-actions">
        <button class="btn btn-primary"><?= $s['id'] ? 'Lưu thay đổi' : 'Thêm suất chiếu' ?></button>
        <a class="btn btn-ghost" href="/admin/showtimes">Huỷ</a>
    </div>
</form>
