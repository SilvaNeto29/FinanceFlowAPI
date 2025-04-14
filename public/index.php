<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\Request;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$request = new Request();
$router = new Router($request);

// Carrega as rotas
require __DIR__ . '/../routes/api.php';

$router->resolve();
