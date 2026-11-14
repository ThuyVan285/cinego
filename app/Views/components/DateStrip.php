<?php
/** @var string $date */
/** @var string $base */
/** @var array $params */
$params = $params ?? [];
?>
<nav class="date-strip" aria-label="Chọn ngày">
    <?php for ($i = 0; $i < 7; $i++):
        $d = addDays(today(), $i); ?>
        <a href="<?= e(url($base, array_merge($params, ['date' => $d]))) ?>" class="date-chip <?= $d === $date ? 'is-active' : '' ?>" <?= $d === $date ? 'aria-current="date"' : '' ?>>
            <span><?= $i === 0 ? 'Hôm nay' : weekdayVi($d, true) ?></span>
            <strong><?= e(date('d/m', strtotime($d))) ?></strong>
        </a>
    <?php endfor; ?>
</nav>
