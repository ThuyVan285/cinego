<?php /** @var string $error */ ?>
<div class="container"><div class="empty big">
    <strong>Chưa kết nối được cơ sở dữ liệu</strong>
    <span>Bật MySQL (XAMPP → Start), sau đó chạy cài đặt để tạo bảng và dữ liệu mẫu.</span>
    <a class="btn btn-primary" href="/install.php">Chạy cài đặt CSDL</a>
    <small class="muted"><?= e($error) ?></small>
</div></div>
