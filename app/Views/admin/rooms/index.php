<?php /** @var array $cinemas @var string $cinemaId @var ?array $cinema @var array $rooms @var array $layouts @var array $prices @var string $heading */
use Admin\RoomController as RC; ?>
<div class="admin-head"><h1><?= e($heading) ?></h1></div>

<section class="admin-card">
    <h2>Giá vé mặc định</h2>
    <p class="muted small">Dùng cho suất chiếu mới (tạo tay hoặc tự sinh). Mỗi suất chiếu vẫn có thể đặt giá riêng trong trang Suất chiếu.</p>
    <form method="post" action="/admin/prices" class="admin-filters">
        <?= csrfField() ?>
        <input type="hidden" name="cinema" value="<?= e($cinemaId) ?>">
        <?php foreach (Seat::TYPE_LABEL as $t => $label): ?>
            <label class="inline-label"><?= e($label) ?> <input type="number" name="price_<?= $t ?>" value="<?= (int)$prices[$t] ?>" min="10000" step="1000" required class="input-price"> đ</label>
        <?php endforeach; ?>
        <label class="check"><input type="checkbox" name="apply_future" value="1"> Áp dụng cho các suất sắp tới chưa có vé</label>
        <button class="btn btn-primary btn-sm">Lưu giá vé</button>
    </form>
</section>

<form method="get" class="admin-filters">
    <label class="inline-label">Rạp
        <select name="cinema" data-autosubmit-select>
            <?php foreach ($cinemas as $c): ?><option value="<?= e($c['id']) ?>" <?= $c['id'] === $cinemaId ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?>
        </select>
    </label>
    <noscript><button class="btn btn-ghost btn-sm">Xem</button></noscript>
</form>

<?php if ($cinema): ?>
<div class="table-wrap">
<table class="admin-table">
    <thead><tr><th>Phòng</th><th>Định dạng</th><th>Ghế</th><th>Suất sắp tới</th><th>Sửa tên / định dạng</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($rooms as $r): ?>
        <tr>
            <td><b><?= e($r['name']) ?></b><div class="muted small">Số <?= (int)$r['number'] ?></div></td>
            <td><?= e($r['format']) ?></td>
            <td class="small"><?= (int)$r['seat_count'] ?> ghế · <?= (int)$r['vip_count'] ?> VIP · <?= (int)$r['couple_count'] ?> đôi<?= (int)$r['inactive_count'] ? ' · <span class="text-warn">' . (int)$r['inactive_count'] . ' tạm ngưng</span>' : '' ?></td>
            <td><?= (int)$r['upcoming_shows'] ?></td>
            <td>
                <form method="post" action="/admin/rooms/save" class="inline-form">
                    <?= csrfField() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                    <input type="text" name="name" value="<?= e($r['name']) ?>" maxlength="40" class="input-sm">
                    <select name="format" class="input-sm"><?php foreach (RC::FORMATS as $fm): ?><option <?= $r['format'] === $fm ? 'selected' : '' ?>><?= $fm ?></option><?php endforeach; ?></select>
                    <button class="btn btn-ghost btn-xs">Lưu</button>
                </form>
            </td>
            <td class="actions">
                <a class="row-btn primary" href="/admin/rooms/<?= (int)$r['id'] ?>/seats" title="Sơ đồ ghế" aria-label="Sơ đồ ghế"><?= Icon::svg('armchair', 17) ?></a>
                <form method="post" action="/admin/rooms/<?= (int)$r['id'] ?>/delete" class="inline-form" data-confirm="Xoá <?= e($r['name']) ?> cùng ghế và các suất chiếu chưa có vé của phòng?">
                    <?= csrfField() ?><button class="row-btn danger" title="Xoá" aria-label="Xoá"><?= Icon::svg('trash', 17) ?></button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$rooms): ?><tr><td colspan="6" class="empty-state"><?= Icon::svg('search', 28) ?>Rạp chưa có phòng chiếu.</td></tr><?php endif; ?>
    </tbody>
</table>
</div>

<section class="admin-card">
    <h2>Thêm phòng cho <?= e($cinema['name']) ?></h2>
    <form method="post" action="/admin/rooms/save" class="admin-filters">
        <?= csrfField() ?>
        <input type="hidden" name="id" value="0"><input type="hidden" name="cinema_id" value="<?= e($cinemaId) ?>">
        <input type="text" name="name" placeholder="Tên phòng (để trống = Phòng N)" maxlength="40">
        <select name="format"><?php foreach (RC::FORMATS as $fm): ?><option><?= $fm ?></option><?php endforeach; ?></select>
        <select name="layout">
            <?php foreach ($layouts as $k => $l): ?><option value="<?= $k ?>" <?= $k === 'small' ? 'selected' : '' ?>><?= e($l['label']) ?> – <?= strlen($l['rows']) ?> hàng × <?= $l['cols'] ?> ghế<?= $l['couple'] ? ' + ghế đôi' : '' ?></option><?php endforeach; ?>
        </select>
        <button class="btn btn-primary btn-sm"><?= Icon::svg('plus', 16) ?> Thêm phòng</button>
    </form>
</section>
<?php endif; ?>
