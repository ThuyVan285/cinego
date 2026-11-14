<?php /** @var array $p @var bool $isNew @var array $errors @var string $heading */
use Admin\PromotionController as PC;
$err = fn(string $k) => isset($errors[$k]) ? '<span class="field-error">' . e($errors[$k]) . '</span>' : ''; ?>
<div class="admin-head">
    <h1><?= e($heading) ?></h1>
    <a class="btn btn-ghost btn-sm" href="/admin/promotions"><?= Icon::svg('arrow-left', 16) ?> Danh sách mã</a>
</div>
<form method="post" action="/admin/promotions/save" class="admin-form">
    <?= csrfField() ?>
    <input type="hidden" name="is_new" value="<?= $isNew ? '1' : '0' ?>">
    <div class="form-grid">
        <label>Mã giảm giá *<input type="text" name="code" value="<?= e($p['code']) ?>" maxlength="20" pattern="[A-Za-z0-9]{4,20}" <?= $isNew ? 'required' : 'readonly' ?> class="upper"><?= $err('code') ?></label>
        <label>Hết hạn *<input type="date" name="expires_at" value="<?= e($p['expires_at']) ?>" required><?= $err('expires_at') ?></label>
        <label class="span-2">Tiêu đề *<input type="text" name="title" value="<?= e($p['title']) ?>" maxlength="120" required><?= $err('title') ?></label>
        <label class="span-2">Mô tả *<input type="text" name="description" value="<?= e($p['description']) ?>" maxlength="255" required><?= $err('description') ?></label>
        <label class="span-2">Điều kiện hiển thị cho khách<input type="text" name="conditions" value="<?= e($p['conditions']) ?>" maxlength="255"></label>
        <label>Kiểu giảm
            <select name="type"><?php foreach (PC::TYPES as $k => $v): ?><option value="<?= $k ?>" <?= $p['type'] === $k ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?></select>
        </label>
        <label>Giá trị (% hoặc đ) *<input type="number" name="value" value="<?= (int)$p['value'] ?>" min="1" required><?= $err('value') ?></label>
        <label>Giảm tối đa (đ) <small class="muted">– với kiểu %</small><input type="number" name="max_discount" value="<?= (int)$p['max_discount'] ?>" min="0" step="1000"><?= $err('max_discount') ?></label>
        <label>Đơn tối thiểu (đ)<input type="number" name="min_spend" value="<?= (int)$p['min_spend'] ?>" min="0" step="1000"><?= $err('min_spend') ?></label>
        <label>Nhãn ngắn <small class="muted">(để trống = tự tạo, vd. -10%)</small><input type="text" name="tag" value="<?= e($p['tag']) ?>" maxlength="12"></label>
        <label>Màu thẻ <span class="color-pair"><input type="color" name="color1" value="<?= e($p['color1']) ?>"><input type="color" name="color2" value="<?= e($p['color2']) ?>"></span></label>
        <label class="check"><input type="checkbox" name="need_combo" value="1" <?= $p['need_combo'] ? 'checked' : '' ?>> Chỉ áp dụng khi đơn có combo bắp nước</label>
        <label class="check"><input type="checkbox" name="active" value="1" <?= $p['active'] ? 'checked' : '' ?>> Đang bật (khách dùng được)</label>
    </div>
    <div class="form-actions">
        <button class="btn btn-primary"><?= $isNew ? 'Thêm mã' : 'Lưu thay đổi' ?></button>
        <a class="btn btn-ghost" href="/admin/promotions">Huỷ</a>
    </div>
</form>
