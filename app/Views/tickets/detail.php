<?php /** @var array $b */ ?>
<div class="container narrow">
    <p class="crumbs no-print"><a href="/tickets">← Vé của tôi</a></p>
    <?php component('TicketCard', ['b' => $b]); ?>
    <div class="action-row center no-print">
        <?php if ($b['status'] === 'confirmed'): ?><button class="btn btn-primary" type="button" data-print>In vé</button><?php endif; ?>
        <a class="btn btn-ghost" href="/tickets">Danh sách vé</a>
        <?php if ($b['can_cancel']): ?>
            <form method="post" action="/tickets/<?= e($b['code']) ?>/cancel"
                  data-confirm="Huỷ vé <?= e($b['code']) ?>? Ghế sẽ được trả lại<?= ($b['payment']['status'] ?? '') === 'success' ? ' và tiền được hoàn về phương thức đã thanh toán' : '' ?>.">
                <?= csrfField() ?><button class="btn btn-ghost btn-danger" type="submit">Huỷ vé</button>
            </form>
        <?php endif; ?>
    </div>
    <?php if ($b['status'] === 'confirmed' && !$b['can_cancel'] && $b['upcoming']): ?>
        <p class="muted small center no-print">Vé chỉ huỷ được trước giờ chiếu ít nhất <?= (int)config('app.cancel_before_minutes', 120) ?> phút.</p>
    <?php elseif ($b['status'] === 'confirmed' && $b['can_cancel']): ?>
        <p class="muted small center no-print">Có thể huỷ vé trước giờ chiếu ít nhất <?= (int)config('app.cancel_before_minutes', 120) ?> phút; vé đã thanh toán online được hoàn tiền.</p>
    <?php endif; ?>
</div>
