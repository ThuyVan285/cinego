<?php
/** @var array $f @var string $action */
$action = $action ?? '/movies';
?>
<form class="filter-bar" action="<?= e($action) ?>" method="get" <?= isset($anchor) ? 'data-anchor="' . e($anchor) . '"' : '' ?>>
    <label class="field grow"><span>Tìm phim</span>
        <input type="search" name="q" value="<?= e($f['q']) ?>" placeholder="Nhập tên phim..."></label>
    <label class="field"><span>Thể loại</span>
        <select name="genre"><option value="">Tất cả</option>
            <?php foreach (genres() as $g): ?><option value="<?= e($g) ?>" <?= $f['genre'] === $g ? 'selected' : '' ?>><?= e($g) ?></option><?php endforeach; ?>
        </select></label>
    <label class="field"><span>Trạng thái</span>
        <select name="status">
            <option value="">Tất cả</option>
            <option value="now" <?= $f['status'] === 'now' ? 'selected' : '' ?>>Đang chiếu</option>
            <option value="soon" <?= $f['status'] === 'soon' ? 'selected' : '' ?>>Sắp chiếu</option>
        </select></label>
    <label class="field"><span>Sắp xếp</span>
        <select name="sort">
            <?php foreach (['' => 'Mới nhất', 'name_asc' => 'Tên A → Z', 'name_desc' => 'Tên Z → A', 'rating_desc' => 'Điểm cao nhất', 'rating_asc' => 'Điểm thấp nhất'] as $v => $l): ?>
                <option value="<?= $v ?>" <?= $f['sort'] === $v ? 'selected' : '' ?>><?= $l ?></option>
            <?php endforeach; ?>
        </select></label>
    <div class="filter-actions">
        <button class="btn btn-primary" type="submit">Lọc</button>
        <a class="btn btn-ghost" href="<?= e($action) ?>">Xoá lọc</a>
    </div>
</form>
