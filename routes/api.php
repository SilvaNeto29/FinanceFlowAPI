<?php

use App\Middlewares\AuthMiddleware;
use App\Models\User;

$router->get('/api/ping', function () {
    echo json_encode(['pong' => true]);
});

$router->get('/db', function () {
    $user = new User();
    echo json_encode($user->busca());
});   

$router->get('/api/protegido', function () {
    echo json_encode(['ok' => 'rota autenticada']);
}, AuthMiddleware::class);
