<?php /** @var array $sum @var array $status @var array $selected */
$st = $sum['showtime'];
$prices = ['standard' => (int)$st['price_standard'], 'vip' => (int)$st['price_vip'], 'couple' => (int)$st['price_couple']]; ?>
<div class="container">
    <?php partial('steps', ['step' => 2]); ?>
    <p class="crumbs"><a href="<?= e(url('/booking', ['movie' => $st['movie_id'], 'date' => $st['show_date']])) ?>">← Đổi suất chiếu</a></p>
    <form method="post" action="/booking/seats" class="booking-layout" id="seat-form"
          data-max="<?= MAX_SEATS ?>" data-prices='<?= e(json_encode($prices)) ?>'>
        <?= csrfField() ?>
        <input type="hidden" name="seats" id="seats-input" value="<?= e(implode(',', $selected)) ?>">
        <section class="card pad">
            <h2>Chọn ghế</h2>
            <p class="muted small">Tối đa <?= MAX_SEATS ?> ghế mỗi lượt. Ghế đã đặt hiển thị màu xám.</p>
            <ul class="legend">
                <li><i class="seat-sample standard"></i>Thường <?= e(money($prices['standard'])) ?></li>
                <li><i class="seat-sample vip"></i>VIP <?= e(money($prices['vip'])) ?></li>
                <li><i class="seat-sample couple"></i>Ghế đôi <?= e(money($prices['couple'])) ?></li>
                <li><i class="seat-sample is-selected"></i>Đang chọn</li>
                <li><i class="seat-sample is-booked"></i>Đã đặt</li>
                <li><i class="seat-sample is-blocked"></i>Không khả dụng</li>
            </ul>
            <div class="seat-scroll">
                <div class="seatmap" role="group" aria-label="Sơ đồ ghế">
                    <div class="screen"><span>MÀN HÌNH</span></div>
                    <?php foreach (Seats::ROWS as $row):
                        $type = Seats::typeOfRow($row);
                        $cols = $row === 'I' ? Seats::COUPLE_COLS : Seats::COLS; ?>
                        <div class="seat-row">
                            <span class="row-label"><?= $row ?></span>
                            <?php for ($c = 1; $c <= $cols; $c++):
                                $code = $row . $c;
                                $s = $status[$code];
                                $sel = in_array($code, $selected, true);
                                $cls = 'seat ' . $type . ($s === 'booked' ? ' is-booked' : ($s === 'blocked' ? ' is-blocked' : '')) . ($sel ? ' is-selected' : '') . ($c === 6 && $type !== 'couple' ? ' gap' : ''); ?>
                                <button type="button" class="<?= $cls ?>" data-code="<?= $code ?>" data-type="<?= $type ?>"
                                        aria-pressed="<?= $sel ? 'true' : 'false' ?>"
                                        aria-label="Ghế <?= $code ?> <?= Seats::TYPE_LABEL[$type] ?><?= $s === 'booked' ? ' đã đặt' : ($s === 'blocked' ? ' không khả dụng' : '') ?>"
                                        <?= $s !== 'available' ? 'aria-disabled="true"' : '' ?>><?= $c ?></button>
                            <?php endfor; ?>
                            <span class="row-label"><?= $row ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <p class="seat-error" id="seat-error" role="alert" hidden></p>
        </section>

        <div class="side-col">
            <?php partial('summary', ['sum' => $sum]); ?>
            <div class="action-row">
                <a class="btn btn-ghost" href="<?= e(url('/booking', ['movie' => $st['movie_id'], 'date' => $st['show_date']])) ?>">Quay lại</a>
                <button class="btn btn-primary" type="submit">Tiếp tục: Bắp nước</button>
            </div>
        </div>
    </form>
</div>
