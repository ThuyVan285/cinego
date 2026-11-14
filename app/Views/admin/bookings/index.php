<?php /** @var array $rows @var array $f @var array $pg @var array $sum @var array $cinemas @var string $heading */ ?>
<div class="admin-head">
    <h1><?= e($heading) ?> <small><?= (int)$sum['n'] ?> đơn · <?= e(money((int)$sum['amount'])) ?> đã xác nhận</small></h1>
</div>

<form class="admin-filters" method="get">
    <input type="search" name="q" value="<?= e($f['q']) ?>" placeholder="Mã đơn, tên, SĐT hoặc email">
    <select name="status"><option value="">Mọi trạng thái đơn</option><?php foreach (Booking::STATUS_LABEL as $k => $v): ?><option value="<?= $k ?>" <?= $f['status'] === $k ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?></select>
    <select name="pay"><option value="">Mọi trạng thái thanh toán</option><?php foreach (Payment::STATUS_LABEL as $k => $v): ?><option value="<?= $k ?>" <?= $f['pay'] === $k ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?></select>
    <select name="cinema"><option value="">Mọi rạp</option><?php foreach ($cinemas as $c): ?><option value="<?= e($c['id']) ?>" <?= $f['cinema'] === $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select>
    <label class="inline-label">Từ <input type="date" name="from" value="<?= e($f['from']) ?>"></label>
    <label class="inline-label">đến <input type="date" name="to" value="<?= e($f['to']) ?>"></label>
    <button class="btn btn-ghost btn-sm"><?= Icon::svg('filter', 15) ?> Lọc</button>
    <?php if (array_filter($f)): ?><a class="btn btn-ghost btn-sm" href="/admin/bookings">Bỏ lọc</a><?php endif; ?>
</form>

<div class="table-wrap">
<table class="admin-table">
    <thead><tr><th>Mã đơn</th><th>Khách hàng</th><th>Phim · Suất</th><th>Ghế</th><th>Tổng</th><th>Đơn</th><th>Thanh toán</th><th>Đặt lúc</th></tr></thead>
    <tbody>
    <?php foreach ($rows as $b): ?>
        <tr>
            <td><a href="/admin/bookings/<?= e($b['code']) ?>"><code><?= e($b['code']) ?></code></a></td>
            <td><?= e($b['customer_name']) ?><div class="muted small"><?= e($b['phone']) ?></div></td>
            <td><?= e($b['title']) ?><div class="muted small"><?= e($b['cinema_name']) ?> · <?= e($b['show_time']) ?> <?= e(date('d/m', strtotime($b['show_date']))) ?></div></td>
            <td class="small"><?= e((string)$b['seat_list']) ?></td>
            <td><?= e(money((int)$b['total'])) ?></td>
            <td><span class="badge badge-<?= e($b['status']) ?>"><?= e(Booking::STATUS_LABEL[$b['status']]) ?></span></td>
            <td><span class="pay-status pay-<?= e((string)$b['pay_status']) ?>"><?= e(Payment::STATUS_LABEL[$b['pay_status']] ?? '—') ?></span><div class="muted small"><?= e(Payment::label($b['payment_method'])) ?></div></td>
            <td class="small"><?= e(date('H:i d/m/Y', strtotime($b['created_at']))) ?></td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$rows): ?><tr><td colspan="8" class="empty-state"><?= Icon::svg('search', 28) ?>Không có đơn phù hợp.</td></tr><?php endif; ?>
    </tbody>
</table>
</div>
<?php component('Pagination', ['page' => $pg['page'], 'pages' => $pg['pages'], 'path' => '/admin/bookings', 'query' => array_filter($f), 'total' => $pg['total']]); ?>
