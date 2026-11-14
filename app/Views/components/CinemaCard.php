<?php
/**
 * Thẻ rạp chiếu. @var array $c (id, name, address, brand…)
 * TODO: tách khối thẻ rạp đang viết trực tiếp trong Views/cinemas/index.php sang đây.
 */
?>
<a class="cinema-card" href="/cinemas/<?= e($c['id']) ?>">
    <strong><?= e($c['name']) ?></strong>
    <span class="muted small"><?= e($c['address']) ?></span>
</a>
