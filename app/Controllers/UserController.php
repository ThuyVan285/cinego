<?php
declare(strict_types=1);

final class UserController
{
    /** Trang tài khoản cá nhân: sửa thông tin, đổi mật khẩu, xem vé. (Route có AuthMiddleware) */
    public static function profile(): void
    {
        $user = User::requireLogin('/profile');
        $tickets = Booking::forVisitor(visitorId(), (int)$user['id']);

        $updateErrors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            checkCsrf();
            $action = (string)($_POST['action'] ?? '');

            if ($action === 'avatar') {
                $err = !empty($_POST['remove']) ? self::removeAvatar($user) : self::saveAvatar($user, $_FILES['avatar'] ?? null);
                if ($err === null) {
                    flash('ok', !empty($_POST['remove']) ? 'Đã gỡ ảnh đại diện.' : 'Đã cập nhật ảnh đại diện.');
                    redirect('/profile');
                }
                $updateErrors['avatar'] = $err;
            } elseif ($action === 'update_info') {
                $name = trim((string)($_POST['name'] ?? ''));
                $phone = Validator::cleanPhone((string)($_POST['phone'] ?? ''));

                if ($name === '' || mb_strlen($name) < 2) {
                    $updateErrors['name'] = 'Họ tên không hợp lệ.';
                }
                if ($phone === '' || !Validator::mobile($phone)) {
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
                if (!Validator::password($newPass)) {
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

        $user = User::find((int)$user['id']) ?? $user;
        render('profile/index', [
            'title' => 'Tài khoản của tôi – ' . APP_NAME,
            'nav' => '',
            'user' => $user,
            'tickets' => $tickets,
            'errors' => $updateErrors,
        ]);
    }

    private const AVATAR_TYPES = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    private const AVATAR_MAX_BYTES = 2 * 1024 * 1024;

    /** Lưu ảnh đại diện vào public/uploads/users/. Trả về lỗi hoặc null. */
    private static function saveAvatar(array $user, mixed $file): ?string
    {
        if (!is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return 'Vui lòng chọn một ảnh.';
        }
        if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
            return in_array($file['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true) ? 'Ảnh vượt quá dung lượng cho phép (2 MB).' : 'Tải ảnh lên không thành công, vui lòng thử lại.';
        }
        if ($file['size'] > self::AVATAR_MAX_BYTES) {
            return 'Ảnh vượt quá dung lượng cho phép (2 MB).';
        }
        // Kiểm tra nội dung thật của file, không tin tên/đuôi file do trình duyệt gửi.
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        $info = @getimagesize($file['tmp_name']);
        if (!isset(self::AVATAR_TYPES[$mime]) || !$info || $info[0] < 32 || $info[1] < 32 || $info[0] > 4000 || $info[1] > 4000) {
            return 'Chỉ nhận ảnh JPG, PNG hoặc WEBP, kích thước từ 32 đến 4000 px.';
        }
        $name = (int)$user['id'] . '-' . bin2hex(random_bytes(6)) . '.' . self::AVATAR_TYPES[$mime];
        $dir = BASE_PATH . '/public/uploads/users';
        if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) {
            return 'Không lưu được ảnh, vui lòng thử lại.';
        }
        self::deleteAvatarFile($user);
        User::update((int)$user['id'], ['avatar' => '/uploads/users/' . $name]);
        return null;
    }

    private static function removeAvatar(array $user): ?string
    {
        self::deleteAvatarFile($user);
        User::update((int)$user['id'], ['avatar' => null]);
        return null;
    }

    /** Xoá file ảnh cũ (chỉ trong thư mục uploads/users). */
    private static function deleteAvatarFile(array $user): void
    {
        $old = (string)($user['avatar'] ?? '');
        if (preg_match('#^/uploads/users/[\w.-]+$#', $old) && is_file(BASE_PATH . '/public' . $old)) {
            unlink(BASE_PATH . '/public' . $old);
        }
    }
}
