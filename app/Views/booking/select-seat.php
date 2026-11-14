<?php
/** @var array $sum @var array $status @var array $selected @var array $rows @var bool $hasCouple */
$st = $sum['showtime'];
$prices = ['standard' => (int)$st['price_standard'], 'vip' => (int)$st['price_vip'], 'couple' => (int)$st['price_couple']];
$stateLabel = ['booked' => ' đã đặt', 'held' => ' đang được giữ', 'blocked' => ' tạm ngưng'];
?>
<div class="container">
    <?php component('Steps', ['step' => 2]); ?>
    <p class="crumbs"><a href="<?= e(url('/booking', ['movie' => $st['movie_id'], 'date' => $st['show_date']])) ?>">← Đổi suất chiếu</a></p>
    <form method="post" action="/booking/seats" class="booking-layout" id="seat-form"
          data-max="<?= MAX_SEATS ?>" data-prices='<?= e(json_encode($prices)) ?>'
          data-status-url="<?= e('/api/showtimes/' . rawurlencode($st['id']) . '/seats') ?>">
        <?= csrfField() ?>
        <input type="hidden" name="seats" id="seats-input" value="<?= e(implode(',', $selected)) ?>">
        <section class="card pad">
            <h2>Chọn ghế · <?= e($st['room_name']) ?></h2>
            <p class="muted small">
                Tối đa <?= MAX_SEATS ?> ghế mỗi lượt. Khi bấm tiếp tục, ghế được giữ cho bạn trong <?= SeatService::holdMinutes() ?> phút.
                Sơ đồ tự cập nhật khi có người khác chọn hoặc đặt ghế.
            </p>
            <ul class="legend">
                <li><i class="seat-sample standard"></i>Thường <?= e(money($prices['standard'])) ?></li>
                <li><i class="seat-sample vip"></i>VIP <?= e(money($prices['vip'])) ?></li>
                <?php if ($hasCouple): ?><li><i class="seat-sample couple"></i>Ghế đôi <?= e(money($prices['couple'])) ?></li><?php endif; ?>
                <li><i class="seat-sample is-selected"></i>Đang chọn</li>
                <li><i class="seat-sample is-held"></i>Đang được giữ</li>
                <li><i class="seat-sample is-booked"></i>Đã đặt</li>
            </ul>
            <div class="seat-scroll">
                <div class="seatmap" role="group" aria-label="Sơ đồ ghế <?= e($st['room_name']) ?>">
                    <div class="screen"><span>MÀN HÌNH</span></div>
                    <?php foreach ($rows as $row => $seats):
                        $half = intdiv(count($seats), 2); ?>
                        <div class="seat-row">
                            <span class="row-label"><?= e($row) ?></span>
                            <?php foreach ($seats as $seat):
                                $code = $seat['code'];
                                $type = $seat['type'];
                                $s = $status[$code] ?? 'available';
                                $sel = in_array($code, $selected, true) && in_array($s, ['available', 'mine'], true);
                                $blocked = in_array($s, ['booked', 'held', 'blocked'], true);
                                $cls = 'seat ' . $type . ($blocked ? ' is-' . $s : '') . ($sel ? ' is-selected' : '')
                                    . ((int)$seat['col_no'] === $half && $type !== 'couple' ? ' gap' : ''); ?>
                                <button type="button" class="<?= $cls ?>" data-code="<?= e($code) ?>" data-type="<?= e($type) ?>"
                                        aria-pressed="<?= $sel ? 'true' : 'false' ?>"
                                        aria-label="Ghế <?= e($code) ?> <?= Seat::TYPE_LABEL[$type] ?><?= $stateLabel[$s] ?? '' ?>"
                                        <?= $blocked ? 'aria-disabled="true"' : '' ?>><?= (int)$seat['col_no'] ?></button>
                            <?php endforeach; ?>
                            <span class="row-label"><?= e($row) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <p class="seat-error" id="seat-error" role="alert" hidden></p>
        </section>

        <div class="side-col">
            <?php component('BookingSummary', ['sum' => $sum]); ?>
            <div class="action-row">
                <a class="btn btn-ghost" href="<?= e(url('/booking', ['movie' => $st['movie_id'], 'date' => $st['show_date']])) ?>">Quay lại</a>
                <button class="btn btn-primary" type="submit">Tiếp tục: Bắp nước</button>
            </div>
        </div>
    </form>
</div>
