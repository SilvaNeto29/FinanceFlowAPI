<?php

namespace App\Core;

class Router
{
    private $request;
    private $routes = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function get(string $path, callable $callback, $middleware = null)
    {
        $this->routes['GET'][$path] = compact('callback', 'middleware');
    }

    public function post(string $path, callable $callback, $middleware = null)
    {
        $this->routes['POST'][$path] = compact('callback', 'middleware');
    }

    public function put(string $path, callable $callback, $middleware = null)
    {
        $this->routes['PUT'][$path] = compact('callback', 'middleware');
    }

    public function delete(string $path, callable $callback, $middleware = null)
    {
        $this->routes['DELETE'][$path] = compact('callback', 'middleware');
    }

    public function resolve()
    {
        $method = $this->request->method();
        $uri = $this->request->uri();
        $route = $this->routes[$method][$uri] ?? null;

        if (!$route) {
            http_response_code(404);
            echo json_encode(['error' => 'Rota não encontrada']);
            return;
        }

        if ($route['middleware']) {
            $middleware = new $route['middleware'];
            if (!$middleware->handle($this->request)) return;
        }

        call_user_func($route['callback'], $this->request);
    }
}
