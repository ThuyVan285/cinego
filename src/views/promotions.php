<?php /** @var array $promos */ ?>
<div class="container">
    <div class="page-head"><h1>Ưu đãi</h1><p class="muted">Nhập mã ở bước Xác nhận khi đặt vé.</p></div>
    <div class="promo-grid">
        <?php foreach ($promos as $p) { echo '<div id="' . e($p['code']) . '">'; partial('promo_card', ['p' => $p]); echo '</div>'; } ?>
    </div>
    <section class="section">
        <h2>Cách dùng mã</h2>
        <ol class="howto">
            <li>Chọn phim, suất chiếu, ghế và (tuỳ chọn) bắp nước.</li>
            <li>Ở bước <b>Xác nhận</b>, nhập mã vào ô “Mã ưu đãi” rồi bấm <b>Áp dụng</b>.</li>
            <li>Tổng tiền cập nhật ngay. Mức giảm không bao giờ vượt quá tổng đơn hàng và mỗi đơn chỉ dùng một mã.</li>
        </ol>
        <a class="btn btn-primary" href="/booking">Đặt vé ngay</a>
    </section>
</div>
