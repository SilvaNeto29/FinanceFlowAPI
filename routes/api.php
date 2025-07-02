<?php

use App\Helpers\RouterHelper;
use App\Middlewares\AuthMiddleware;
use App\Controllers\UserController;
use App\Controllers\AuthController;

$router->get('/api/v1/ping', fn () => RouterHelper::respond(['pong' => true], 200));

$router->get('/api/v1/users', function (): void {
    (new UserController())->getAll();
});

$router->get('/api/v1/users/{id}', function ($id): void {
    RouterHelper::isInt($id);
    (new UserController())->getById($id);
});

$router->get('/api/v1/users/{doc}', function ($doc): void {
    RouterHelper::isString($doc);
    (new UserController())->getByDoc($doc);
});

$router->post('/api/v1/auth/login', [AuthController::class, 'login']);
$router->post('/api/v1/auth/register', [AuthController::class, 'register']);
$router->post('/api/v1/auth/logout', [AuthController::class, 'logout'], AuthMiddleware::class);
$router->post('/api/v1/auth/refresh', [AuthController::class, 'refresh'], AuthMiddleware::class);
$router->get('/api/v1/auth/me', [AuthController::class, 'me'], AuthMiddleware::class);


$router->get('/api/v1/protegido', function () {
    echo json_encode(['ok' => 'rota autenticada']);
}, AuthMiddleware::class);
