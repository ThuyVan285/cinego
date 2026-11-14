<?php
declare(strict_types=1);

// Kiểm tra dữ liệu form
check(Validator::email('khachhang@gmail.com'), 'Email đúng định dạng');
check(!Validator::email('khachhang@'), 'Email thiếu tên miền bị từ chối');
check(Validator::name('Nguyễn Văn A'), 'Họ tên tiếng Việt có dấu hợp lệ');
check(!Validator::name('A'), 'Họ tên 1 ký tự bị từ chối');
check(!Validator::name('Nam123'), 'Họ tên chứa số bị từ chối');
check(Validator::mobile('0912345678'), 'SĐT 0912345678 hợp lệ');
check(Validator::mobile('+84912345678'), 'SĐT +84 hợp lệ');
check(!Validator::mobile('0212345678'), 'Đầu số 02 (cố định) bị từ chối');
same('0912345678', Validator::cleanPhone('091.234-5678'), 'Bỏ dấu chấm, gạch ngang trong SĐT');
check(!Validator::password('12345'), 'Mật khẩu dưới 6 ký tự bị từ chối');

// Đăng nhập với tài khoản demo (cần CSDL đã cài bằng DatabaseSeeder)
$admin = AuthService::attempt('admin@cinego.vn', 'admin123');
check($admin !== null && $admin['role'] === 'admin', 'Đăng nhập admin demo thành công');
same(null, AuthService::attempt('admin@cinego.vn', 'sai-mat-khau'), 'Sai mật khẩu thì không đăng nhập được');
same(null, AuthService::attempt('khong-ton-tai@cinego.vn', 'admin123'), 'Email không tồn tại thì không đăng nhập được');
