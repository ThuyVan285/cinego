<?php
declare(strict_types=1);

/** Nghiệp vụ xác thực tài khoản. */
final class AuthService
{
    /** Kiểm tra email + mật khẩu; trả về user nếu đúng, null nếu sai. */
    public static function attempt(string $email, string $password): ?array
    {
        $user = User::findByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            return null;
        }
        return $user;
    }
}
