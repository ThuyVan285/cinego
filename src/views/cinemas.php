<?php /** @var array $f @var array $list @var array $cities @var array $brands */ ?>
<div class="container">
    <div class="page-head">
        <h1>Rạp chiếu tại Đà Nẵng</h1>
        <p class="muted">Khám phá các cụm rạp và lịch chiếu theo từng khu vực.</p>
    </div>

    <!-- Bộ lọc thu gọn -->
    <form class="cinema-filter-bar" method="get" action="/cinemas" data-autosubmit>
        <label class="field grow">
            <span>Tìm theo tên</span>
            <input type="search" name="q" value="<?= e($f['q']) ?>" placeholder="Nhập tên rạp...">
        </label>
        <label class="field">
            <span>Hệ thống</span>
            <select name="brand">
                <option value="">Tất cả hệ thống</option>
                <?php foreach ($brands as $id => $b): ?>
                    <option value="<?= e($id) ?>" <?= $f['brand'] === $id ? 'selected' : '' ?>><?= e($b['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <div class="filter-actions">
            <noscript><button class="btn btn-primary" type="submit">Tìm</button></noscript>
            <a class="btn btn-ghost" href="/cinemas">Xoá lọc</a>
        </div>
    </form>

    <?php if (!$list): ?>
        <div class="empty"><strong>Không tìm thấy rạp</strong><span>Hãy thử từ khoá hoặc bỏ bộ lọc.</span></div>
    <?php else: ?>

        <?php
        /* Nhóm rạp theo brand */
        $grouped = [];
        foreach ($list as $c) {
            $bid = $c['brand'];
            $grouped[$bid] ??= ['name' => $c['brand_name'], 'color' => $c['color'], 'items' => []];
            $grouped[$bid]['items'][] = $c;
        }
        ?>

        <div class="cinemas-brand-list">
            <?php foreach ($grouped as $bid => $group): ?>
            <section class="cinemas-brand-section">
                <!-- Header hệ thống rạp -->
                <div class="cinemas-brand-header">
                    <span class="cinemas-brand-dot" style="background:<?= e($group['color']) ?>"></span>
                    <h2 class="cinemas-brand-name"><?= e($group['name']) ?></h2>
                    <span class="cinemas-brand-count"><?= count($group['items']) ?> rạp</span>
                </div>

                <!-- Lưới chi nhánh -->
                <div class="cinemas-branch-grid">
                    <?php foreach ($group['items'] as $c): ?>
                    <article class="cinemas-branch-card">
                        <div class="cinemas-branch-top">
                            <div class="cinemas-branch-icon" style="background:<?= e($c['color']) ?>22;border-color:<?= e($c['color']) ?>44">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="<?= e($c['color']) ?>" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                            </div>
                            <div class="cinemas-branch-info">
                                <h3 class="cinemas-branch-name">
                                    <a href="/cinemas/<?= e($c['id']) ?>"><?= e($c['name']) ?></a>
                                </h3>
                                <p class="cinemas-branch-address">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                    <?= e($c['address']) ?>, <?= e($c['city']) ?>
                                </p>
                            </div>
                        </div>

                        <div class="cinemas-branch-meta">
                            <span class="cinemas-meta-chip">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="15" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                                <?= (int)$c['rooms'] ?> phòng chiếu
                            </span>
                            <?php if ($c['movies']): ?>
                            <span class="cinemas-meta-chip cinemas-meta-movies">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"/><line x1="7" y1="2" x2="7" y2="22"/><line x1="17" y1="2" x2="17" y2="22"/><line x1="2" y1="12" x2="22" y2="12"/><line x1="2" y1="7" x2="7" y2="7"/><line x1="2" y1="17" x2="7" y2="17"/><line x1="17" y1="17" x2="22" y2="17"/><line x1="17" y1="7" x2="22" y2="7"/></svg>
                                <?= count($c['movies']) ?> phim đang chiếu
                            </span>
                            <?php else: ?>
                            <span class="cinemas-meta-chip cinemas-meta-empty">Chưa có lịch hôm nay</span>
                            <?php endif; ?>
                        </div>

                        <?php if ($c['movies']): ?>
                        <p class="cinemas-branch-movies">
                            <?php foreach (array_slice($c['movies'], 0, 3) as $i => $mv): ?>
                                <?= $i ? ' · ' : '' ?><a href="/movies/<?= e($mv['id']) ?>"><?= e($mv['title']) ?></a>
                            <?php endforeach; ?>
                            <?= count($c['movies']) > 3 ? ' <span class="muted">+' . (count($c['movies']) - 3) . ' phim</span>' : '' ?>
                        </p>
                        <?php endif; ?>

                        <a class="btn btn-primary btn-sm cinemas-branch-cta" href="/cinemas/<?= e($c['id']) ?>">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            Xem lịch chiếu
                        </a>
                    </article>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>
</div>
