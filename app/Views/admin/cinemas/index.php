<?php /** @var array $rows @var string $heading */ ?>
<div class="admin-head">
    <h1><?= e($heading) ?> <small><?= count($rows) ?></small></h1>
    <a class="btn btn-primary btn-sm" href="/admin/cinemas/create"><?= Icon::svg('plus', 16) ?> Thêm rạp</a>
</div>
<div class="table-wrap">
<table class="admin-table">
    <thead><tr><th>Rạp</th><th>Khu vực</th><th>Địa chỉ</th><th>Phòng</th><th>Suất sắp tới</th><th>Đơn</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($rows as $c): ?>
        <tr>
            <td><span class="brand-dot" style="background:<?= e($c['color']) ?>"></span><b><?= e($c['name']) ?></b><div class="muted small"><?= e($c['brand_name']) ?> · <?= e($c['id']) ?></div></td>
            <td><?= e($c['area']) ?>, <?= e($c['city']) ?></td>
            <td class="small"><?= e($c['address']) ?></td>
            <td><a href="/admin/rooms?cinema=<?= e(rawurlencode($c['id'])) ?>"><?= (int)$c['room_count'] ?> phòng</a></td>
            <td><?= (int)$c['upcoming_shows'] ?></td>
            <td><?= (int)$c['bookings'] ?></td>
            <td class="actions">
                <a class="row-btn" href="/admin/cinemas/<?= e($c['id']) ?>/edit" title="Sửa" aria-label="Sửa"><?= Icon::svg('edit', 17) ?></a>
                <a class="row-btn" href="/admin/rooms?cinema=<?= e(rawurlencode($c['id'])) ?>" title="Phòng & ghế" aria-label="Phòng & ghế"><?= Icon::svg('armchair', 17) ?></a>
                <form method="post" action="/admin/cinemas/<?= e($c['id']) ?>/delete" class="inline-form"
                      data-confirm="Xoá rạp &quot;<?= e($c['name']) ?>&quot; cùng toàn bộ phòng, ghế và suất chiếu? Không thể hoàn tác.">
                    <?= csrfField() ?><button class="row-btn danger" title="Xoá" aria-label="Xoá"><?= Icon::svg('trash', 17) ?></button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
