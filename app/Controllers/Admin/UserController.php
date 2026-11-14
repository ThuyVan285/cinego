<?php
declare(strict_types=1);

namespace Admin;

use Database;
use User;
use Validator;

/** Tài khoản & phân quyền (Admin / Nhân viên / Khách hàng), khoá tài khoản. (Chỉ admin) */
final class UserController
{
    public static function index(): void
    {
        $f = ['q' => queryStr('q'), 'role' => queryStr('role')];
        $where = [];
        $p = [];
        if ($f['q'] !== '') {
            $where[] = '(u.name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)';
            array_push($p, Ui::like($f['q']), Ui::like($f['q']), Ui::like($f['q']));
        }
        if (isset(User::ROLE_LABEL[$f['role']])) {
            $where[] = 'u.role = ?';
            $p[] = $f['role'];
        }
        $w = $where ? ' WHERE ' . implode(' AND ', $where) : '';
        $pg = Ui::paginate((int)Database::one("SELECT COUNT(*) n FROM users u$w", $p)['n']);
        $rows = Database::all("SELECT u.*,
                (SELECT COUNT(*) FROM bookings b WHERE b.user_id = u.id AND b.status = 'confirmed') AS bookings,
                (SELECT COALESCE(SUM(b.total), 0) FROM bookings b WHERE b.user_id = u.id AND b.status = 'confirmed') AS spent
            FROM users u$w ORDER BY FIELD(u.role, 'admin', 'staff', 'user'), u.created_at DESC LIMIT " . Ui::PER_PAGE . ' OFFSET ' . $pg['offset'], $p);
        $counts = array_column(Database::all('SELECT role, COUNT(*) n FROM users GROUP BY role'), 'n', 'role');
        Ui::render('users/index', 'Người dùng & phân quyền', 'users', [
            'rows' => $rows, 'f' => $f, 'pg' => $pg, 'counts' => $counts, 'meId' => User::id(),
        ]);
    }

    /** Tạo tài khoản (thường dùng để tạo nhân viên rạp). */
    public static function create(): void
    {
        checkCsrf();
        $name = Ui::str('name');
        $email = strtolower(Ui::str('email'));
        $phone = Validator::cleanPhone(Ui::str('phone'));
        $pass = (string)($_POST['password'] ?? '');
        $role = isset(User::ROLE_LABEL[Ui::str('role')]) ? Ui::str('role') : 'staff';
        $err = match (true) {
            !Validator::name($name) => 'Họ tên không hợp lệ.',
            !Validator::email($email) => 'Email không hợp lệ.',
            User::findByEmail($email) !== null => 'Email đã được dùng.',
            !Validator::mobile($phone) => 'Số điện thoại không hợp lệ.',
            !Validator::password($pass, 8) => 'Mật khẩu tối thiểu 8 ký tự.',
            default => null,
        };
        if ($err) {
            Ui::back('/admin/users', 'Không tạo được tài khoản: ' . $err, 'error');
        }
        User::create($name, $email, $phone, $pass, $role);
        Ui::back('/admin/users', 'Đã tạo tài khoản ' . User::ROLE_LABEL[$role] . ' cho ' . $email . '.');
    }

    public static function role(string $id): void
    {
        checkCsrf();
        $u = User::find((int)$id) ?? notFound();
        $role = Ui::str('role');
        if (!isset(User::ROLE_LABEL[$role])) {
            Ui::back('/admin/users', 'Vai trò không hợp lệ.', 'error');
        }
        if ((int)$u['id'] === User::id()) {
            Ui::back('/admin/users', 'Không thể tự đổi quyền của chính mình.', 'error');
        }
        if ($u['role'] === 'admin' && $role !== 'admin' && self::adminCount() <= 1) {
            Ui::back('/admin/users', 'Phải còn ít nhất một quản trị viên.', 'error');
        }
        Database::exec('UPDATE users SET role = ? WHERE id = ?', [$role, $u['id']]);
        Ui::back('/admin/users', $u['name'] . ' giờ là ' . User::ROLE_LABEL[$role] . '.');
    }

    public static function lock(string $id): void
    {
        checkCsrf();
        $u = User::find((int)$id) ?? notFound();
        if ((int)$u['id'] === User::id()) {
            Ui::back('/admin/users', 'Không thể tự khoá tài khoản của chính mình.', 'error');
        }
        if (!$u['locked'] && $u['role'] === 'admin' && self::adminCount() <= 1) {
            Ui::back('/admin/users', 'Không thể khoá quản trị viên cuối cùng.', 'error');
        }
        Database::exec('UPDATE users SET locked = 1 - locked WHERE id = ?', [$u['id']]);
        Ui::back('/admin/users', 'Tài khoản ' . $u['email'] . ($u['locked'] ? ' đã được mở khoá.' : ' đã bị khoá – người dùng bị đăng xuất ở lần truy cập tiếp theo.'));
    }

    private static function adminCount(): int
    {
        return (int)Database::one("SELECT COUNT(*) n FROM users WHERE role = 'admin' AND locked = 0")['n'];
    }
}
