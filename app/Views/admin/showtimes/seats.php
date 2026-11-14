<?php /** @var array $st @var array $rows @var array $status @var array $owner @var array $bookings @var array $counts @var string $heading */
$label = ['available' => 'Trống', 'held' => 'Đang giữ / chờ thanh toán', 'booked' => 'Đã bán', 'blocked' => 'Tạm ngưng', 'mine' => 'Trống']; ?>
<div class="admin-head">
    <h1><?= e($heading) ?> <small><?= e($st['movie_title']) ?> · <?= e($st['show_time']) ?> <?= e(dateVi($st['show_date'])) ?></small></h1>
    <a class="btn btn-ghost btn-sm" href="<?= e(url('/admin/showtimes', ['date' => $st['show_date'], 'cinema' => $st['cinema_id']])) ?>"><?= Icon::svg('arrow-left', 16) ?> Suất chiếu</a>
</div>
<p class="muted"><?= e($st['cinema_name']) ?> · <?= e($st['room_name']) ?> (<?= e($st['room_format']) ?>)</p>

<div class="admin-stats compact">
    <div class="admin-stat"><span>Đã bán</span><strong><?= (int)($counts['booked'] ?? 0) ?></strong></div>
    <div class="admin-stat"><span>Đang giữ</span><strong><?= (int)($counts['held'] ?? 0) ?></strong></div>
    <div class="admin-stat"><span>Còn trống</span><strong><?= (int)($counts['available'] ?? 0) + (int)($counts['mine'] ?? 0) ?></strong></div>
    <div class="admin-stat"><span>Tạm ngưng</span><strong><?= (int)($counts['blocked'] ?? 0) ?></strong></div>
</div>

<ul class="legend">
    <li><i class="seat-sample standard"></i>Trống</li><li><i class="seat-sample is-held"></i>Đang giữ</li>
    <li><i class="seat-sample is-booked admin-booked"></i>Đã bán (rê chuột để xem mã đơn)</li><li><i class="seat-sample is-blocked"></i>Tạm ngưng</li>
</ul>
<div class="admin-card seat-scroll">
    <div class="seatmap admin-seatmap">
        <div class="screen"><span>MÀN HÌNH</span></div>
        <?php foreach ($rows as $row => $seats): $half = intdiv(count($seats), 2); ?>
            <div class="seat-row">
                <span class="row-label"><?= e($row) ?></span>
                <?php foreach ($seats as $seat):
                    $s = $status[$seat['code']] ?? 'available';
                    $o = $owner[$seat['code']] ?? null;
                    $cls = 'seat ' . $seat['type'] . (in_array($s, ['booked', 'held', 'blocked'], true) ? ' is-' . $s : '') . ($s === 'booked' ? ' admin-booked' : '')
                        . ((int)$seat['col_no'] === $half && $seat['type'] !== 'couple' ? ' gap' : '');
                    $title = $seat['code'] . ' – ' . ($label[$s] ?? $s) . ($o ? ' · ' . $o['code'] . ' · ' . $o['customer_name'] : ''); ?>
                    <?php if ($o): ?>
                        <a class="<?= $cls ?>" href="/admin/bookings/<?= e($o['code']) ?>" title="<?= e($title) ?>"><?= (int)$seat['col_no'] ?></a>
                    <?php else: ?>
                        <span class="<?= $cls ?>" title="<?= e($title) ?>"><?= (int)$seat['col_no'] ?></span>
                    <?php endif; ?>
                <?php endforeach; ?>
                <span class="row-label"><?= e($row) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<h2>Đơn đặt vé của suất này (<?= count($bookings) ?>)</h2>
<div class="table-wrap">
<table class="admin-table">
    <thead><tr><th>Mã đơn</th><th>Khách hàng</th><th>Ghế</th><th>Tổng tiền</th><th>Đơn</th><th>Thanh toán</th><th>Đặt lúc</th></tr></thead>
    <tbody>
    <?php foreach ($bookings as $b): ?>
        <tr>
            <td><a href="/admin/bookings/<?= e($b['code']) ?>"><code><?= e($b['code']) ?></code></a></td>
            <td><?= e($b['customer_name']) ?><div class="muted small"><?= e($b['phone']) ?></div></td>
            <td><?= e((string)$b['seat_list']) ?></td>
            <td><?= e(money((int)$b['total'])) ?></td>
            <td><span class="badge badge-<?= e($b['status']) ?>"><?= e(Booking::STATUS_LABEL[$b['status']] ?? $b['status']) ?></span></td>
            <td><span class="pay-status pay-<?= e((string)$b['pay_status']) ?>"><?= e(Payment::STATUS_LABEL[$b['pay_status']] ?? '—') ?></span></td>
            <td class="small"><?= e(date('H:i d/m', strtotime($b['created_at']))) ?></td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$bookings): ?><tr><td colspan="7" class="empty-state"><?= Icon::svg('search', 28) ?>Chưa có đơn nào.</td></tr><?php endif; ?>
    </tbody>
</table>
</div>
