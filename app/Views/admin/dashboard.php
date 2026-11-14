<?php
/** @var int $days @var string $from @var string $to @var array $cinemas @var ?string $cinemaId @var array $stats @var array $prev
 *  @var array $byDay @var array $byMonth @var array $byCinema @var array $topMovies @var array $transactions @var array $latest @var string $heading */
use Admin\DashboardController as DC;

$delta = function (int $now, int $before) use ($days): string {
    if ($before === 0) {
        return $now > 0 ? '<span class="delta up">Mới</span>' : '<span class="delta flat">—</span>';
    }
    $pct = (int)round(($now - $before) * 100 / $before);
    $cls = $pct > 0 ? 'up' : ($pct < 0 ? 'down' : 'flat');
    return '<span class="delta ' . $cls . '" title="So với ' . DC::PERIODS[$days] . ' liền trước">'
        . ($pct > 0 ? Icon::svg('arrow-up', 12) : ($pct < 0 ? Icon::svg('arrow-down', 12) : '')) . abs($pct) . '%</span>';
};
$providers = ['demo' => 'Cổng thử nghiệm', 'momo' => 'MoMo', 'vnpay' => 'VNPay (cũ)', 'counter' => 'Tại quầy'];
$cinemaName = $cinemaId ? (array_column($cinemas, 'name', 'id')[$cinemaId] ?? '') : 'Tất cả rạp';
$fmt = fn(int $n) => number_format($n, 0, ',', '.');
$dayTotal = array_sum($byDay);
$monthTotal = array_sum($byMonth);

// Tình trạng giao dịch: gộp theo trạng thái cho biểu đồ tròn, chi tiết theo cổng ở bảng.
$byStatus = [];
foreach ($transactions as $t) {
    $byStatus[$t['status']] = ($byStatus[$t['status']] ?? 0) + (int)$t['n'];
}
$statusColor = ['success' => 'var(--ad-success)', 'unpaid' => 'var(--ad-warn)', 'pending' => 'var(--ad-faint)', 'failed' => 'var(--ad-danger)',
    'refund_pending' => '#a78bfa', 'refunded' => 'var(--ad-muted)'];
$donut = [];
foreach ($byStatus as $s => $n) {
    $donut[] = ['label' => Payment::STATUS_LABEL[$s] ?? $s, 'value' => $n, 'color' => $statusColor[$s] ?? 'var(--ad-faint)', 'note' => $fmt($n) . ' GD'];
}
$successRate = array_sum($byStatus) ? round(($byStatus['success'] ?? 0) * 100 / array_sum($byStatus)) : 0;
?>
<div class="admin-head">
    <div>
        <h1><?= e($heading) ?></h1>
        <p class="ad-subtitle"><?= Icon::svg('calendar', 14) ?> <?= e(date('d/m/Y', strtotime($from))) ?> – <?= e(date('d/m/Y', strtotime($to))) ?> · <?= e($cinemaName) ?></p>
    </div>
    <form class="actions" method="get">
        <div class="segmented" role="tablist" aria-label="Khoảng thời gian">
            <?php foreach (DC::PERIODS as $d => $label): ?>
                <a href="<?= e(url('/admin', ['days' => $d, 'cinema' => $cinemaId])) ?>" class="<?= $days === $d ? 'is-active' : '' ?>" role="tab" aria-selected="<?= $days === $d ? 'true' : 'false' ?>"><?= $label ?></a>
            <?php endforeach; ?>
        </div>
        <input type="hidden" name="days" value="<?= $days ?>">
        <select name="cinema" data-autosubmit-select aria-label="Lọc theo rạp">
            <option value="">Tất cả rạp</option>
            <?php foreach ($cinemas as $c): ?><option value="<?= e($c['id']) ?>" <?= $cinemaId === $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?>
        </select>
    </form>
</div>

