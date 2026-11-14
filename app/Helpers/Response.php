<?php
declare(strict_types=1);

// ---------- Phản hồi: chuyển hướng, render view, JSON ----------
function redirect(string $to): never
{
    header('Location: ' . $to);
    exit;
}

/** Render app/Views/{$view}.php bên trong layout (mặc định layouts/main). */
function render(string $view, array $vars = [], string $layout = 'main'): void
{
    $vars += ['title' => APP_NAME, 'nav' => '', 'bare' => false];
    extract($vars, EXTR_SKIP);
    ob_start();
    require BASE_PATH . '/app/Views/' . $view . '.php';
    $content = ob_get_clean();
    require BASE_PATH . '/app/Views/layouts/' . $layout . '.php';
}

/** Nhúng component app/Views/components/{$name}.php */
function component(string $name, array $vars = []): void
{
    extract($vars, EXTR_SKIP);
    require BASE_PATH . '/app/Views/components/' . $name . '.php';
}

function json(mixed $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function notFound(): never
{
    http_response_code(404);
    render('errors/error', ['title' => 'Không tìm thấy trang', 'message' => 'Trang bạn tìm không tồn tại hoặc đã được di chuyển.']);
    exit;
}
