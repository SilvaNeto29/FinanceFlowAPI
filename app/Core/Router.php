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

    public function get(string $path, $callback, $middleware = null)
    {
        $this->routes['GET'][$path] = compact('callback', 'middleware');
    }

    public function post(string $path, $callback, $middleware = null)
    {
        $this->routes['POST'][$path] = compact('callback', 'middleware');
    }

    public function put(string $path, $callback, $middleware = null)
    {
        $this->routes['PUT'][$path] = compact('callback', 'middleware');
    }

    public function delete(string $path, $callback, $middleware = null)
    {
        $this->routes['DELETE'][$path] = compact('callback', 'middleware');
    }

    public function resolve()
    {
        $method = $this->request->method();
        $uri = $this->request->uri();

        // Verifica se o método existe nas rotas
        if (!isset($this->routes[$method])) {
            RouterHelper::respond(['error' => 'Method not allowed'], 405);
            return;
        }

        foreach ($this->routes[$method] as $path => $routeData) {
            // Otimiza a conversão de padrão de rota
            $pattern = '#^' . preg_replace('/\{([a-zA-Z_]+)\}/', '([^/]+)', $path) . '$#';

            if (!preg_match($pattern, $uri, $matches)) {
                continue; // Vai para a próxima rota se não corresponder
            }

            // Remove o primeiro elemento (match completo)
            array_shift($matches);

            // Executa middleware se existir
            if (!empty($routeData['middleware'])) {
                $middleware = new $routeData['middleware']();
                if (!$middleware->handle($this->request)) {
                    return; // Interrompe se o middleware falhar
                }
            }

            // Executa o callback
            $callback = $routeData['callback'];

            if (is_array($callback) && count($callback) === 2) {
                // Callback no formato [Controller::class, 'method']
                [$controllerClass, $method] = $callback;
                $controller = new $controllerClass();

                // Adiciona o Request como primeiro parâmetro
                array_unshift($matches, $this->request);

                call_user_func_array([$controller, $method], $matches);
            } else {
                // Callback direto (função/closure)
                call_user_func_array($callback, $matches);
            }

            return;
        }

        // Nenhuma rota encontrada
        RouterHelper::respond(['error' => 'Route not found'], 404);
        exit;
    }
}
