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
            $user = User::findByEmail($email);
            if (!$user || !password_verify($password, $user['password'])) {
                $errors['general'] = 'Email hoặc mật khẩu không chính xác. Vui lòng thử lại.';
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
        $phone = preg_replace('/[\s.\-]/', '', (string)($_POST['phone'] ?? ''));
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

    /** Trang thông tin tài khoản cá nhân */
    public static function profile(): void
    {
        $user = User::requireLogin('/profile');
        $tickets = Booking::forVisitor(visitorId(), (int)$user['id']);

        $updateErrors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            checkCsrf();
            $action = (string)($_POST['action'] ?? '');

            if ($action === 'update_info') {
                $name = trim((string)($_POST['name'] ?? ''));
                $phone = preg_replace('/[\s.\-]/', '', (string)($_POST['phone'] ?? ''));

                if ($name === '' || mb_strlen($name) < 2) {
                    $updateErrors['name'] = 'Họ tên không hợp lệ.';
                }
                if ($phone === '' || !preg_match('/^(0|\+84)(3|5|7|8|9)\d{8}$/', $phone)) {
                    $updateErrors['phone'] = 'Số điện thoại không hợp lệ.';
                }

                if (!$updateErrors) {
                    User::update((int)$user['id'], ['name' => $name, 'phone' => $phone]);
                    $user = User::find((int)$user['id']);
                    User::login($user);
                    flash('ok', 'Đã cập nhật thông tin cá nhân thành công.');
                    redirect('/profile');
                }
            } elseif ($action === 'change_password') {
                $oldPass = (string)($_POST['old_password'] ?? '');
                $newPass = (string)($_POST['new_password'] ?? '');
                $confirmPass = (string)($_POST['confirm_password'] ?? '');

                if (!password_verify($oldPass, $user['password'])) {
                    $updateErrors['old_password'] = 'Mật khẩu hiện tại không đúng.';
                }
                if (strlen($newPass) < 6) {
                    $updateErrors['new_password'] = 'Mật khẩu mới phải có ít nhất 6 ký tự.';
                }
                if ($newPass !== $confirmPass) {
                    $updateErrors['confirm_password'] = 'Xác nhận mật khẩu mới không khớp.';
                }

                if (!$updateErrors) {
                    User::update((int)$user['id'], ['password' => $newPass]);
                    flash('ok', 'Đổi mật khẩu thành công!');
                    redirect('/profile');
                }
            }
        }

        render('auth/profile', [
            'title' => 'Tài khoản của tôi – ' . APP_NAME,
            'nav' => '',
            'user' => $user,
            'tickets' => $tickets,
            'errors' => $updateErrors,
        ]);
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