<div class="admin-stats">
    <div class="kpi is-primary">
        <div class="kpi-head"><span class="kpi-label">Doanh thu đã thu</span><span class="kpi-icon"><?= Icon::svg('wallet') ?></span></div>
        <div class="kpi-value"><?= e(money($stats['revenue'])) ?></div>
        <div class="kpi-foot"><?= $delta($stats['revenue'], $prev['revenue']) ?><?= Chart::spark($byDay, 'Doanh thu theo ngày') ?></div>
    </div>
    <div class="kpi">
        <div class="kpi-head"><span class="kpi-label">Vé đã bán</span><span class="kpi-icon"><?= Icon::svg('ticket') ?></span></div>
        <div class="kpi-value"><?= $fmt($stats['tickets']) ?></div>
        <div class="kpi-foot"><?= $delta($stats['tickets'], $prev['tickets']) ?><span class="kpi-sub"><?= $fmt($stats['confirmed']) ?> đơn xác nhận</span></div>
    </div>
    <div class="kpi">
        <div class="kpi-head"><span class="kpi-label">Đơn đặt vé</span><span class="kpi-icon"><?= Icon::svg('receipt') ?></span></div>
        <div class="kpi-value"><?= $fmt($stats['bookings']) ?></div>
        <div class="kpi-foot"><?= $delta($stats['bookings'], $prev['bookings']) ?><span class="kpi-sub"><?= $fmt($stats['cancelled']) ?> huỷ/hết hạn<?= $stats['pending'] ? ' · ' . $stats['pending'] . ' chờ TT' : '' ?></span></div>
    </div>
    <div class="kpi">
        <div class="kpi-head"><span class="kpi-label">Giá trị đơn trung bình</span><span class="kpi-icon"><?= Icon::svg('trend') ?></span></div>
        <div class="kpi-value"><?= e(money($stats['avg_order'])) ?></div>
        <div class="kpi-foot"><?= $delta($stats['avg_order'], $prev['avg_order']) ?><span class="kpi-sub"><?= $fmt($stats['paid_orders']) ?> đơn đã thu</span></div>
    </div>
    <div class="kpi">
        <div class="kpi-head"><span class="kpi-label">Chờ thu tại quầy</span><span class="kpi-icon"><?= Icon::svg('cash') ?></span></div>
        <div class="kpi-value"><?= e(money($stats['unpaid_amount'])) ?></div>
        <div class="kpi-foot"><span class="kpi-sub"><?= $fmt($stats['unpaid_count']) ?> đơn chưa thanh toán</span><?php if ($stats['unpaid_count']): ?><a class="small" href="/admin/bookings?pay=unpaid" style="color:var(--ad-accent);font-weight:600;text-decoration:none">Xem</a><?php endif; ?></div>
    </div>
    <div class="kpi">
        <div class="kpi-head"><span class="kpi-label">Hệ thống</span><span class="kpi-icon"><?= Icon::svg('grid') ?></span></div>
        <div class="kpi-value"><?= $stats['movies'] ?> <small class="muted" style="font-size:13px;font-weight:500">phim</small> · <?= $stats['cinemas'] ?> <small class="muted" style="font-size:13px;font-weight:500">rạp</small></div>
        <div class="kpi-foot"><span class="kpi-sub"><?= $fmt($stats['users']) ?> khách hàng có tài khoản</span></div>
    </div>
</div>

<div class="admin-grid-3-1">
    <section class="admin-card">
        <div class="chart-card-head">
            <div><h2>Xu hướng doanh thu</h2><p>Tiền thực thu theo ngày thanh toán · <?= DC::PERIODS[$days] ?></p></div>
            <div class="chart-total"><strong><?= e(money($dayTotal)) ?></strong><span>tổng kỳ này</span></div>
        </div>
        <div class="chart-wrap"><?= Chart::area($byDay, 'Doanh thu theo ngày', fn($d) => date('d/m', strtotime($d))) ?></div>
    </section>
    <section class="admin-card">
        <div class="chart-card-head"><div><h2>Tình trạng giao dịch</h2><p>Theo ngày tạo giao dịch</p></div></div>
        <?= Chart::donut($donut, $successRate . '%', 'thành công', 'Tình trạng giao dịch') ?>
    </section>
</div>

