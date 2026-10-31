<?php /** @var array $f @var array $list @var array $cities @var array $brands */ ?>
<div class="container">
    <div class="page-head"><h1>Rạp chiếu tại Đà Nẵng</h1><p class="muted">Khám phá các cụm rạp và lịch chiếu theo từng khu vực trong thành phố.</p></div>
    <form class="filter-bar" method="get" action="/cinemas" data-autosubmit>
        <label class="field grow"><span>Tên rạp</span><input type="search" name="q" value="<?= e($f['q']) ?>" placeholder="Nhập tên rạp..."></label>
        <label class="field"><span>Hệ thống</span>
            <select name="brand"><option value="">Tất cả</option>
                <?php foreach ($brands as $id => $b): ?><option value="<?= e($id) ?>" <?= $f['brand'] === $id ? 'selected' : '' ?>><?= e($b['name']) ?></option><?php endforeach; ?>
            </select></label>
        <div class="filter-actions"><button class="btn btn-primary" type="submit">Tìm</button><a class="btn btn-ghost" href="/cinemas">Xoá lọc</a></div>
    </form>
    <?php if (!$list): ?>
        <div class="empty"><strong>Không tìm thấy rạp</strong><span>Hãy thử từ khoá hoặc thành phố khác.</span></div>
    <?php else: ?>
        <div class="cinema-grid">
            <?php foreach ($list as $c): ?>
                <article class="cinema-card card">
                    <a href="/cinemas/<?= e($c['id']) ?>"><?= img(artUrl('cinema', $c['id']), $c['name'], '', 'cinema') ?></a>
                    <div class="cinema-body">
                        <span class="brand-tag" style="--c:<?= e($c['color']) ?>"><?= e($c['brand_name']) ?></span>
                        <h3><a href="/cinemas/<?= e($c['id']) ?>"><?= e($c['name']) ?></a></h3>
                        <p class="muted small"><?= e($c['address']) ?>, <?= e($c['city']) ?> · <?= (int)$c['rooms'] ?> phòng</p>
                        <p class="small"><b>Đang chiếu:</b>
                            <?php foreach (array_slice($c['movies'], 0, 4) as $i => $mv): ?><?= $i ? ', ' : '' ?><a href="/movies/<?= e($mv['id']) ?>"><?= e($mv['title']) ?></a><?php endforeach; ?>
                            <?= count($c['movies']) > 4 ? ' và ' . (count($c['movies']) - 4) . ' phim khác' : '' ?>
                            <?= !$c['movies'] ? '<span class="muted">chưa có lịch</span>' : '' ?></p>
                        <a class="btn btn-primary btn-sm" href="/cinemas/<?= e($c['id']) ?>">Xem lịch chiếu</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
