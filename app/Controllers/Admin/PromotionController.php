<?php
declare(strict_types=1);

namespace Admin;

use Database;

/** Chương trình khuyến mãi / mã giảm giá. (Chỉ admin) */
final class PromotionController
{
    public const TYPES = ['pct' => 'Giảm theo %', 'fixed' => 'Giảm số tiền cố định'];

    public static function index(): void
    {
        $rows = Database::all("SELECT p.*,
                (SELECT COUNT(*) FROM bookings b WHERE b.promo_code = p.code AND b.status = 'confirmed') AS used,
                (SELECT COALESCE(SUM(b.discount), 0) FROM bookings b WHERE b.promo_code = p.code AND b.status = 'confirmed') AS discounted
            FROM promotions p ORDER BY p.active DESC, p.expires_at DESC");
        Ui::render('promotions/index', 'Khuyến mãi & mã giảm giá', 'promotions', ['rows' => $rows]);
    }

    public static function create(): void
    {
        self::form([
            'code' => '', 'title' => '', 'description' => '', 'conditions' => '', 'expires_at' => addDays(today(), 30),
            'type' => 'fixed', 'value' => 20000, 'max_discount' => 20000, 'min_spend' => 100000, 'need_combo' => 0,
            'tag' => '', 'color1' => '#4f46e5', 'color2' => '#c026d3', 'active' => 1,
        ], true);
    }

    public static function edit(string $code): void
    {
        self::form(Database::one('SELECT * FROM promotions WHERE code = ?', [$code]) ?? notFound(), false);
    }

    private static function form(array $p, bool $isNew, array $errors = []): void
    {
        Ui::render('promotions/form', $isNew ? 'Thêm mã giảm giá' : 'Sửa mã ' . $p['code'], 'promotions', ['p' => $p, 'isNew' => $isNew, 'errors' => $errors]);
    }

    public static function save(): void
    {
        checkCsrf();
        $isNew = Ui::str('is_new') === '1';
        $p = [
            'code' => strtoupper(Ui::str('code')), 'title' => Ui::str('title'), 'description' => Ui::str('description'),
            'conditions' => Ui::str('conditions'), 'expires_at' => Ui::str('expires_at'),
            'type' => isset(self::TYPES[Ui::str('type')]) ? Ui::str('type') : 'fixed',
            'value' => Ui::int('value'), 'max_discount' => Ui::int('max_discount'), 'min_spend' => Ui::int('min_spend'),
            'need_combo' => empty($_POST['need_combo']) ? 0 : 1, 'tag' => Ui::str('tag'),
            'color1' => preg_match('/^#[0-9a-f]{6}$/i', Ui::str('color1')) ? Ui::str('color1') : '#4f46e5',
            'color2' => preg_match('/^#[0-9a-f]{6}$/i', Ui::str('color2')) ? Ui::str('color2') : '#c026d3',
            'active' => empty($_POST['active']) ? 0 : 1,
        ];
        if ($p['type'] === 'fixed') {
            $p['max_discount'] = $p['value']; // giảm cố định: mức tối đa chính là số tiền giảm
        }
        if ($p['tag'] === '') {
            $p['tag'] = $p['type'] === 'pct' ? '-' . $p['value'] . '%' : '-' . intdiv($p['value'], 1000) . 'K';
        }
        $errors = [];
        if (!preg_match('/^[A-Z0-9]{4,20}$/', $p['code'])) {
            $errors['code'] = 'Mã 4–20 ký tự, chỉ gồm chữ không dấu và số.';
        } elseif ($isNew && Database::one('SELECT 1 FROM promotions WHERE code = ?', [$p['code']])) {
            $errors['code'] = 'Mã này đã tồn tại.';
        }
        if (mb_strlen($p['title']) < 5) {
            $errors['title'] = 'Tiêu đề ít nhất 5 ký tự.';
        }
        if (mb_strlen($p['description']) < 5) {
            $errors['description'] = 'Nhập mô tả ưu đãi.';
        }
        if (!validDate($p['expires_at'])) {
            $errors['expires_at'] = 'Ngày hết hạn không hợp lệ.';
        }
        if ($p['type'] === 'pct' && ($p['value'] < 1 || $p['value'] > 100)) {
            $errors['value'] = 'Phần trăm từ 1 đến 100.';
        } elseif ($p['type'] === 'fixed' && ($p['value'] < 1000 || $p['value'] % 1000 !== 0)) {
            $errors['value'] = 'Số tiền giảm tối thiểu 1.000đ và chia hết cho 1.000.';
        }
        if ($p['type'] === 'pct' && $p['max_discount'] < 1000) {
            $errors['max_discount'] = 'Nhập mức giảm tối đa.';
        }
        if ($p['min_spend'] < 0) {
            $errors['min_spend'] = 'Không được âm.';
        }
        if ($errors) {
            flash('error', 'Vui lòng kiểm tra lại các trường được đánh dấu.');
            self::form($p, $isNew, $errors);
            return;
        }
        $cols = ['title', 'description', 'conditions', 'expires_at', 'type', 'value', 'max_discount', 'min_spend', 'need_combo', 'tag', 'color1', 'color2', 'active'];
        $vals = array_map(fn($c) => $p[$c], $cols);
        if ($isNew) {
            Database::exec('INSERT INTO promotions (code, ' . implode(', ', $cols) . ') VALUES (?' . str_repeat(',?', count($cols)) . ')', [$p['code'], ...$vals]);
            Ui::back('/admin/promotions', 'Đã thêm mã ' . $p['code'] . '.');
        }
        Database::exec('UPDATE promotions SET ' . implode(' = ?, ', $cols) . ' = ? WHERE code = ?', [...$vals, $p['code']]);
        Ui::back('/admin/promotions', 'Đã cập nhật mã ' . $p['code'] . '.');
    }

    public static function toggle(string $code): void
    {
        checkCsrf();
        $p = Database::one('SELECT * FROM promotions WHERE code = ?', [$code]) ?? notFound();
        Database::exec('UPDATE promotions SET active = 1 - active WHERE code = ?', [$code]);
        Ui::back('/admin/promotions', 'Mã ' . $code . ($p['active'] ? ' đã tắt.' : ' đã bật lại.'));
    }

    public static function delete(string $code): void
    {
        checkCsrf();
        Database::one('SELECT 1 FROM promotions WHERE code = ?', [$code]) ?? notFound();
        if (Database::one('SELECT 1 FROM bookings WHERE promo_code = ? LIMIT 1', [$code])) {
            Ui::back('/admin/promotions', 'Mã ' . $code . ' đã được dùng trong đơn đặt vé – hãy tắt mã thay vì xoá để giữ lịch sử.', 'error');
        }
        Database::exec('DELETE FROM promotions WHERE code = ?', [$code]);
        Ui::back('/admin/promotions', 'Đã xoá mã ' . $code . '.');
    }
}
