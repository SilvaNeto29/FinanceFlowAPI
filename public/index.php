<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\Request;

// Ajuste o caminho para o arquivo .env
$dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$request = new Request();
$router = new Router($request);

// Carrega as rotas
require __DIR__ . '/../routes/api.php';

$router->resolve();
