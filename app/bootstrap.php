<?php
declare(strict_types=1);

/**
 * Khởi động ứng dụng: nạp .env, cấu hình, helper và autoload class trong app/.
 * Dùng chung cho public/index.php, database/seeders, database/migrations và tests.
 */
define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/app/Helpers/Functions.php';
require BASE_PATH . '/app/Helpers/Session.php';
require BASE_PATH . '/app/Helpers/Response.php';

loadEnv(BASE_PATH . '/.env');

date_default_timezone_set((string)config('app.timezone', 'Asia/Ho_Chi_Minh'));

define('APP_NAME', (string)config('app.name', 'CineGo'));
define('MAX_SEATS', (int)config('app.max_seats', 8));
define('PER_PAGE', (int)config('app.per_page', 8));

ini_set('log_errors', '1');
ini_set('error_log', BASE_PATH . '/storage/logs/php-error.log');
ini_set('display_errors', config('app.debug') ? '1' : '0');

// Autoload: tên class → file trong các thư mục dưới. "Admin\MovieController" → Controllers/Admin/MovieController.php
spl_autoload_register(function (string $class): void {
    $rel = str_replace('\\', '/', $class) . '.php';
    foreach (['Controllers', 'Models', 'Services', 'Middleware', 'Helpers'] as $dir) {
        $f = BASE_PATH . "/app/$dir/$rel";
        if (is_file($f)) {
            require $f;
            return;
        }
    }
});
