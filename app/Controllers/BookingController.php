<?php
declare(strict_types=1);

final class BookingController
{
    /** Bước 1 – chọn phim, ngày, rạp, suất. */
    public static function selectShowtime(): void
    {
        $movieId = is_string($_GET['movie'] ?? null) ? $_GET['movie'] : '';
        $date = pickDate($_GET['date'] ?? null);
        $movie = $movieId !== '' ? Movie::find($movieId) : null;
        $sched = [];
        if ($movie && $movie['status'] === 'now') {
            $sched = Showtime::schedule($date, ['movie' => $movieId]);
        }
        render('booking/select-showtime', [
            'title' => 'Chọn suất chiếu – ' . APP_NAME, 'nav' => 'movies', 'step' => 1,
            'movies' => Movie::search('', '', 'now'), 'movie' => $movie, 'date' => $date, 'sched' => $sched,
            'current' => Booking::currentSummary(),
        ]);
    }

    public static function start(): void
    {
        $id = is_string($_GET['showtime'] ?? null) ? $_GET['showtime'] : '';
        $st = $id !== '' ? Showtime::find($id) : null;
        if (!$st) {
            flash('error', 'Suất chiếu không tồn tại. Vui lòng chọn lại.');
            redirect('/booking');
        }
        if (!Showtime::isBookable($st)) {
            flash('error', 'Suất chiếu này đã qua giờ, vui lòng chọn suất khác.');
            redirect(url('/booking', ['movie' => $st['movie_id'], 'date' => $st['show_date']]));
        }
        Booking::start($id);
        redirect('/booking/seats');
    }

