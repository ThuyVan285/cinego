<?php /** @var array $rows @var array $f @var array $pg @var array $cinemas @var array $movies @var bool $isAdmin @var string $heading */ ?>
<div class="admin-head">
    <h1><?= e($heading) ?> <small><?= e(dateVi($f['date'])) ?> · <?= $pg['total'] ?> suất</small></h1>
    <?php if ($isAdmin): ?>
        <div class="actions">
            <a class="btn btn-primary btn-sm" href="<?= e(url('/admin/showtimes/create', ['date' => $f['date'], 'cinema' => $f['cinema'], 'movie' => $f['movie']])) ?>"><?= Icon::svg('plus', 16) ?> Thêm suất chiếu</a>
            <form method="post" action="/admin/showtimes/generate" class="inline-form" data-confirm="Tự sinh thêm suất chiếu 7 ngày tới cho các phim đang chiếu? Suất đã có được giữ nguyên.">
                <?= csrfField() ?><button class="btn btn-ghost btn-sm">Tự sinh lịch 7 ngày</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<form class="admin-filters" method="get">
    <input type="date" name="date" value="<?= e($f['date']) ?>">
    <select name="cinema"><option value="">Mọi rạp</option><?php foreach ($cinemas as $c): ?><option value="<?= e($c['id']) ?>" <?= $f['cinema'] === $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select>
    <select name="movie"><option value="">Mọi phim</option><?php foreach ($movies as $m): ?><option value="<?= e($m['id']) ?>" <?= $f['movie'] === $m['id'] ? 'selected' : '' ?>><?= e($m['title']) ?></option><?php endforeach; ?></select>
    <button class="btn btn-ghost btn-sm"><?= Icon::svg('filter', 15) ?> Lọc</button>
    <a class="btn btn-ghost btn-sm" href="<?= e(url('/admin/showtimes', ['date' => addDays($f['date'], -1), 'cinema' => $f['cinema'], 'movie' => $f['movie']])) ?>"><?= Icon::svg('chevron-left', 16) ?> Hôm trước</a>
    <a class="btn btn-ghost btn-sm" href="<?= e(url('/admin/showtimes', ['date' => addDays($f['date'], 1), 'cinema' => $f['cinema'], 'movie' => $f['movie']])) ?>">Hôm sau <?= Icon::svg('chevron-right', 16) ?></a>
</form>

<div class="table-wrap">
<table class="admin-table">
    <thead><tr><th>Giờ</th><th>Phim</th><th>Rạp · Phòng</th><th>Giá (thường / VIP / đôi)</th><th>Lấp đầy</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($rows as $s):
        $pct = $s['capacity'] ? round($s['sold'] * 100 / $s['capacity']) : 0;
        $past = strtotime($s['show_date'] . ' ' . $s['show_time']) <= time(); ?>
        <tr class="<?= $past ? 'is-past' : '' ?>">
            <td><b><?= e($s['show_time']) ?></b><?= $past ? '<div class="muted small">đã chiếu</div>' : '' ?></td>
            <td><?= e($s['title']) ?><div class="muted small"><?= e(durationText((int)$s['duration'])) ?></div></td>
            <td><?= e($s['cinema_name']) ?><div class="muted small"><?= e($s['room_name']) ?></div></td>
            <td class="small"><?= e(money((int)$s['price_standard'])) ?> / <?= e(money((int)$s['price_vip'])) ?> / <?= e(money((int)$s['price_couple'])) ?></td>
            <td>
                <div class="meter" title="<?= (int)$s['sold'] ?>/<?= (int)$s['capacity'] ?> ghế đã bán"><span style="width:<?= $pct ?>%"></span></div>
                <div class="small"><?= (int)$s['sold'] ?>/<?= (int)$s['capacity'] ?> đã bán<?= (int)$s['held'] ? ' · ' . (int)$s['held'] . ' đang giữ' : '' ?></div>
            </td>
            <td class="actions">
                <a class="row-btn primary" href="/admin/showtimes/<?= e($s['id']) ?>/seats" title="Ghế" aria-label="Ghế"><?= Icon::svg('armchair', 17) ?></a>
                <?php if ($isAdmin): ?>
                    <a class="row-btn" href="/admin/showtimes/<?= e($s['id']) ?>/edit" title="Sửa" aria-label="Sửa"><?= Icon::svg('edit', 17) ?></a>
                    <form method="post" action="/admin/showtimes/<?= e($s['id']) ?>/delete" class="inline-form" data-confirm="Xoá suất <?= e($s['show_time']) ?> – <?= e($s['title']) ?>?">
                        <?= csrfField() ?><button class="row-btn danger" aria-label="Xoá" <?= $s['booking_count'] ? 'disabled title="Suất đã có đơn đặt vé"' : 'title="Xoá"' ?>><?= Icon::svg('trash', 17) ?></button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$rows): ?><tr><td colspan="6" class="empty-state"><?= Icon::svg('search', 28) ?>Không có suất chiếu nào.</td></tr><?php endif; ?>
    </tbody>
</table>
</div>
<?php component('Pagination', ['page' => $pg['page'], 'pages' => $pg['pages'], 'path' => '/admin/showtimes', 'query' => array_filter($f), 'total' => $pg['total']]); ?>
