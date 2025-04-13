<?php

use App\Middlewares\AuthMiddleware;

$router->get('/api/ping', function () {
    echo json_encode(['pong' => true]);
});

$router->get('/api/protegido', function () {
    echo json_encode(['ok' => 'rota autenticada']);
}, AuthMiddleware::class);
