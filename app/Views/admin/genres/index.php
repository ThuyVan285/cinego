<?php /** @var array $rows @var string $heading */ ?>
<div class="admin-head"><h1><?= e($heading) ?> <small><?= count($rows) ?></small></h1></div>

<form method="post" action="/admin/genres/save" class="admin-filters">
    <?= csrfField() ?>
    <input type="hidden" name="id" value="0">
    <input type="text" name="name" placeholder="Tên thể loại mới, ví dụ: Âm nhạc" maxlength="40" required>
    <button class="btn btn-primary btn-sm"><?= Icon::svg('plus', 16) ?> Thêm thể loại</button>
</form>

<div class="table-wrap">
<table class="admin-table">
    <thead><tr><th>Thể loại</th><th>Số phim</th><th>Đổi tên</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($rows as $g): ?>
        <tr>
            <td><a href="/admin/movies?genre=<?= e(rawurlencode($g['name'])) ?>"><b><?= e($g['name']) ?></b></a></td>
            <td><?= (int)$g['movie_count'] ?></td>
            <td>
                <form method="post" action="/admin/genres/save" class="inline-form">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= (int)$g['id'] ?>">
                    <input type="text" name="name" value="<?= e($g['name']) ?>" maxlength="40" required class="input-sm">
                    <button class="btn btn-ghost btn-xs">Lưu</button>
                </form>
            </td>
            <td class="actions">
                <form method="post" action="/admin/genres/<?= (int)$g['id'] ?>/delete" class="inline-form" data-confirm="Xoá thể loại &quot;<?= e($g['name']) ?>&quot;?">
                    <?= csrfField() ?><button class="row-btn danger" aria-label="Xoá" <?= $g['movie_count'] ? 'disabled title="Đang có phim dùng thể loại này"' : 'title="Xoá"' ?>><?= Icon::svg('trash', 17) ?></button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
