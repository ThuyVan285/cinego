<?php /** @var array $f @var array $movies @var int $total @var int $page @var int $pages */ ?>
<div class="container">
    <div class="page-head"><h1>Phim</h1><p class="muted"><?= $total ?> phim</p></div>
    <?php partial('movie_filter', ['f' => $f, 'action' => '/movies']); ?>
    <?php if (!$movies): ?>
        <div class="empty"><strong>Không tìm thấy phim phù hợp</strong><span>Hãy thử từ khoá khác hoặc xoá bộ lọc.</span><a class="btn btn-ghost" href="/movies">Xoá bộ lọc</a></div>
    <?php else: ?>
        <div class="movie-grid"><?php foreach ($movies as $m) { partial('movie_card', ['m' => $m]); } ?></div>
        <?php if ($pages > 1): ?>
            <nav class="pagination" aria-label="Phân trang">
                <?php if ($page > 1): ?><a class="btn btn-ghost btn-sm" href="<?= e(url('/movies', $f + ['page' => $page - 1])) ?>">← Trước</a><?php endif; ?>
                <?php for ($i = 1; $i <= $pages; $i++): ?>
                    <a class="page-num <?= $i === $page ? 'is-active' : '' ?>" href="<?= e(url('/movies', $f + ['page' => $i])) ?>" <?= $i === $page ? 'aria-current="page"' : '' ?>><?= $i ?></a>
                <?php endfor; ?>
                <?php if ($page < $pages): ?><a class="btn btn-ghost btn-sm" href="<?= e(url('/movies', $f + ['page' => $page + 1])) ?>">Sau →</a><?php endif; ?>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>
