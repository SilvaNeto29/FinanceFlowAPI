<?php

namespace App\Core;
use App\Helpers\RouterHelper;

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

        foreach ($this->routes[$method] ?? [] as $path => $routeData) {

            $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '([^\/]+)', $path);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                $params = $matches; 

                if ($routeData['middleware']) {
                    $middleware = new $routeData['middleware'];
                    if (!$middleware->handle($this->request/*, $params*/)) return;
                }
                call_user_func_array($routeData['callback'], $params);
                return; 
            }
        }

        RouterHelper::respond(['error' => 'Route not found'],404);
        exit;
    }
}
