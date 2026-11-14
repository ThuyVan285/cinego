<?php /** @var array $rows @var string $heading */ ?>
<div class="admin-head">
    <h1><?= e($heading) ?> <small><?= count($rows) ?></small></h1>
    <a class="btn btn-primary btn-sm" href="/admin/promotions/create"><?= Icon::svg('plus', 16) ?> Thêm mã</a>
</div>
<div class="table-wrap">
<table class="admin-table">
    <thead><tr><th>Mã</th><th>Ưu đãi</th><th>Điều kiện</th><th>Hết hạn</th><th>Đã dùng</th><th>Trạng thái</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($rows as $p):
        $expired = $p['expires_at'] < today(); ?>
        <tr>
            <td><code class="promo-chip" style="background:linear-gradient(135deg, <?= e($p['color1']) ?>, <?= e($p['color2']) ?>)"><?= e($p['code']) ?></code><div class="muted small"><?= e($p['tag']) ?></div></td>
            <td><b><?= e($p['title']) ?></b><div class="muted small"><?= $p['type'] === 'pct' ? (int)$p['value'] . '%, tối đa ' . e(money((int)$p['max_discount'])) : 'Giảm ' . e(money((int)$p['value'])) ?></div></td>
            <td class="small">Đơn từ <?= e(money((int)$p['min_spend'])) ?><?= $p['need_combo'] ? ' · cần combo' : '' ?></td>
            <td class="<?= $expired ? 'text-warn' : '' ?>"><?= e(date('d/m/Y', strtotime($p['expires_at']))) ?><?= $expired ? '<div class="small">đã hết hạn</div>' : '' ?></td>
            <td><?= (int)$p['used'] ?> đơn<div class="muted small">−<?= e(money((int)$p['discounted'])) ?></div></td>
            <td><span class="badge <?= $p['active'] && !$expired ? 'badge-confirmed' : 'badge-cancelled' ?>"><?= $p['active'] ? ($expired ? 'Hết hạn' : 'Đang bật') : 'Đã tắt' ?></span></td>
            <td class="actions">
                <a class="row-btn" href="/admin/promotions/<?= e($p['code']) ?>/edit" title="Sửa" aria-label="Sửa"><?= Icon::svg('edit', 17) ?></a>
                <form method="post" action="/admin/promotions/<?= e($p['code']) ?>/toggle" class="inline-form"><?= csrfField() ?><button class="row-btn" title="<?= $p['active'] ? 'Tắt mã' : 'Bật mã' ?>" aria-label="<?= $p['active'] ? 'Tắt mã' : 'Bật mã' ?>"><?= Icon::svg('power', 17) ?></button></form>
                <form method="post" action="/admin/promotions/<?= e($p['code']) ?>/delete" class="inline-form" data-confirm="Xoá mã <?= e($p['code']) ?>?">
                    <?= csrfField() ?><button class="row-btn danger" aria-label="Xoá" <?= $p['used'] ? 'disabled title="Mã đã được dùng – hãy tắt thay vì xoá"' : 'title="Xoá"' ?>><?= Icon::svg('trash', 17) ?></button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
