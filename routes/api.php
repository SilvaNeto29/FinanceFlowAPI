<?php

use App\Middlewares\AuthMiddleware;
use App\Models\User;

$router->get('/api/ping', function () {
    echo json_encode(['pong' => true]);
});

$router->get('/getall', function (): void {
    $user = new User();
    echo json_encode(['data' => $user->getAll()]);
});   

$router->get('/get/*', function (int $id): void {

    $uri = $_SERVER['REQUEST_URI']; // Exemplo: "/get/2"
    $parts = explode('/', $uri);
    $id = (int) end($parts); // Obtém o último segmento da URL e converte para inteiro

    if ($id === 0) {
        echo json_encode(['error' => 'ID inválido ou não fornecido']);
        return;
    }

    $user = new User();
    echo json_encode($user->get($id));
});  


$router->get('/api/protegido', function () {
    echo json_encode(['ok' => 'rota autenticada']);
}, AuthMiddleware::class);
