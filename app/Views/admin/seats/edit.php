<?php /** @var array $room @var array $rows @var string $heading */ ?>
<div class="admin-head">
    <h1><?= e($heading) ?></h1>
    <a class="btn btn-ghost btn-sm" href="/admin/rooms?cinema=<?= e(rawurlencode($room['cinema_id'])) ?>"><?= Icon::svg('arrow-left', 16) ?> Phòng của rạp</a>
</div>
<p class="muted small">Bấm vào một ghế để <b>tạm ngưng / mở lại</b> (ghế hỏng, bảo trì). Đổi loại ghế cho cả hàng bằng ô chọn ở cuối mỗi hàng. Giá vé theo loại ghế chỉnh ở trang Phòng & giá vé hoặc từng suất chiếu.</p>
<ul class="legend">
    <?php foreach (Seat::TYPE_LABEL as $t => $l): ?><li><i class="seat-sample <?= $t ?>"></i><?= e($l) ?></li><?php endforeach; ?>
    <li><i class="seat-sample is-blocked"></i>Tạm ngưng</li>
</ul>

<div class="admin-card seat-scroll">
    <div class="seatmap admin-seatmap">
        <div class="screen"><span>MÀN HÌNH</span></div>
        <?php foreach ($rows as $row => $seats):
            $rowType = $seats[0]['type']; $half = intdiv(count($seats), 2); ?>
            <div class="seat-row">
                <span class="row-label"><?= e($row) ?></span>
                <?php foreach ($seats as $s): ?>
                    <form method="post" class="inline-form">
                        <?= csrfField() ?><input type="hidden" name="action" value="toggle"><input type="hidden" name="code" value="<?= e($s['code']) ?>">
                        <button class="seat <?= e($s['type']) ?><?= $s['active'] ? '' : ' is-blocked' ?><?= (int)$s['col_no'] === $half && $s['type'] !== 'couple' ? ' gap' : '' ?>"
                                title="<?= e($s['code']) ?> – <?= e(Seat::TYPE_LABEL[$s['type']]) ?><?= $s['active'] ? '' : ' (tạm ngưng)' ?> – bấm để <?= $s['active'] ? 'tạm ngưng' : 'mở lại' ?>"><?= (int)$s['col_no'] ?></button>
                    </form>
                <?php endforeach; ?>
                <form method="post" class="inline-form row-type-form">
                    <?= csrfField() ?><input type="hidden" name="action" value="row_type"><input type="hidden" name="row" value="<?= e($row) ?>">
                    <select name="type" data-autosubmit-select aria-label="Loại ghế hàng <?= e($row) ?>" class="input-sm">
                        <?php foreach (Seat::TYPE_LABEL as $t => $l): ?><option value="<?= $t ?>" <?= $rowType === $t ? 'selected' : '' ?>><?= e($l) ?></option><?php endforeach; ?>
                    </select>
                </form>
            </div>
        <?php endforeach; ?>
        <?php if (!$rows): ?><p class="muted center">Phòng chưa có ghế – thêm hàng ghế bên dưới.</p><?php endif; ?>
    </div>
</div>

<div class="admin-card">
    <form method="post" class="admin-filters">
        <?= csrfField() ?><input type="hidden" name="action" value="add_row">
        <b>Thêm hàng mới:</b>
        <label class="inline-label">Số ghế <input type="number" name="count" min="1" max="20" value="10" class="input-price"></label>
        <select name="type"><?php foreach (Seat::TYPE_LABEL as $t => $l): ?><option value="<?= $t ?>"><?= e($l) ?></option><?php endforeach; ?></select>
        <button class="btn btn-primary btn-sm"><?= Icon::svg('plus', 16) ?> Thêm hàng</button>
    </form>
    <?php if ($rows): ?>
        <form method="post" class="admin-filters" data-confirm="Bỏ hàng ghế cuối cùng (<?= e((string)array_key_last($rows)) ?>)?">
            <?= csrfField() ?><input type="hidden" name="action" value="remove_row">
            <button class="btn btn-ghost btn-sm btn-danger">Bỏ hàng cuối (<?= e((string)array_key_last($rows)) ?>)</button>
        </form>
    <?php endif; ?>
</div>
