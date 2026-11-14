<?php /** @var array $p */
$isExpired  = $p['expires_at'] < date('Y-m-d');
$isPct      = $p['type'] === 'pct';
$badgeLabel = $p['tag'] ?? ($isPct ? '-' . $p['value'] . '%' : '-' . number_format($p['value'] / 1000) . 'K');
$c1         = $p['color1'] ?? '#ff5a36';
$c2         = $p['color2'] ?? '#ff9175';
?>
<article class="promo-ticket<?= $isExpired ? ' is-expired' : '' ?>" id="<?= e($p['code']) ?>">

    <!-- Phần trái: badge giảm giá -->
    <div class="promo-ticket-left" style="--c1:<?= e($c1) ?>;--c2:<?= e($c2) ?>">
        <span class="promo-badge"><?= e($badgeLabel) ?></span>
        <?php if ($p['need_combo'] ?? false): ?>
            <span class="promo-sub">+Combo</span>
        <?php endif; ?>
    </div>

    <!-- Notch giả vé -->
    <span class="promo-notch promo-notch-top"></span>
    <span class="promo-notch promo-notch-bot"></span>

    <!-- Phần phải: nội dung -->
    <div class="promo-ticket-right">
        <div class="promo-ticket-head">
            <h3 class="promo-title"><?= e($p['title']) ?></h3>
            <?php if ($isExpired): ?>
                <span class="promo-expired-badge">Hết hạn</span>
            <?php endif; ?>
        </div>
        <p class="promo-desc"><?= e($p['description']) ?></p>
        <p class="promo-cond">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <?= e($p['conditions']) ?>
        </p>
        <div class="promo-footer">
            <div class="promo-footer-meta">
                <span class="promo-expires">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    HSD: <?= e(date('d/m/Y', strtotime($p['expires_at']))) ?>
                </span>
                <button class="promo-code-btn" data-copy="<?= e($p['code']) ?>" title="Sao chép mã">
                    <code><?= e($p['code']) ?></code>
                    <svg class="icon-copy" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                    <span class="copy-label">Sao chép</span>
                </button>
            </div>
            <?php if (!$isExpired): ?>
            <a class="btn btn-primary btn-sm promo-cta" href="/booking">Đặt vé &amp; dùng mã</a>
            <?php else: ?>
            <span class="btn btn-ghost btn-sm promo-cta" style="opacity:.4;cursor:not-allowed">Đã hết hạn</span>
            <?php endif; ?>
        </div>
    </div>

</article>
