<?php
/**
 * Một ghế trong sơ đồ. @var string $code @var string $type @var string $status
 * TODO: dùng trong Views/booking/select-seat.php thay cho nút ghế viết trực tiếp.
 */
?>
<button type="button" class="seat seat-<?= e($type) ?> <?= $status !== 'available' ? 'is-booked' : '' ?>" data-code="<?= e($code) ?>" data-type="<?= e($type) ?>"<?= $status !== 'available' ? ' aria-disabled="true"' : '' ?>><?= e($code) ?></button>
