<?php

namespace App\Helpers;

class RouterHelper
{
    public static function respond(array $message, int $statusCode)
    {
        http_response_code($statusCode);
        echo json_encode($message);
        exit;
    }

    public static function isInt($i){
        if (!is_numeric($i) || (int) $i <= 0) {
            static::respond(['error'=> 'Unformated ID'],400);
        }
    }

    public static function isString($i){
        if (!is_string($i) || (string) $i <= 0) {
            static::respond(['error'=> 'Unformated Doc'],400);
        }
    }
}