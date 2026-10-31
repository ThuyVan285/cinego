<?php /** @var array $list */ ?>
<div class="container">
    <div class="page-head row-between">
        <div><h1>Vé của tôi</h1><p class="muted">Vé được lưu theo trình duyệt này (cookie) trong cơ sở dữ liệu.</p></div>
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
                            <span class="status <?= $b['upcoming'] ? 'is-up' : 'is-past' ?>"><?= $b['upcoming'] ? 'Sắp chiếu' : 'Đã qua giờ chiếu' ?></span>
                        </div>
                        <p class="muted small"><?= e($st['cinema_name']) ?> · Phòng <?= (int)$st['room'] ?></p>
                        <p><?= e($st['show_time']) ?> · <?= e(dateVi($st['show_date'])) ?></p>
                        <p class="small">Ghế: <b><?= e(implode(', ', array_column($b['seats'], 'code'))) ?></b> · Tổng: <b><?= e(money((int)$b['total'])) ?></b></p>
                        <p class="small muted">Mã: <span class="code"><?= e($b['code']) ?></span></p>
                    </div>
                    <a class="btn btn-ghost btn-sm" href="/tickets/<?= e($b['code']) ?>">Chi tiết</a>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
