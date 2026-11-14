<?php
declare(strict_types=1);

// Front controller. Chạy:  php -S localhost:8000 -t public public/index.php  (hoặc Apache với .htaccess)
$uriPath = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
if (PHP_SAPI === 'cli-server' && $uriPath !== '/' && is_file(__DIR__ . $uriPath)) {
    return false; // file tĩnh
}

require __DIR__ . '/../app/bootstrap.php';
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
    render('errors/setup', ['title' => 'Cần cài đặt CSDL', 'error' => $e->getMessage(), 'bare' => true]);
    exit;
}

$path = rtrim($uriPath, '/') ?: '/';
$method = $_SERVER['REQUEST_METHOD'];

if (str_starts_with($path, '/api/') || $path === '/api') {
    $routes = require BASE_PATH . '/routes/api.php';
    $path = substr($path, 4) ?: '/';
} else {
    $routes = require BASE_PATH . '/routes/web.php';
}

foreach ($routes as $route) {
    [$m, $re, $handler] = $route;
    if (($m === '*' || $m === $method) && preg_match($re, $path, $mt)) {
        array_shift($mt);
        foreach ($route[3] ?? [] as $middleware) {
            $middleware::handle();
        }
        $handler(...$mt);
        exit;
    }
}
notFound();
