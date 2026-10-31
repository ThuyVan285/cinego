<?php
declare(strict_types=1);

// Cấu hình mặc định của XAMPP. Có thể ghi đè bằng biến môi trường.
return [
    'db_host' => getenv('CINEGO_DB_HOST') ?: '127.0.0.1',
    'db_port' => getenv('CINEGO_DB_PORT') ?: '3306',
    'db_name' => getenv('CINEGO_DB_NAME') ?: 'cinego',
    'db_user' => getenv('CINEGO_DB_USER') ?: 'root',
    'db_pass' => getenv('CINEGO_DB_PASS') ?: '',
];
