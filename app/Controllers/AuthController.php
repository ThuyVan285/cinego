<?php
declare(strict_types=1);

final class AuthController
{
    /** Hiển thị form đăng nhập */
    public static function showLogin(): void
    {
        if (User::check()) {
            redirect('/profile');
        }

        $redirect = self::sanitizeRedirect($_GET['redirect'] ?? '');
        render('auth/login', [
            'title' => 'Đăng nhập – ' . APP_NAME,
            'nav' => '',
            'email' => '',
            'errors' => [],
            'redirect' => $redirect,
        ]);
    }

    /** Xử lý gửi form đăng nhập */
    public static function login(): void
    {
        if (User::check()) {
            redirect('/profile');
        }

        checkCsrf();
        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $redirect = self::sanitizeRedirect($_POST['redirect'] ?? '');

        $errors = [];
        if ($email === '') {
            $errors['email'] = 'Vui lòng nhập địa chỉ email.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Địa chỉ email không đúng định dạng.';
        }

        if ($password === '') {
            $errors['password'] = 'Vui lòng nhập mật khẩu.';
        }

        if (!$errors) {
            $user = AuthService::attempt($email, $password);
            if (!$user) {
                $errors['general'] = 'Email hoặc mật khẩu không chính xác. Vui lòng thử lại.';
            } elseif (!empty($user['locked'])) {
                $errors['general'] = 'Tài khoản này đã bị khoá. Vui lòng liên hệ quản trị viên CineGo.';
            } else {
                User::login($user);
                flash('ok', 'Đăng nhập thành công! Chào mừng ' . $user['name'] . ' trở lại.');
                redirect($redirect !== '' ? $redirect : '/');
            }
        }

        render('auth/login', [
            'title' => 'Đăng nhập – ' . APP_NAME,
            'nav' => '',
            'email' => $email,
            'errors' => $errors,
            'redirect' => $redirect,
        ]);
    }

    /** Hiển thị form đăng ký */
    public static function showRegister(): void
    {
        if (User::check()) {
            redirect('/profile');
        }

        render('auth/register', [
            'title' => 'Đăng ký tài khoản – ' . APP_NAME,
            'nav' => '',
            'old' => ['name' => '', 'email' => '', 'phone' => ''],
            'errors' => [],
        ]);
    }

    /** Xử lý gửi form đăng ký */
    public static function register(): void
    {
        if (User::check()) {
            redirect('/profile');
        }

        checkCsrf();
        $name = trim((string)($_POST['name'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $phone = Validator::cleanPhone((string)($_POST['phone'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $passwordConfirm = (string)($_POST['password_confirm'] ?? '');

        $errors = [];

        // Kiểm tra họ tên
        if ($name === '') {
            $errors['name'] = 'Vui lòng nhập họ và tên.';
        } elseif (mb_strlen($name) < 2 || mb_strlen($name) > 80 || !preg_match('/^[\p{L}\s\'.-]{2,80}$/u', $name)) {
            $errors['name'] = 'Họ tên không hợp lệ (từ 2 đến 80 ký tự chữ cái).';
        }

        // Kiểm tra email
        if ($email === '') {
            $errors['email'] = 'Vui lòng nhập địa chỉ email.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Địa chỉ email không đúng định dạng.';
        } elseif (User::findByEmail($email) !== null) {
            $errors['email'] = 'Email này đã được sử dụng. Vui lòng đăng nhập hoặc dùng email khác.';
        }

        // Kiểm tra số điện thoại
        if ($phone === '') {
            $errors['phone'] = 'Vui lòng nhập số điện thoại.';
        } elseif (!preg_match('/^(0|\+84)(3|5|7|8|9)\d{8}$/', $phone)) {
            $errors['phone'] = 'Số điện thoại không hợp lệ (ví dụ: 0912345678).';
        }

        // Kiểm tra mật khẩu
        if ($password === '') {
            $errors['password'] = 'Vui lòng tạo mật khẩu.';
        } elseif (strlen($password) < 6) {
            $errors['password'] = 'Mật khẩu phải chứa ít nhất 6 ký tự.';
        }

        // Xác nhận mật khẩu
        if ($passwordConfirm === '') {
            $errors['password_confirm'] = 'Vui lòng nhập lại mật khẩu để xác nhận.';
        } elseif ($password !== $passwordConfirm) {
            $errors['password_confirm'] = 'Mật khẩu xác nhận không trùng khớp.';
        }

        if (!$errors) {
            $newId = User::create($name, $email, $phone, $password, 'user');
            $newUser = User::find($newId);
            if ($newUser) {
                User::login($newUser);
                flash('ok', 'Đăng ký tài khoản CineGo thành công! Chào mừng ' . $newUser['name'] . '.');
                redirect('/profile');
            }
            $errors['general'] = 'Không thể tạo tài khoản vào lúc này. Vui lòng thử lại.';
        }

        render('auth/register', [
            'title' => 'Đăng ký tài khoản – ' . APP_NAME,
            'nav' => '',
            'old' => ['name' => $name, 'email' => $email, 'phone' => $phone],
            'errors' => $errors,
        ]);
    }

    /** Đăng xuất */
    public static function logout(): void
    {
        User::logout();
        flash('ok', 'Đã đăng xuất tài khoản thành công.');
        redirect('/');
    }

    /** Kiểm tra URL chuyển hướng an toàn */
    private static function sanitizeRedirect(string $url): string
    {
        $url = trim($url);
        if ($url !== '' && str_starts_with($url, '/') && !str_starts_with($url, '//')) {
            return $url;
        }
        return '';
    }
}

