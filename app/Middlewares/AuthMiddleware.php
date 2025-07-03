<?php

namespace App\Middlewares;

use App\Core\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\TokenBlacklist;

class AuthMiddleware
{
    public function handle(Request $request): bool
    {
        $auth = $request->header('Authorization');

        if (!$auth || !str_starts_with($auth, 'Bearer ')) {
            http_response_code(401);
            echo json_encode(['error' => 'Missing Token']);
            return false;
        }

        $token = trim(str_replace('Bearer', '', $auth));

        try {
            if ($this->isTokenBlacklisted($token)) {
                http_response_code(401);
                echo json_encode(['error' => 'Token revoked']);
                return false;
            }

            $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
            if (!isset($decoded->user_id)) {
                http_response_code(401);
                echo json_encode(['error' => 'Invalid Token']);
                return false;
            }

            //(new TokenBlacklist())->renewToken($token);

            return true;
        } catch (\Exception $e) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid Token']);
            return false;
        }
    }

    private function isTokenBlacklisted(string $token): bool
    {
        return (new TokenBlacklist())->isTokenActive($token);
    }
}
