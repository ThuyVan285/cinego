<?php
/**
 * Một nút suất chiếu. @var array $st (id, show_time…)
 * TODO: dùng trong component Schedule thay cho phần lặp nút giờ chiếu.
 */
?>
<a class="time-chip" href="<?= e(url('/booking/start', ['showtime' => $st['id']])) ?>"><?= e($st['show_time']) ?></a>
