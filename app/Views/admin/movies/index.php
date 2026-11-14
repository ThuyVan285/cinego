<?php /** @var array $rows @var array $f @var array $pg @var string[] $genres @var string $heading */
use Admin\MovieController as MC; ?>
<div class="admin-head">
    <h1><?= e($heading) ?> <small><?= $pg['total'] ?></small></h1>
    <a class="btn btn-primary btn-sm" href="/admin/movies/create"><?= Icon::svg('plus', 16) ?> Thêm phim</a>
</div>

<form class="admin-filters" method="get">
    <input type="search" name="q" value="<?= e($f['q']) ?>" placeholder="Tên phim, đạo diễn hoặc mã phim">
    <select name="status">
        <option value="">Mọi trạng thái</option>
        <?php foreach (MC::STATUS as $k => $v): ?><option value="<?= $k ?>" <?= $f['status'] === $k ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?>
    </select>
    <select name="genre">
        <option value="">Mọi thể loại</option>
        <?php foreach ($genres as $g): ?><option <?= $f['genre'] === $g ? 'selected' : '' ?>><?= e($g) ?></option><?php endforeach; ?>
    </select>
    <button class="btn btn-ghost btn-sm"><?= Icon::svg('filter', 15) ?> Lọc</button>
</form>

<div class="table-wrap">
<table class="admin-table">
    <thead><tr><th></th><th>Phim</th><th>Thể loại</th><th>Khởi chiếu</th><th>Suất sắp tới</th><th>Đơn</th><th>Trạng thái</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($rows as $r): ?>
        <tr>
            <td><img class="thumb" src="<?= e(posterUrl(['id' => $r['id'], 'poster' => $r['poster_url']])) ?>" alt="" loading="lazy"></td>
            <td>
                <a href="/admin/movies/<?= e($r['id']) ?>/edit"><b><?= e($r['title']) ?></b></a>
                <div class="muted small"><?= e($r['id']) ?> · <?= e(durationText((int)$r['duration'])) ?> · <?= ageBadge($r['age']) ?><?= $r['featured'] ? ' · ⭐ nổi bật' : '' ?></div>
            </td>
            <td class="small"><?= e($r['genres']) ?></td>
            <td><?= e(date('d/m/Y', strtotime($r['release_date']))) ?></td>
            <td><?= (int)$r['upcoming_shows'] ?></td>
            <td><?= (int)$r['bookings'] ?></td>
            <td>
                <form method="post" action="/admin/movies/<?= e($r['id']) ?>/status" class="inline-form">
                    <?= csrfField() ?>
                    <select name="status" data-autosubmit-select class="status-select status-<?= e($r['status']) ?>">
                        <?php foreach (MC::STATUS as $k => $v): ?><option value="<?= $k ?>" <?= $r['status'] === $k ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?>
                    </select>
                </form>
            </td>
            <td class="actions">
                <a class="row-btn" href="/admin/movies/<?= e($r['id']) ?>/edit" title="Sửa" aria-label="Sửa"><?= Icon::svg('edit', 17) ?></a>
                <a class="row-btn" href="/movies/<?= e($r['id']) ?>" target="_blank" title="Xem" aria-label="Xem"><?= Icon::svg('eye', 17) ?></a>
                <form method="post" action="/admin/movies/<?= e($r['id']) ?>/delete" class="inline-form"
                      data-confirm="Xoá phim &quot;<?= e($r['title']) ?>&quot; và mọi suất chiếu của phim? Không thể hoàn tác.">
                    <?= csrfField() ?><button class="row-btn danger" title="Xoá" aria-label="Xoá"><?= Icon::svg('trash', 17) ?></button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$rows): ?><tr><td colspan="8" class="empty-state"><?= Icon::svg('search', 28) ?>Không có phim phù hợp.</td></tr><?php endif; ?>
    </tbody>
</table>
</div>
<?php component('Pagination', ['page' => $pg['page'], 'pages' => $pg['pages'], 'path' => '/admin/movies', 'query' => array_filter($f), 'total' => $pg['total']]); ?>
