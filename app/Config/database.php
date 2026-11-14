<?php
declare(strict_types=1);

// Mặc định theo XAMPP. Ghi đè bằng file .env (DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS).
return [
    'db_host' => env('DB_HOST', '127.0.0.1'),
    'db_port' => env('DB_PORT', '3306'),
    'db_name' => env('DB_NAME', 'cinego'),
    'db_user' => env('DB_USER', 'root'),
    'db_pass' => env('DB_PASS', ''),
];
