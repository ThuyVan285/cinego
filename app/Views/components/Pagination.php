<?php
/**
 * Phân trang dạng cửa sổ: ‹ 1 … 4 5 [6] 7 8 … 90 ›
 * @var int $page @var int $pages @var string $path @var array $query (tham số giữ lại) @var ?int $total (tuỳ chọn, để hiện "x kết quả")
 */
if ($pages <= 1) return;
$link = fn(int $p) => url($path, $query + ['page' => $p]);
$nums = array_unique(array_filter([1, $page - 2, $page - 1, $page, $page + 1, $page + 2, $pages], fn($p) => $p >= 1 && $p <= $pages));
sort($nums);
$prevIcon = class_exists('Icon') ? Icon::svg('chevron-left', 16) : '‹';
$nextIcon = class_exists('Icon') ? Icon::svg('chevron-right', 16) : '›';
?>
<nav class="pagination" aria-label="Phân trang">
    <a href="<?= e($link(max(1, $page - 1))) ?>" class="<?= $page <= 1 ? 'is-disabled' : '' ?>" aria-label="Trang trước"><?= $prevIcon ?></a>
    <?php $last = 0; foreach ($nums as $p): ?>
        <?php if ($p - $last > 1): ?><span>…</span><?php endif; ?>
        <a href="<?= e($link($p)) ?>" class="<?= $p === $page ? 'is-active' : '' ?>" <?= $p === $page ? 'aria-current="page"' : '' ?>><?= $p ?></a>
        <?php $last = $p; endforeach; ?>
    <a href="<?= e($link(min($pages, $page + 1))) ?>" class="<?= $page >= $pages ? 'is-disabled' : '' ?>" aria-label="Trang sau"><?= $nextIcon ?></a>
    <span class="pagination-info">Trang <?= $page ?> / <?= $pages ?><?= isset($total) ? ' · ' . number_format((int)$total, 0, ',', '.') . ' kết quả' : '' ?></span>
</nav>
