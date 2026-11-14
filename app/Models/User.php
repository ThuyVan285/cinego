<?php
declare(strict_types=1);

final class User
{
    private static ?array $cachedCurrent = null;

    public static function find(int $id): ?array
    {
        return Database::one('SELECT * FROM users WHERE id = ?', [$id]);
    }

    public static function findByEmail(string $email): ?array
    {
        return Database::one('SELECT * FROM users WHERE LOWER(email) = LOWER(?)', [trim($email)]);
    }

    public static function create(string $name, string $email, string $phone, string $password, string $role = 'user'): int
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $pdo = Database::pdo();
        $st = $pdo->prepare('INSERT INTO users (name, email, phone, password, role, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
        $st->execute([trim($name), strtolower(trim($email)), trim($phone), $hash, $role]);
        return (int)$pdo->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [];
        foreach (['name', 'email', 'phone', 'role', 'avatar'] as $col) {
            if (array_key_exists($col, $data)) {
                $fields[] = "$col = ?";
                $params[] = $data[$col];
            }
        }
        if (!empty($data['password'])) {
            $fields[] = 'password = ?';
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        if (!$fields) {
            return false;
        }
        $params[] = $id;
        $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = ?';
        return Database::exec($sql, $params) > 0;
    }

    // ---------- Quản lý Session / Đăng nhập ----------

    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        self::$cachedCurrent = $user;
    }

    public static function logout(): void
    {
        unset(
            $_SESSION['user_id'],
            $_SESSION['user_name'],
            $_SESSION['user_email'],
            $_SESSION['user_role']
        );
        self::$cachedCurrent = null;
        session_regenerate_id(true);
    }

    public static function check(): bool
    {
        return !empty($_SESSION['user_id']);
    }

    public static function id(): ?int
    {
        return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    }

    public static function current(): ?array
    {
        if (!self::check()) {
            return null;
        }
        if (self::$cachedCurrent !== null && (int)(self::$cachedCurrent['id'] ?? 0) === self::id()) {
            return self::$cachedCurrent;
        }
        $u = self::find((int)$_SESSION['user_id']);
        if (!$u || !empty($u['locked'])) { // tài khoản bị xoá hoặc bị khoá trong lúc đang đăng nhập
            self::logout();
            return null;
        }
        self::$cachedCurrent = $u;
        return $u;
    }

    public static function role(): string
    {
        return (string)($_SESSION['user_role'] ?? 'guest');
    }

    public const ROLE_LABEL = ['admin' => 'Quản trị viên', 'staff' => 'Nhân viên', 'user' => 'Khách hàng'];

    public static function isAdmin(): bool
    {
        return (self::current()['role'] ?? '') === 'admin';
    }

    /** Admin hoặc nhân viên rạp – được vào trang quản trị (phần vận hành). */
    public static function isStaff(): bool
    {
        return in_array(self::current()['role'] ?? '', ['admin', 'staff'], true);
    }

    public static function requireLogin(string $redirect = ''): array
    {
        $u = self::current();
        if (!$u) {
            flash('error', 'Vui lòng đăng nhập để tiếp tục.');
            $target = $redirect !== '' ? url('/login', ['redirect' => $redirect]) : '/login';
            redirect($target);
        }
        return $u;
    }

    public static function requireAdmin(): array
    {
        $u = self::requireLogin();
        if ($u['role'] !== 'admin') {
            http_response_code(403);
            render('errors/error', [
                'title' => 'Không có quyền truy cập',
                'message' => 'Tài khoản của bạn không có quyền quản trị viên.',
            ]);
            exit;
        }
        return $u;
    }
}

