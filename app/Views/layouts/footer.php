<?php /** Chân trang. */ ?>
<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <a class="logo logo-light" href="/"><span class="logo-mark"><svg viewBox="0 0 24 24" width="18" height="18"><path d="M8 5l12 7-12 7z" fill="currentColor"/></svg></span><?= APP_NAME ?></a>
            <p>Đặt vé xem phim trực tuyến: chọn phim, rạp, suất chiếu, ghế, bắp nước và hoàn tất đặt vé nhanh chóng.</p>
        </div>
        <div>
            <h4>Khám phá</h4>
            <a href="/movies">Phim</a><a href="/showtimes">Lịch chiếu</a><a href="/cinemas">Rạp chiếu</a><a href="/promotions">Ưu đãi</a>
        </div>
        <div>
            <h4>Hỗ trợ khách hàng</h4>
            <a href="/tickets">Vé của tôi</a>
            <a href="#terms">Điều khoản sử dụng</a>
            <span>Hotline: 1900 0000</span>
        </div>
        <div>
            <h4>Liên hệ</h4>
            <span>hello@cinego.example</span>
            <span>123 Bạch Đằng, Hải Châu, Đà Nẵng</span>
        </div>
    </div>
    <div class="container footer-note" id="terms">
        <strong>Điều khoản sử dụng:</strong> Đây là sản phẩm minh hoạ (đồ án). Thanh toán qua cổng thử nghiệm không thu tiền thật; MoMo chỉ hoạt động khi đã cấu hình tài khoản đối tác. Vé được huỷ trước giờ chiếu ít nhất 2 giờ. Dữ liệu phim, rạp và lịch chiếu chỉ nhằm mô phỏng, các thương hiệu rạp không liên quan đến đơn vị thật.
        <div class="copy">© <?= date('Y') ?> <?= APP_NAME ?></div>
    </div>
</footer>
