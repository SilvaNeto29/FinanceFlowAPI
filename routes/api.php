<?php

/**
 * The router is prepared to handle query params.
 * To debug the resolve method:
 * /Core/Router.php
 */

use App\Helpers\RouterHelper;
use App\Middlewares\AuthMiddleware;
use App\Controllers\UserController;

$router->get('/api/ping', function () {
    RouterHelper::respond(['pong' => true],200);
});

$router->get('/api/getall', function (): void {
    $user = new UserController();
    $user->getAll();
});   

$router->get('/api/get/{id}', function ($id): void {
    RouterHelper::isInt($id);
    $user = new UserController();
    $user->getById($id);
});  

$router->get('/api/protegido', function () {
    echo json_encode(['ok' => 'rota autenticada']);
}, AuthMiddleware::class);
