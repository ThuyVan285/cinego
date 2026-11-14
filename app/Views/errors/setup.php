<?php /** @var string $error */ ?>
<div class="container"><div class="empty big">
    <strong>Chưa kết nối được cơ sở dữ liệu</strong>
    <span>Bật MySQL (XAMPP → Start), sau đó chạy lệnh cài đặt để tạo bảng và dữ liệu mẫu:</span>
    <code>C:\xampp\php\php.exe database\seeders\DatabaseSeeder.php</code>
    <small class="muted"><?= e($error) ?></small>
</div></div>
