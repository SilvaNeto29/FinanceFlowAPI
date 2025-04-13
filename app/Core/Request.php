<?php

namespace App\Core;

class Request
{
    public function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function uri(): string
    {
        return strtok($_SERVER['REQUEST_URI'], '?');
    }

    public function body()
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    public function header(string $key): ?string
    {
        $headers = getallheaders();
        return $headers[$key] ?? null;
    }
}
