<?php /** @var array $list */
$currUser = User::current();
?>
<div class="container">
    <div class="page-head row-between">
        <div>
            <h1>Vé của tôi</h1>
            <?php if ($currUser): ?>
                <p class="muted">Vé đã đặt được liên kết và đồng bộ an toàn với tài khoản <strong><?= e($currUser['name']) ?></strong>.</p>
            <?php else: ?>
                <p class="muted">Vé được lưu theo trình duyệt này. <a href="/login?redirect=/tickets">Đăng nhập</a> để đồng bộ vé vĩnh viễn vào tài khoản.</p>
            <?php endif; ?>
        </div>
        <?php if ($list): ?>
            <form method="post" action="/tickets/clear" data-confirm="Xoá toàn bộ lịch sử vé? Các ghế sẽ được trả về trạng thái trống.">
                <?= csrfField() ?><button class="btn btn-ghost btn-sm" type="submit">Xoá lịch sử vé</button>
            </form>
        <?php endif; ?>
    </div>
    <?php if (!$list): ?>
        <div class="empty big"><strong>Bạn chưa có vé nào</strong><span>Hãy chọn một bộ phim và hoàn tất đặt vé đầu tiên.</span><a class="btn btn-primary" href="/movies?status=now">Chọn phim</a></div>
    <?php else: ?>
        <div class="ticket-list">
            <?php foreach ($list as $b): $st = $b['showtime']; ?>
                <article class="ticket-row card">
                    <?= img(artUrl('poster', $st['movie_id']), 'Poster ' . $st['movie_title']) ?>
                    <div class="grow">
                        <div class="row-between">
                            <h3><?= e($st['movie_title']) ?></h3>
                            <?php component('TicketStatus', ['b' => $b]); ?>
                        </div>
                        <p class="muted small"><?= e($st['cinema_name']) ?> · <?= e($st['room_name']) ?></p>
                        <p><?= e($st['show_time']) ?> · <?= e(dateVi($st['show_date'])) ?></p>
                        <p class="small">Ghế: <b><?= e(implode(', ', array_column($b['seats'], 'code'))) ?></b> · Tổng: <b><?= e(money((int)$b['total'])) ?></b> · <?= e(Payment::label($b['payment_method'])) ?><?= $b['pay_label'] ? ' (' . e($b['pay_label']) . ')' : '' ?></p>
                        <p class="small muted">Mã: <span class="code"><?= e($b['code']) ?></span></p>
                    </div>
                    <a class="btn btn-ghost btn-sm" href="/tickets/<?= e($b['code']) ?>">Chi tiết</a>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
