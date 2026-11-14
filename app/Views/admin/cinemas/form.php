<?php /** @var array $c @var bool $isNew @var array $errors @var array $brands @var array $layouts @var string $heading */
$err = fn(string $k) => isset($errors[$k]) ? '<span class="field-error">' . e($errors[$k]) . '</span>' : ''; ?>
<div class="admin-head">
    <h1><?= e($heading) ?></h1>
    <a class="btn btn-ghost btn-sm" href="/admin/cinemas"><?= Icon::svg('arrow-left', 16) ?> Danh sách rạp</a>
</div>
<form method="post" action="/admin/cinemas/save" class="admin-form">
    <?= csrfField() ?>
    <input type="hidden" name="is_new" value="<?= $isNew ? '1' : '0' ?>">
    <div class="form-grid">
        <label class="span-2">Tên rạp *<input type="text" name="name" value="<?= e($c['name']) ?>" maxlength="100" required><?= $err('name') ?></label>
        <?php if ($isNew): ?>
            <label>Mã rạp <small class="muted">(để trống = tự tạo từ tên)</small><input type="text" name="id" value="<?= e($c['id']) ?>" maxlength="24" pattern="[a-z0-9-]*" placeholder="vd. cgv-hai-chau"><?= $err('id') ?></label>
        <?php else: ?>
            <label>Mã rạp<input type="text" name="id" value="<?= e($c['id']) ?>" readonly></label>
        <?php endif; ?>
        <label>Chuỗi rạp *
            <select name="brand" required>
                <option value="">— Chọn —</option>
                <?php foreach ($brands as $id => $b): ?><option value="<?= e($id) ?>" <?= $c['brand'] === $id ? 'selected' : '' ?>><?= e($b['name']) ?></option><?php endforeach; ?>
            </select><?= $err('brand') ?>
        </label>
        <label>Thành phố *<input type="text" name="city" value="<?= e($c['city']) ?>" maxlength="40" required><?= $err('city') ?></label>
        <label>Quận / khu vực *<input type="text" name="area" value="<?= e($c['area']) ?>" maxlength="40" required placeholder="vd. Hải Châu"><?= $err('area') ?></label>
        <label class="span-2">Địa chỉ *<input type="text" name="address" value="<?= e($c['address']) ?>" maxlength="160" required><?= $err('address') ?><small class="muted">Bản đồ trên trang rạp được tìm theo tên + địa chỉ này.</small></label>
        <?php if ($isNew): ?>
            <label>Số phòng chiếu tạo sẵn<input type="number" name="room_count" min="0" max="15" value="3"></label>
            <label>Sơ đồ ghế
                <select name="layout">
                    <option value="">Tự động (phòng 1 lớn, phòng 2 vừa, còn lại nhỏ)</option>
                    <?php foreach ($layouts as $k => $l): ?><option value="<?= $k ?>"><?= e($l['label']) ?> – <?= strlen($l['rows']) ?> hàng × <?= $l['cols'] ?> ghế<?= $l['couple'] ? ', có ghế đôi' : '' ?></option><?php endforeach; ?>
                </select>
            </label>
        <?php endif; ?>
    </div>
    <div class="form-actions">
        <button class="btn btn-primary"><?= $isNew ? 'Thêm rạp' : 'Lưu thay đổi' ?></button>
        <a class="btn btn-ghost" href="/admin/cinemas">Huỷ</a>
    </div>
</form>
