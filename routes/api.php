<?php

use App\Helpers\RouterHelper;
use App\Middlewares\AuthMiddleware;
use App\Controllers\UserController;
use App\Controllers\AuthController;

$router->get('/api/v1/ping', function () {
    RouterHelper::respond(['pong' => true], 200);
});

$router->get('/api/v1/getall', function (): void {
    $user = new UserController();
    $user->getAll();
});

$router->get('/api/v1/get/{id}', function ($id): void {
    RouterHelper::isInt($id);
    $user = new UserController();
    $user->getById($id);
});

$router->get('/api/v1/getbydoc/{doc}', function ($doc): void {
    RouterHelper::isString($doc);
    $user = new UserController();
    $user->getByDoc($doc);
});

$router->post('/api/v1/auth/login', [AuthController::class, 'login']);
$router->post('/api/v1/auth/signin', [AuthController::class, 'signin']);
// $router->post('/api/v1/auth/refresh', [AuthController::class, 'refresh']);
// $router->post('/api/v1/auth/logout', [AuthController::class, 'logout']);

$router->get('/api/v1/protegido', function () {
    echo json_encode(['ok' => 'rota autenticada']);
}, AuthMiddleware::class);
