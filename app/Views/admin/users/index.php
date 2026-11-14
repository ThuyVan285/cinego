<?php /** @var array $rows @var array $f @var array $pg @var array $counts @var ?int $meId @var string $heading */ ?>
<div class="admin-head">
    <h1><?= e($heading) ?> <small><?= $pg['total'] ?></small></h1>
</div>
<div class="admin-stats compact">
    <?php foreach (User::ROLE_LABEL as $k => $v): ?><div class="admin-stat"><span><?= e($v) ?></span><strong><?= (int)($counts[$k] ?? 0) ?></strong></div><?php endforeach; ?>
</div>
<p class="muted small"><b>Quản trị viên</b>: toàn quyền. <b>Nhân viên</b>: xem thống kê, tra cứu & xử lý đơn (thu tiền tại quầy, huỷ đơn), xem suất chiếu và tình trạng ghế. <b>Khách hàng</b>: chỉ dùng trang đặt vé.</p>

<form class="admin-filters" method="get">
    <input type="search" name="q" value="<?= e($f['q']) ?>" placeholder="Tên, email hoặc SĐT">
    <select name="role"><option value="">Mọi vai trò</option><?php foreach (User::ROLE_LABEL as $k => $v): ?><option value="<?= $k ?>" <?= $f['role'] === $k ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?></select>
    <button class="btn btn-ghost btn-sm"><?= Icon::svg('filter', 15) ?> Lọc</button>
</form>

<div class="table-wrap">
<table class="admin-table">
    <thead><tr><th>Người dùng</th><th>Liên hệ</th><th>Đơn đã mua</th><th>Tham gia</th><th>Vai trò</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($rows as $u): $me = (int)$u['id'] === $meId; ?>
        <tr class="<?= $u['locked'] ? 'is-past' : '' ?>">
            <td><b><?= e($u['name']) ?></b><?= $me ? ' <span class="badge">Bạn</span>' : '' ?><?= $u['locked'] ? ' <span class="badge badge-cancelled">Đã khoá</span>' : '' ?></td>
            <td class="small"><?= e($u['email']) ?><div class="muted"><?= e($u['phone']) ?></div></td>
            <td><?= (int)$u['bookings'] ?><div class="muted small"><?= e(money((int)$u['spent'])) ?></div></td>
            <td class="small"><?= e(date('d/m/Y', strtotime($u['created_at']))) ?></td>
            <td>
                <form method="post" action="/admin/users/<?= (int)$u['id'] ?>/role" class="inline-form">
                    <?= csrfField() ?>
                    <select name="role" data-autosubmit-select class="input-sm" <?= $me ? 'disabled title="Không thể tự đổi quyền"' : '' ?>>
                        <?php foreach (User::ROLE_LABEL as $k => $v): ?><option value="<?= $k ?>" <?= $u['role'] === $k ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?>
                    </select>
                </form>
            </td>
            <td class="actions">
                <?php if (!$me): ?>
                    <form method="post" action="/admin/users/<?= (int)$u['id'] ?>/lock" class="inline-form" data-confirm="<?= $u['locked'] ? 'Mở khoá' : 'Khoá' ?> tài khoản <?= e($u['email']) ?>?">
                        <?= csrfField() ?><button class="row-btn <?= $u['locked'] ? '' : 'danger' ?>" title="<?= $u['locked'] ? 'Mở khoá' : 'Khoá tài khoản' ?>" aria-label="<?= $u['locked'] ? 'Mở khoá' : 'Khoá tài khoản' ?>"><?= Icon::svg($u['locked'] ? 'unlock' : 'lock', 17) ?></button>
                    </form>
                <?php endif; ?>
                <a class="row-btn" href="/admin/bookings?q=<?= e(rawurlencode($u['email'])) ?>" title="Đơn" aria-label="Đơn"><?= Icon::svg('ticket', 17) ?></a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php component('Pagination', ['page' => $pg['page'], 'pages' => $pg['pages'], 'path' => '/admin/users', 'query' => array_filter($f), 'total' => $pg['total']]); ?>

<section class="admin-card">
    <h2>Tạo tài khoản nhân viên / quản trị</h2>
    <form method="post" action="/admin/users/create" class="admin-filters">
        <?= csrfField() ?>
        <input type="text" name="name" placeholder="Họ tên" required maxlength="80">
        <input type="email" name="email" placeholder="Email" required maxlength="120">
        <input type="tel" name="phone" placeholder="SĐT, vd. 0901234567" required>
        <input type="password" name="password" placeholder="Mật khẩu (≥ 8 ký tự)" required minlength="8" autocomplete="new-password">
        <select name="role"><option value="staff">Nhân viên</option><option value="admin">Quản trị viên</option><option value="user">Khách hàng</option></select>
        <button class="btn btn-primary btn-sm"><?= Icon::svg('plus', 16) ?> Tạo tài khoản</button>
    </form>
</section>
