<?php /** @var array $m @var bool $isNew @var array $errors @var string[] $genres @var string $heading */
use Admin\MovieController as MC;
$err = fn(string $k) => isset($errors[$k]) ? '<span class="field-error">' . e($errors[$k]) . '</span>' : '';
$cast = is_array($m['cast']) ? implode(', ', $m['cast']) : (string)$m['cast']; ?>
<div class="admin-head">
    <h1><?= e($heading) ?><?= !$isNew ? ' <small>' . e($m['id']) . '</small>' : '' ?></h1>
    <a class="btn btn-ghost btn-sm" href="/admin/movies"><?= Icon::svg('arrow-left', 16) ?> Danh sách phim</a>
</div>

<form method="post" action="/admin/movies/save" enctype="multipart/form-data" class="admin-form">
    <?= csrfField() ?>
    <input type="hidden" name="id" value="<?= e($m['id']) ?>">
    <div class="form-grid">
        <label class="span-2">Tên phim *<input type="text" name="title" value="<?= e($m['title']) ?>" maxlength="120" required><?= $err('title') ?></label>
        <label>Trạng thái
            <select name="status"><?php foreach (MC::STATUS as $k => $v): ?><option value="<?= $k ?>" <?= $m['status'] === $k ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?></select>
        </label>
        <label>Ngày khởi chiếu *<input type="date" name="release" value="<?= e($m['release']) ?>" required><?= $err('release') ?></label>
        <label>Thời lượng (phút)<input type="number" name="duration" min="0" max="400" value="<?= (int)$m['duration'] ?>"><?= $err('duration') ?><small class="muted">Để 0 nếu chưa công bố.</small></label>
        <label>Phân loại độ tuổi
            <select name="age">
                <option value="" <?= $m['age'] === '' ? 'selected' : '' ?>>Chưa phân loại</option>
                <?php foreach (MC::AGES as $a): ?><option <?= $m['age'] === $a ? 'selected' : '' ?>><?= $a ?></option><?php endforeach; ?>
            </select>
        </label>
        <label>Điểm đánh giá (0–10)<input type="number" name="rating" min="0" max="10" step="0.1" value="<?= e((string)$m['rating']) ?>"><small class="muted">0 = chưa có đánh giá.</small></label>
        <label>Đạo diễn *<input type="text" name="director" value="<?= e($m['director']) ?>" maxlength="80"><?= $err('director') ?></label>
        <label class="span-2">Diễn viên <small class="muted">(cách nhau bằng dấu phẩy)</small><input type="text" name="cast" value="<?= e($cast) ?>" maxlength="255"></label>
        <fieldset class="span-2">
            <legend>Thể loại * <a class="small" href="/admin/genres">Quản lý thể loại</a></legend>
            <div class="check-grid">
                <?php foreach ($genres as $g): ?>
                    <label class="check"><input type="checkbox" name="genres[]" value="<?= e($g) ?>" <?= in_array($g, (array)$m['genres'], true) ? 'checked' : '' ?>> <?= e($g) ?></label>
                <?php endforeach; ?>
            </div>
            <?= $err('genres') ?>
        </fieldset>
        <label class="span-2">Nội dung phim *<textarea name="synopsis" rows="5"><?= e($m['synopsis']) ?></textarea><?= $err('synopsis') ?></label>
        <label class="span-2">Link trailer (YouTube)<input type="url" name="trailer" value="<?= e((string)$m['trailer']) ?>" placeholder="https://www.youtube.com/watch?v=..."><?= $err('trailer') ?></label>
        <div class="span-2 poster-field">
            <img src="<?= e(posterUrl(['id' => $m['id'] ?: 'new', 'poster' => $m['poster']])) ?>" alt="Poster hiện tại" class="poster-preview" data-poster-preview>
            <label>Poster <small class="muted">(JPG/PNG/WEBP, tối đa 3 MB<?= $isNew ? '' : ' – để trống nếu giữ ảnh cũ' ?>)</small>
                <input type="file" name="poster" accept="image/jpeg,image/png,image/webp" data-poster-input><?= $err('poster') ?>
            </label>
        </div>
        <label class="check span-2"><input type="checkbox" name="featured" value="1" <?= $m['featured'] ? 'checked' : '' ?>> Phim nổi bật (hiện ở banner trang chủ khi đang chiếu)</label>
    </div>
    <div class="form-actions">
        <button class="btn btn-primary"><?= $isNew ? 'Thêm phim' : 'Lưu thay đổi' ?></button>
        <a class="btn btn-ghost" href="/admin/movies">Huỷ</a>
    </div>
</form>
