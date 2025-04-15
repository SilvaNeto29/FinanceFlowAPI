<?php

namespace App\Helpers;

class RouterHelper
{
    public static function respond(int $statusCode, array $message)
    {
        http_response_code($statusCode);
        echo json_encode($message);
    }
}