<?php
declare(strict_types=1);

/**
 * Route giao diện: [method, regex, handler]. Method '*' = GET hoặc POST.
 * Handler là [Controller::class, 'method'] hoặc closure; nhóm bắt được trong regex được truyền làm tham số.
 */
return [
    // Trang chung
    ['GET', '#^/$#', [HomeController::class, 'index']],
    ['GET', '#^/promotions$#', [HomeController::class, 'promotions']],
    ['GET', '#^/art/(\w+)/([\w.-]+)\.svg$#', [Art::class, 'serve']],
    ['GET', '#^/ticket$#', [TicketController::class, 'paper']],

    // Phim, rạp, lịch chiếu
    ['GET', '#^/movies$#', [MovieController::class, 'index']],
    ['GET', '#^/movies/([\w-]+)$#', [MovieController::class, 'show']],
    ['GET', '#^/cinemas$#', [CinemaController::class, 'index']],
    ['GET', '#^/cinemas/([\w-]+)$#', [CinemaController::class, 'show']],
    ['GET', '#^/showtimes$#', [ShowtimeController::class, 'index']],

    // Đặt vé: chọn suất → ghế → bắp nước → xác nhận → thanh toán → thành công
    ['GET', '#^/booking$#', [BookingController::class, 'selectShowtime']],
    ['GET', '#^/booking/start$#', [BookingController::class, 'start']],
    ['*', '#^/booking/seats$#', [SeatController::class, 'select']],
    ['*', '#^/booking/combos$#', [BookingController::class, 'combos']],
    ['*', '#^/booking/confirm$#', [BookingController::class, 'checkout']],
    ['GET', '#^/booking/payment$#', [PaymentController::class, 'show']],
    ['POST', '#^/booking/pay$#', [PaymentController::class, 'pay']],
    ['GET', '#^/booking/success/([\w-]+)$#', [BookingController::class, 'confirmation']],

    // Cổng thanh toán: trang thử nghiệm, kết quả trả về (redirect) và IPN của MoMo
    ['GET', '#^/payment/demo/(\w+)$#', [PaymentController::class, 'demoGateway']],
    ['POST', '#^/payment/demo/(\w+)$#', [PaymentController::class, 'demoGatewaySubmit']],
    ['GET', '#^/payment/return/(momo|demo)$#', [PaymentController::class, 'result']],
    ['POST', '#^/payment/ipn/(momo)$#', [PaymentController::class, 'ipn']],

    // Tài khoản
    ['GET', '#^/login$#', [AuthController::class, 'showLogin']],
    ['POST', '#^/login$#', [AuthController::class, 'login']],
    ['GET', '#^/register$#', [AuthController::class, 'showRegister']],
    ['POST', '#^/register$#', [AuthController::class, 'register']],
    ['*', '#^/logout$#', [AuthController::class, 'logout']],
    ['*', '#^/profile$#', [UserController::class, 'profile'], [AuthMiddleware::class]],

    // Vé của tôi
    ['GET', '#^/tickets$#', [TicketController::class, 'index']],
    ['GET', '#^/tickets/([\w-]+)$#', [TicketController::class, 'show']],
    ['POST', '#^/tickets/([\w-]+)/cancel$#', [TicketController::class, 'cancel']],
    ['POST', '#^/tickets/clear$#', [TicketController::class, 'clear']],

    // ===== Quản trị =====
    // Nhân viên (staff) & admin: tổng quan, đơn đặt vé, suất chiếu (xem) & tình trạng ghế
    ['GET', '#^/admin$#', [Admin\DashboardController::class, 'index'], [StaffMiddleware::class]],
    ['GET', '#^/admin/bookings$#', [Admin\BookingController::class, 'index'], [StaffMiddleware::class]],
    ['GET', '#^/admin/bookings/([\w-]+)$#', [Admin\BookingController::class, 'show'], [StaffMiddleware::class]],
    ['POST', '#^/admin/bookings/([\w-]+)/paid$#', [Admin\BookingController::class, 'markPaid'], [StaffMiddleware::class]],
    ['POST', '#^/admin/bookings/([\w-]+)/cancel$#', [Admin\BookingController::class, 'cancel'], [StaffMiddleware::class]],
    ['GET', '#^/admin/showtimes$#', [Admin\ShowtimeController::class, 'index'], [StaffMiddleware::class]],
    ['GET', '#^/admin/showtimes/([\w.-]+)/seats$#', [Admin\ShowtimeController::class, 'seats'], [StaffMiddleware::class]],

    // Chỉ admin: suất chiếu (thêm/sửa/xoá/tự sinh)
    ['GET', '#^/admin/showtimes/create$#', [Admin\ShowtimeController::class, 'create'], [AdminMiddleware::class]],
    ['GET', '#^/admin/showtimes/([\w.-]+)/edit$#', [Admin\ShowtimeController::class, 'edit'], [AdminMiddleware::class]],
    ['POST', '#^/admin/showtimes/save$#', [Admin\ShowtimeController::class, 'save'], [AdminMiddleware::class]],
    ['POST', '#^/admin/showtimes/generate$#', [Admin\ShowtimeController::class, 'generate'], [AdminMiddleware::class]],
    ['POST', '#^/admin/showtimes/([\w.-]+)/delete$#', [Admin\ShowtimeController::class, 'delete'], [AdminMiddleware::class]],
    // Phim & thể loại
    ['GET', '#^/admin/movies$#', [Admin\MovieController::class, 'index'], [AdminMiddleware::class]],
    ['GET', '#^/admin/movies/create$#', [Admin\MovieController::class, 'create'], [AdminMiddleware::class]],
    ['GET', '#^/admin/movies/([\w-]+)/edit$#', [Admin\MovieController::class, 'edit'], [AdminMiddleware::class]],
    ['POST', '#^/admin/movies/save$#', [Admin\MovieController::class, 'save'], [AdminMiddleware::class]],
    ['POST', '#^/admin/movies/([\w-]+)/status$#', [Admin\MovieController::class, 'status'], [AdminMiddleware::class]],
    ['POST', '#^/admin/movies/([\w-]+)/delete$#', [Admin\MovieController::class, 'delete'], [AdminMiddleware::class]],
    ['GET', '#^/admin/genres$#', [Admin\GenreController::class, 'index'], [AdminMiddleware::class]],
    ['POST', '#^/admin/genres/save$#', [Admin\GenreController::class, 'save'], [AdminMiddleware::class]],
    ['POST', '#^/admin/genres/(\d+)/delete$#', [Admin\GenreController::class, 'delete'], [AdminMiddleware::class]],
    // Rạp, phòng, sơ đồ ghế, giá vé
    ['GET', '#^/admin/cinemas$#', [Admin\CinemaController::class, 'index'], [AdminMiddleware::class]],
    ['GET', '#^/admin/cinemas/create$#', [Admin\CinemaController::class, 'create'], [AdminMiddleware::class]],
    ['GET', '#^/admin/cinemas/([\w-]+)/edit$#', [Admin\CinemaController::class, 'edit'], [AdminMiddleware::class]],
    ['POST', '#^/admin/cinemas/save$#', [Admin\CinemaController::class, 'save'], [AdminMiddleware::class]],
    ['POST', '#^/admin/cinemas/([\w-]+)/delete$#', [Admin\CinemaController::class, 'delete'], [AdminMiddleware::class]],
    ['GET', '#^/admin/rooms$#', [Admin\RoomController::class, 'index'], [AdminMiddleware::class]],
    ['POST', '#^/admin/rooms/save$#', [Admin\RoomController::class, 'save'], [AdminMiddleware::class]],
    ['POST', '#^/admin/rooms/(\d+)/delete$#', [Admin\RoomController::class, 'delete'], [AdminMiddleware::class]],
    ['GET', '#^/admin/rooms/(\d+)/seats$#', [Admin\SeatController::class, 'edit'], [AdminMiddleware::class]],
    ['POST', '#^/admin/rooms/(\d+)/seats$#', [Admin\SeatController::class, 'update'], [AdminMiddleware::class]],
    ['POST', '#^/admin/prices$#', [Admin\RoomController::class, 'prices'], [AdminMiddleware::class]],
    // Khuyến mãi
    ['GET', '#^/admin/promotions$#', [Admin\PromotionController::class, 'index'], [AdminMiddleware::class]],
    ['GET', '#^/admin/promotions/create$#', [Admin\PromotionController::class, 'create'], [AdminMiddleware::class]],
    ['GET', '#^/admin/promotions/(\w+)/edit$#', [Admin\PromotionController::class, 'edit'], [AdminMiddleware::class]],
    ['POST', '#^/admin/promotions/save$#', [Admin\PromotionController::class, 'save'], [AdminMiddleware::class]],
    ['POST', '#^/admin/promotions/(\w+)/toggle$#', [Admin\PromotionController::class, 'toggle'], [AdminMiddleware::class]],
    ['POST', '#^/admin/promotions/(\w+)/delete$#', [Admin\PromotionController::class, 'delete'], [AdminMiddleware::class]],
    // Người dùng & phân quyền
    ['GET', '#^/admin/users$#', [Admin\UserController::class, 'index'], [AdminMiddleware::class]],
    ['POST', '#^/admin/users/create$#', [Admin\UserController::class, 'create'], [AdminMiddleware::class]],
    ['POST', '#^/admin/users/(\d+)/role$#', [Admin\UserController::class, 'role'], [AdminMiddleware::class]],
    ['POST', '#^/admin/users/(\d+)/lock$#', [Admin\UserController::class, 'lock'], [AdminMiddleware::class]],
];
