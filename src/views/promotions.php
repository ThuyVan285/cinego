<?php /** @var array $promos */ ?>
<div class="container">
    <div class="page-head">
        <h1>Ưu đãi</h1>
        <p class="muted">Nhập mã ở bước <b>Xác nhận</b> khi đặt vé để được giảm giá.</p>
    </div>

    <div class="promo-list">
        <?php foreach ($promos as $p): ?>
            <?php partial('promo_card', ['p' => $p]); ?>
        <?php endforeach; ?>
    </div>

    <section class="promo-howto-section">
        <h2>Cách dùng mã ưu đãi</h2>
        <ol class="promo-howto">
            <li>
                <span class="howto-num">1</span>
                <div><b>Chọn phim &amp; suất chiếu</b><p>Chọn phim, ngày chiếu, ghế và (tuỳ chọn) combo bắp nước.</p></div>
            </li>
            <li>
                <span class="howto-num">2</span>
                <div><b>Nhập mã ưu đãi</b><p>Ở bước <b>Xác nhận</b>, nhập mã vào ô "Mã ưu đãi" rồi bấm <b>Áp dụng</b>.</p></div>
            </li>
            <li>
                <span class="howto-num">3</span>
                <div><b>Hoàn tất &amp; thanh toán</b><p>Tổng tiền cập nhật ngay. Mỗi đơn hàng chỉ dùng được một mã.</p></div>
            </li>
        </ol>
        <a class="btn btn-primary" href="/booking">Đặt vé ngay</a>
    </section>
</div>
