<?php
declare(strict_types=1);

// Front controller. Chạy:  php -S localhost:8000 -t public public/index.php
$uriPath = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
if (PHP_SAPI === 'cli-server' && $uriPath !== '/' && is_file(__DIR__ . $uriPath)) {
    return false; // file tĩnh / install.php
}

require __DIR__ . '/../src/bootstrap.php';
startApp();
visitorId();

try {
    Database::pdo();
    if (($_SESSION['ensured'] ?? '') !== today()) {
        Showtime::ensureRange();
        $_SESSION['ensured'] = today();
    }
} catch (PDOException $e) {
    http_response_code(503);
    render('setup', ['title' => 'Cần cài đặt CSDL', 'error' => $e->getMessage(), 'bare' => true]);
    exit;
}

$path = rtrim($uriPath, '/') ?: '/';
$method = $_SERVER['REQUEST_METHOD'];

$routes = [
    ['GET', '#^/$#', [PageController::class, 'home']],
    ['GET', '#^/movies$#', [PageController::class, 'movies']],
    ['GET', '#^/movies/([\w-]+)$#', [PageController::class, 'movie']],
    ['GET', '#^/showtimes$#', [PageController::class, 'showtimes']],
    ['GET', '#^/cinemas$#', [PageController::class, 'cinemas']],
    ['GET', '#^/cinemas/([\w-]+)$#', [PageController::class, 'cinema']],
    ['GET', '#^/promotions$#', [PageController::class, 'promotions']],
    ['GET', '#^/art/(\w+)/([\w.-]+)\.svg$#', [Art::class, 'serve']],
    ['GET', '#^/booking$#', [BookingController::class, 'select']],
    ['GET', '#^/booking/start$#', [BookingController::class, 'start']],
    ['*', '#^/booking/seats$#', [BookingController::class, 'seats']],
    ['*', '#^/booking/combos$#', [BookingController::class, 'combos']],
    ['*', '#^/booking/confirm$#', [BookingController::class, 'confirm']],
    ['GET', '#^/booking/payment$#', [BookingController::class, 'payment']],
    ['POST', '#^/booking/pay$#', [BookingController::class, 'pay']],
    ['GET', '#^/booking/success/([\w-]+)$#', [BookingController::class, 'success']],
    ['GET', '#^/tickets$#', [TicketController::class, 'index']],
    ['GET', '#^/tickets/([\w-]+)$#', [TicketController::class, 'show']],
    ['POST', '#^/tickets/clear$#', [TicketController::class, 'clear']],
];

foreach ($routes as [$m, $re, $handler]) {
    if (($m === '*' || $m === $method) && preg_match($re, $path, $mt)) {
        array_shift($mt);
        $handler(...$mt);
        exit;
    }
}
notFound();
