<?php /** @var array $b */ ?>
<div class="container narrow">
    <?php partial('steps', ['step' => 6]); ?>
    <div class="success-head">
        <span class="success-icon"><svg viewBox="0 0 24 24" width="34" height="34" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg></span>
        <h1>Đặt vé thành công!</h1>
        <p class="muted">Vé đã được lưu trong mục “Vé của tôi”.</p>
    </div>
    <?php partial('ticket_card', ['b' => $b]); ?>
    <div class="action-row center no-print">
        <a class="btn btn-primary" href="/tickets">Xem vé của tôi</a>
        <button class="btn btn-ghost" type="button" data-print>In vé</button>
        <a class="btn btn-ghost" href="/">Quay về trang chủ</a>
    </div>
</div>
