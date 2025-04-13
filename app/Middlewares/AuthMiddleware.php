<?php

namespace App\Middlewares;

use App\Core\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware
{
    public function handle(Request $request): bool
    {
        $auth = $request->header('Authorization');

        if (!$auth || !str_starts_with($auth, 'Bearer ')) {
            http_response_code(401);
            echo json_encode(['error' => 'Token ausente']);
            return false;
        }

        $token = trim(str_replace('Bearer', '', $auth));

        try {
            $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
            if (!isset($decoded->user_id)) {
                http_response_code(401);
                echo json_encode(['error' => 'Token inválido']);
                return false;
            }
            return true;
        } catch (\Exception $e) {
            http_response_code(401);
            echo json_encode(['error' => 'Token inválido']);
            return false;
        }
    }
}
