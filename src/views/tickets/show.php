<?php /** @var array $b */ ?>
<div class="container narrow">
    <p class="crumbs no-print"><a href="/tickets">← Vé của tôi</a></p>
    <p><span class="status <?= $b['upcoming'] ? 'is-up' : 'is-past' ?>"><?= $b['upcoming'] ? 'Sắp chiếu' : 'Đã qua giờ chiếu' ?></span></p>
    <?php partial('ticket_card', ['b' => $b]); ?>
    <div class="action-row center no-print">
        <button class="btn btn-primary" type="button" data-print>In vé</button>
        <a class="btn btn-ghost" href="/tickets">Danh sách vé</a>
    </div>
    <p class="muted small center no-print">Hệ thống không hỗ trợ huỷ vé hay hoàn tiền.</p>
</div>
