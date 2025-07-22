<?php

namespace App\Core;

/**
 * Class Controller
 * @package App\Core
 */
class Controller
{
    /**
     * Render a JSON response.
     *
     * @param array<string, mixed> $data
     * @param int $status
     */
    public function jsonResponse(array $data, int $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        die;
    }

    // public static function redirect(string $url, int $statusCode = 0, array $headers = []);
}