    public static function combos(): void
    {
        $sum = BookingService::requireCurrent(true);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            checkCsrf();
            $s = Booking::state();
            $s['combos'] = [];
            if (($_POST['action'] ?? '') !== 'skip') {
                $map = Combo::map();
                foreach ((array)($_POST['qty'] ?? []) as $id => $q) {
                    $q = max(0, min(10, (int)$q));
                    if ($q > 0 && isset($map[$id])) {
                        $s['combos'][$id] = $q;
                    }
                }
            }
            Booking::save($s);
            redirect('/booking/confirm');
        }
        render('booking/combos', [
            'title' => 'Chọn bắp nước – ' . APP_NAME, 'nav' => 'movies', 'step' => 3, 'sum' => $sum,
            'combos' => Combo::all(),
            'qty' => array_column($sum['combos'], 'qty', 'id'),
        ]);
    }

    public static function checkout(): void
    {
        $sum = BookingService::requireCurrent(true);
        $errors = [];
        $s = Booking::state();
        $c = $s['customer'] ?? [];
        if (empty($c['name']) && ($u = User::current())) {
            $c['name'] = $u['name'];
            $c['email'] = $u['email'];
            $c['phone'] = $u['phone'];
        }
        $promoCode = (string)($s['promo'] ?? '');
        $promoMsg = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            checkCsrf();

            // Chuẩn hóa và làm sạch thông tin người đặt
            $name = trim((string)($_POST['name'] ?? ''));
            $email = strtolower(trim((string)($_POST['email'] ?? '')));

            // Chuẩn hóa số điện thoại: loại bỏ dấu cách, gạch ngang, chấm, chuyển +84/84 thành 0
            $rawPhone = trim((string)($_POST['phone'] ?? ''));
            $cleanPhone = preg_replace('/[^\d+]/', '', $rawPhone);
            if (str_starts_with($cleanPhone, '+84')) {
                $cleanPhone = '0' . substr($cleanPhone, 3);
            } elseif (str_starts_with($cleanPhone, '84') && strlen($cleanPhone) >= 11) {
                $cleanPhone = '0' . substr($cleanPhone, 2);
            }

            $c = [
                'name' => $name,
                'email' => $email,
                'phone' => $cleanPhone,
            ];

            $action = (string)($_POST['action'] ?? 'confirm');
            $inputPromo = strtoupper(trim((string)($_POST['promo'] ?? '')));

            // Nếu form gửi lên với action="promo" nhưng ô promo để trống (do người dùng gõ xong nhấn Enter)
            // thì tự động chuyển sang hành động xác nhận đặt vé để không làm tắc nghẽn
            if ($action === 'promo' && $inputPromo === '') {
                $action = 'confirm';
            }

            // 1. Xử lý mã ưu đãi
            if ($action === 'remove_promo') {
                $promoCode = '';
                $promoMsg = ['ok', 'Đã gỡ mã ưu đãi.'];
            } elseif ($action === 'promo') {
                if ($inputPromo === '') {
                    $errors['promo'] = 'Vui lòng nhập mã ưu đãi.';
                } else {
                    $p = Promotion::find($inputPromo);
                    [, $perr] = Promotion::evaluate($p, $sum['ticket_total'], $sum['combo_total']);
                    if ($perr) {
                        $errors['promo'] = $perr;
                    } else {
                        $promoCode = $inputPromo;
                        $promoMsg = ['ok', 'Đã áp dụng mã ưu đãi ' . $promoCode . '.'];
                    }
                }
            } elseif ($action === 'confirm') {
                // Khi xác nhận: nếu có nhập mã ưu đãi mới khác mã đã áp dụng
                if ($inputPromo !== '' && $inputPromo !== $promoCode) {
                    $p = Promotion::find($inputPromo);
                    [, $perr] = Promotion::evaluate($p, $sum['ticket_total'], $sum['combo_total']);
                    if (!$perr) {
                        $promoCode = $inputPromo;
                    }
                }
            }

            // 2. Kiểm tra tính hợp lệ của thông tin người đặt khi bấm xác nhận
            if ($action === 'confirm') {
                // Kiểm tra họ tên: từ 2 đến 80 ký tự, hỗ trợ đầy đủ tiếng Việt có dấu (cả NFC và NFD)
                $nameLen = mb_strlen($c['name']);
                if ($nameLen < 2 || $nameLen > 80) {
                    $errors['name'] = $c['name'] === '' ? 'Vui lòng nhập họ và tên.' : 'Họ và tên phải từ 2 đến 80 ký tự.';
                } elseif (!preg_match('/^[\p{L}\p{M}\s\'.-]{2,80}$/u', $c['name'])) {
                    $errors['name'] = 'Họ tên không hợp lệ (chỉ gồm chữ cái và khoảng trắng).';
                }

                // Kiểm tra email
                if ($c['email'] === '') {
                    $errors['email'] = 'Vui lòng nhập địa chỉ email.';
                } elseif (!filter_var($c['email'], FILTER_VALIDATE_EMAIL)) {
                    $errors['email'] = 'Địa chỉ email không đúng định dạng (ví dụ: ten@example.com).';
                }

                // Kiểm tra số điện thoại: 10 hoặc 11 chữ số bắt đầu bằng 0
                if ($c['phone'] === '') {
                    $errors['phone'] = 'Vui lòng nhập số điện thoại.';
                } elseif (!preg_match('/^0\d{9,10}$/', $c['phone'])) {
                    $errors['phone'] = 'Số điện thoại không hợp lệ (gồm 10-11 chữ số, ví dụ: 0912345678).';
                }
            }

            $s['customer'] = $c;
            $s['promo'] = $promoCode !== '' ? $promoCode : null;
            Booking::save($s);

            if ($action === 'confirm' && !$errors) {
                redirect('/booking/payment');
            }
            $sum = Booking::currentSummary();
        }
        render('booking/checkout', [
            'title' => 'Xác nhận đặt vé – ' . APP_NAME, 'nav' => 'movies', 'step' => 4, 'sum' => $sum,
            'c' => $c + ['name' => '', 'email' => '', 'phone' => ''], 'errors' => $errors, 'promoCode' => $promoCode, 'promoMsg' => $promoMsg,
        ]);
    }

    public static function confirmation(string $code): void
    {
        $b = Booking::find($code, visitorId()) ?? notFound();
        render('booking/confirmation', ['title' => 'Đặt vé thành công – ' . APP_NAME, 'nav' => '', 'step' => 6, 'b' => $b]);
    }
}