<div class="admin-grid-2">
    <section class="admin-card">
        <div class="chart-card-head">
            <div><h2>Doanh thu theo tháng</h2><p>12 tháng gần nhất</p></div>
            <div class="chart-total"><strong><?= e(money($monthTotal)) ?></strong><span>12 tháng</span></div>
        </div>
        <div class="chart-wrap"><?= Chart::bars($byMonth, 'Doanh thu theo tháng', fn($m) => 'T' . (int)date('m', strtotime($m . '-01')) . '/' . date('y', strtotime($m . '-01')), 230) ?></div>
    </section>
    <section class="admin-card">
        <div class="chart-card-head"><div><h2>Doanh thu theo rạp</h2><p><?= DC::PERIODS[$days] ?> · xếp theo doanh thu</p></div></div>
        <?= Chart::hbars(array_map(fn($c) => ['label' => $c['name'], 'value' => (int)$c['revenue'], 'note' => $c['orders'] . ' đơn'],
            array_slice(array_values(array_filter($byCinema, fn($c) => $c['revenue'] > 0)), 0, 8)), 'Doanh thu theo rạp') ?>
    </section>
</div>

<div class="admin-grid-2">
    <section class="admin-card">
        <div class="chart-card-head"><div><h2>Phim doanh thu cao</h2><p><?= DC::PERIODS[$days] ?><?= $cinemaId ? ' · ' . e($cinemaName) : '' ?></p></div></div>
        <?= Chart::hbars(array_map(fn($m) => ['label' => $m['title'], 'value' => (int)$m['revenue'], 'note' => $m['tickets'] . ' vé'], $topMovies), 'Phim doanh thu cao') ?>
    </section>
    <section class="admin-card">
        <div class="chart-card-head"><div><h2>Giao dịch theo cổng thanh toán</h2><p>Số giao dịch và số tiền</p></div></div>
        <div class="table-scroll"><table class="admin-table compact">
            <thead><tr><th>Trạng thái</th><th>Cổng</th><th class="num">Số GD</th><th class="num">Số tiền</th></tr></thead>
            <tbody>
            <?php foreach ($transactions as $t): ?>
                <tr>
                    <td><span class="pay-status pay-<?= e($t['status']) ?>"><?= e(Payment::STATUS_LABEL[$t['status']] ?? $t['status']) ?></span></td>
                    <td><?= e($providers[$t['provider']] ?? $t['provider']) ?></td>
                    <td class="num"><?= $fmt((int)$t['n']) ?></td>
                    <td class="num"><?= e(money((int)$t['amount'])) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$transactions): ?><tr><td colspan="4" class="empty-state"><?= Icon::svg('receipt', 28) ?>Chưa có giao dịch trong kỳ này.</td></tr><?php endif; ?>
            </tbody>
        </table></div>
    </section>
</div>

<section class="admin-card">
    <h2>Đơn đặt vé mới nhất <a href="/admin/bookings">Xem tất cả <?= Icon::svg('chevron-right', 14) ?></a></h2>
    <div class="table-wrap" style="box-shadow:none;margin:0">
    <table class="admin-table">
        <thead><tr><th>Mã đơn</th><th>Khách hàng</th><th>Phim</th><th class="num">Tổng tiền</th><th>Trạng thái</th><th>Thời gian</th></tr></thead>
        <tbody>
        <?php foreach ($latest as $b): ?>
            <tr>
                <td><a href="/admin/bookings/<?= e($b['code']) ?>"><code><?= e($b['code']) ?></code></a></td>
                <td><b><?= e($b['customer_name']) ?></b></td>
                <td><?= e($b['movie']) ?></td>
                <td class="num"><?= e(money((int)$b['total'])) ?></td>
                <td><span class="badge badge-<?= e($b['status']) ?>"><?= e(Booking::STATUS_LABEL[$b['status']] ?? $b['status']) ?></span></td>
                <td class="muted small"><?= e(date('H:i · d/m/Y', strtotime($b['created_at']))) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$latest): ?><tr><td colspan="6" class="empty-state"><?= Icon::svg('ticket', 28) ?>Chưa có đơn đặt vé nào.</td></tr><?php endif; ?>
        </tbody>
    </table>
    </div>
</section>
