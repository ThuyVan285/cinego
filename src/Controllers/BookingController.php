<?php
declare(strict_types=1);

final class BookingController
{
    /** Bước 1 – chọn phim, ngày, rạp, suất. */
    public static function select(): void
    {
        $movieId = is_string($_GET['movie'] ?? null) ? $_GET['movie'] : '';
        $date = pickDate($_GET['date'] ?? null);
        $movie = $movieId !== '' ? Movie::find($movieId) : null;
        $sched = [];
        if ($movie && $movie['status'] === 'now') {
            $sched = Showtime::schedule($date, ['movie' => $movieId]);
        }
        render('booking/select', [
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

    /** Trả về tóm tắt hiện tại hoặc chuyển hướng về bước 1 nếu chưa có suất hợp lệ. */
    private static function need(bool $needSeats = false, bool $needCustomer = false): array
    {
        $sum = Booking::currentSummary();
        if (!$sum) {
            flash('error', 'Bạn chưa chọn suất chiếu.');
            redirect('/booking');
        }
        if (!Showtime::isBookable($sum['showtime'])) {
            Booking::reset();
            flash('error', 'Suất chiếu đã qua giờ, vui lòng chọn suất khác.');
            redirect('/booking');
        }
        if ($needSeats && !$sum['seats']) {
            flash('error', 'Vui lòng chọn ghế trước khi tiếp tục.');
            redirect('/booking/seats');
        }
        if ($needCustomer && (empty($sum['customer']['name']))) {
            flash('error', 'Vui lòng điền thông tin và xác nhận trước khi thanh toán.');
            redirect('/booking/confirm');
        }
        return $sum;
    }

    public static function seats(): void
    {
        $sum = self::need();
        $stId = $sum['showtime']['id'];
        $status = Seats::statusMap($stId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            checkCsrf();
            $codes = array_values(array_unique(array_filter(explode(',', (string)($_POST['seats'] ?? '')), 'strlen')));
            $err = null;
            if (!$codes) {
                $err = 'Vui lòng chọn ít nhất 1 ghế để tiếp tục.';
            } elseif (count($codes) > MAX_SEATS) {
                $err = 'Mỗi lượt đặt tối đa ' . MAX_SEATS . ' ghế.';
            } else {
                foreach ($codes as $c) {
                    if (!Seats::isValid($c) || ($status[$c] ?? '') !== 'available') {
                        $err = 'Ghế ' . $c . ' không khả dụng. Vui lòng chọn ghế khác.';
                        break;
                    }
                }
            }
            if ($err === null) {
                $s = Booking::state();
                $s['seats'] = $codes;
                Booking::save($s);
                redirect('/booking/combos');
            }
            flash('error', $err);
            $sum['seats'] = array_map(fn($c) => ['code' => $c, 'type' => Seats::typeOf($c), 'price' => 0], array_filter($codes, [Seats::class, 'isValid']));
        }
        render('booking/seats', [
            'title' => 'Chọn ghế – ' . APP_NAME, 'nav' => 'movies', 'step' => 2, 'sum' => $sum, 'status' => $status,
            'selected' => array_column($sum['seats'], 'code'),
        ]);
    }

    public static function combos(): void
    {
        $sum = self::need(true);
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

    public static function confirm(): void
    {
        $sum = self::need(true);
        $errors = [];
        $s = Booking::state();
        $c = $s['customer'] ?? [];
        $promoCode = (string)($s['promo'] ?? '');
        $promoMsg = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            checkCsrf();
            $c = [
                'name' => trim((string)($_POST['name'] ?? '')),
                'email' => trim((string)($_POST['email'] ?? '')),
                'phone' => preg_replace('/[\s.\-]/', '', (string)($_POST['phone'] ?? '')),
            ];
            $action = (string)($_POST['action'] ?? 'confirm');
            $promoCode = strtoupper(trim((string)($_POST['promo'] ?? '')));

            if ($action === 'remove_promo') {
                $promoCode = '';
                $promoMsg = ['ok', 'Đã gỡ mã ưu đãi.'];
            } elseif ($promoCode !== '' && ($action === 'promo' || $action === 'confirm')) {
                $p = Promotion::find($promoCode);
                [, $perr] = Promotion::evaluate($p, $sum['ticket_total'], $sum['combo_total']);
                if ($perr) {
                    $errors['promo'] = $perr;
                    $promoCode = '';
                } elseif ($action === 'promo') {
                    $promoMsg = ['ok', 'Đã áp dụng mã ' . $promoCode . '.'];
                }
            }

            if ($action === 'confirm') {
                if (mb_strlen($c['name']) < 2 || !preg_match('/^[\p{L}\s\'.-]{2,80}$/u', $c['name'])) {
                    $errors['name'] = $c['name'] === '' ? 'Vui lòng nhập họ tên.' : 'Họ tên không hợp lệ (chỉ gồm chữ cái, ít nhất 2 ký tự).';
                }
                if ($c['email'] === '') {
                    $errors['email'] = 'Vui lòng nhập email.';
                } elseif (!filter_var($c['email'], FILTER_VALIDATE_EMAIL)) {
                    $errors['email'] = 'Email không hợp lệ (ví dụ: ten@example.com).';
                }
                if ($c['phone'] === '') {
                    $errors['phone'] = 'Vui lòng nhập số điện thoại.';
                } elseif (!preg_match('/^(0|\+84)(3|5|7|8|9)\d{8}$/', $c['phone'])) {
                    $errors['phone'] = 'Số điện thoại không hợp lệ (ví dụ: 0912345678).';
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
        render('booking/confirm', [
            'title' => 'Xác nhận đặt vé – ' . APP_NAME, 'nav' => 'movies', 'step' => 4, 'sum' => $sum,
            'c' => $c + ['name' => '', 'email' => '', 'phone' => ''], 'errors' => $errors, 'promoCode' => $promoCode, 'promoMsg' => $promoMsg,
        ]);
    }

    public static function payment(): void
    {
        $sum = self::need(true, true);
        render('booking/payment', [
            'title' => 'Thanh toán – ' . APP_NAME, 'nav' => 'movies', 'step' => 5, 'sum' => $sum,
            'methods' => Booking::PAYMENTS, 'selected' => $sum['pay'] ?: 'wallet',
        ]);
    }

    public static function pay(): void
    {
        checkCsrf();
        self::need(true, true);
        $m = (string)($_POST['method'] ?? '');
        if (!isset(Booking::PAYMENTS[$m])) {
            flash('error', 'Vui lòng chọn phương thức thanh toán.');
            redirect('/booking/payment');
        }
        $s = Booking::state();
        $s['pay'] = $m;
        Booking::save($s);
        [$code, $err] = Booking::create(visitorId());
        if ($err !== null) {
            flash('error', $err);
            redirect('/booking/seats');
        }
        redirect('/booking/success/' . rawurlencode($code));
    }

    public static function success(string $code): void
    {
        $b = Booking::find($code, visitorId()) ?? notFound();
        render('booking/success', ['title' => 'Đặt vé thành công – ' . APP_NAME, 'nav' => '', 'step' => 6, 'b' => $b]);
    }
}
