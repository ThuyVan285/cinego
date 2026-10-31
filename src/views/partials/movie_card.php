<?php /** @var array $m */
$soon = $m['status'] === 'soon'; ?>
<article class="movie-card">
    <a class="poster" href="/movies/<?= e($m['id']) ?>" aria-label="Xem chi tiết <?= e($m['title']) ?>">
        <?= img(artUrl('poster', $m['id']), 'Poster ' . $m['title']) ?>
        <span class="<?= e(ageClass($m['age'])) ?>"><?= e($m['age']) ?></span>
        <?php if ($soon): ?><span class="ribbon">Sắp chiếu · <?= e(date('d/m', strtotime($m['release']))) ?></span>
        <?php else: ?><span class="score"><svg viewBox="0 0 24 24" width="12" height="12"><path fill="currentColor" d="M12 2l3 7 7 .6-5.3 4.7 1.6 7.2L12 17.8 5.7 21.5l1.6-7.2L2 9.6 9 9z"/></svg><?= number_format($m['rating'], 1) ?></span><?php endif; ?>
    </a>
    <div class="movie-info">
        <h3><a href="/movies/<?= e($m['id']) ?>"><?= e($m['title']) ?></a></h3>
        <p class="muted small"><?= e(implode(', ', $m['genres'])) ?> · <?= (int)$m['duration'] ?> phút</p>
        <div class="card-actions">
            <a class="btn btn-ghost btn-sm" href="/movies/<?= e($m['id']) ?>">Chi tiết</a>
            <?php if ($soon): ?>
                <a class="btn btn-soft btn-sm" href="/movies/<?= e($m['id']) ?>">Sắp chiếu</a>
            <?php else: ?>
                <a class="btn btn-primary btn-sm" href="<?= e(url('/booking', ['movie' => $m['id']])) ?>">Mua vé</a>
            <?php endif; ?>
        </div>
    </div>
</article>
