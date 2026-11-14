<?php /** @var array $b @var ?array $user @var array $history @var string $heading */
$p = $b['payment'];
$canCancel = in_array($b['status'], ['confirmed', 'pending'], true) && $b['upcoming']; ?>
<div class="admin-head">
    <h1><?= e($heading) ?> <span class="badge badge-<?= e($b['status']) ?>"><?= e($b['status_label']) ?></span></h1>
    <a class="btn btn-ghost btn-sm" href="/admin/bookings"><?= Icon::svg('arrow-left', 16) ?> Danh sách đơn</a>
</div>

<div class="admin-split">
    <div><?php component('TicketCard', ['b' => $b]); ?></div>
    <aside>
        <section class="admin-card">
            <h2>Khách hàng</h2>
            <dl class="kv">
                <dt>Họ tên</dt><dd><?= e($b['customer_name']) ?></dd>
                <dt>Điện thoại</dt><dd><?= e($b['phone']) ?></dd>
                <dt>Email</dt><dd><?= e($b['email']) ?></dd>
                <dt>Tài khoản</dt><dd><?= $user ? e($user['name']) . ' <span class="muted small">(' . e(User::ROLE_LABEL[$user['role']] ?? $user['role']) . ')</span>' : '<span class="muted">Khách vãng lai</span>' ?></dd>
                <dt>Lịch sử</dt><dd><?= (int)$history['n'] ?> đơn · đã chi <?= e(money((int)$history['spent'])) ?></dd>
            </dl>
            <a class="small" href="/admin/bookings?q=<?= e(rawurlencode($b['phone'])) ?>">Xem các đơn khác của khách →</a>
        </section>

        <section class="admin-card">
            <h2>Xử lý đơn</h2>
            <?php if ($b['status'] === 'confirmed' && ($p['status'] ?? '') === 'unpaid'): ?>
                <form method="post" action="/admin/bookings/<?= e($b['code']) ?>/paid" data-confirm="Xác nhận đã thu <?= e(money((int)$b['total'])) ?> tại quầy?">
                    <?= csrfField() ?><button class="btn btn-primary btn-block"><?= Icon::svg('cash', 16) ?> Xác nhận đã thu tiền tại quầy</button>
                </form>
            <?php endif; ?>
            <?php if ($canCancel): ?>
                <form method="post" action="/admin/bookings/<?= e($b['code']) ?>/cancel"
                      data-confirm="Huỷ đơn <?= e($b['code']) ?>? Ghế được trả lại<?= ($p['status'] ?? '') === 'success' ? ' và tiền được hoàn cho khách' : '' ?>.">
                    <?= csrfField() ?><button class="btn btn-ghost btn-danger btn-block"><?= Icon::svg('x', 16) ?> Huỷ đơn<?= ($p['status'] ?? '') === 'success' ? ' & hoàn tiền' : '' ?></button>
                </form>
            <?php endif; ?>
            <?php if (!$canCancel && !($b['status'] === 'confirmed' && ($p['status'] ?? '') === 'unpaid')): ?>
                <p class="muted small">Không có thao tác nào cho đơn này.</p>
            <?php endif; ?>
            <?php if ($b['showtime']): ?><a class="small" href="/admin/showtimes/<?= e($b['showtime_id']) ?>/seats">Xem sơ đồ ghế của suất →</a><?php endif; ?>
        </section>

        <?php if (count($b['payments']) > 0): ?>
            <section class="admin-card">
                <h2>Lịch sử giao dịch</h2>
                <ul class="plain-list">
                    <?php foreach ($b['payments'] as $tx): ?>
                        <li><code><?= e($tx['txn_ref']) ?></code> · <?= e($tx['provider']) ?> · <span class="pay-status pay-<?= e($tx['status']) ?>"><?= e(Payment::STATUS_LABEL[$tx['status']] ?? $tx['status']) ?></span>
                            <div class="muted small"><?= e(date('H:i d/m/Y', strtotime($tx['created_at']))) ?><?= $tx['message'] ? ' – ' . e($tx['message']) : '' ?></div></li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>
    </aside>
</div>
