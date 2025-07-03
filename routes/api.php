<?php

use App\Helpers\RouterHelper;
use App\Middlewares\AuthMiddleware;
use App\Controllers\UserController;
use App\Controllers\AuthController;
use App\Controllers\AccountsController;

//Test
$router->get('/api/v1/ping', fn () => RouterHelper::respond(['pong' => true], 200));

//Autentication
$router->post('/api/v1/auth/login', [AuthController::class, 'login']);
$router->post('/api/v1/auth/register', [AuthController::class, 'register']);
$router->post('/api/v1/auth/logout', [AuthController::class, 'logout'], AuthMiddleware::class);
$router->post('/api/v1/auth/refresh', [AuthController::class, 'refresh'], AuthMiddleware::class);
$router->get('/api/v1/auth/me', [AuthController::class, 'me'], AuthMiddleware::class);
//todo Reset password

//Admin//////
$router->get('/api/v1/users', function (): void {(new UserController())->getAll();});
$router->get('/api/v1/users/{id}', function ($id): void {RouterHelper::isInt($id);(new UserController())->getById($id);});
$router->get('/api/v1/users/{doc}', function ($doc): void {RouterHelper::isString($doc);(new UserController())->getByDoc($doc);});
//$router->put(); User
//$router->delete(); User
//todo roles
/////////////

//Accounts
$router->get('/api/v1/accounts',[AccountsController::class, 'get']);


// getCards by user
// getCards by card id
// post cards create
// put cards create
// delete cards

// cards add money topup
// topups

//transactions get, get by id, create transaction, delete transaction admin

//notifications get. (recarga realizada, saldo alterado, transação negada, perto de expirar)

// reports by date, money change, recharge history, usage by date

//block card unblock card

//Export transactions pdf csv




$router->get('/api/v1/protegido', function () {
    echo json_encode(['ok' => 'rota autenticada']);
}, AuthMiddleware::class);
