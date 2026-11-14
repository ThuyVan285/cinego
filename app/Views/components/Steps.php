<?php /** @var int $step */
$labels = ['Suất chiếu', 'Chọn ghế', 'Bắp nước', 'Xác nhận', 'Thanh toán', 'Hoàn tất']; ?>
<ol class="steps" aria-label="Tiến trình đặt vé">
    <?php foreach ($labels as $i => $l): $n = $i + 1; ?>
        <li class="<?= $n < $step ? 'is-done' : ($n === $step ? 'is-current' : '') ?>" <?= $n === $step ? 'aria-current="step"' : '' ?>>
            <span class="num"><?= $n < $step ? '✓' : $n ?></span><span class="lbl"><?= $l ?></span>
        </li>
    <?php endforeach; ?>
</ol>
