<?php

namespace App\Services;

use App\Models\RefreshTokens;
use App\Models\TokenBlacklist;
use Firebase\JWT\JWT;
use App\Helpers\RouterHelper;
use App\Models\User;
use Exception;

class AuthService {

    public function isEmailInUse(): bool {
        return true;
    }

    public function getUserIdFromToken(string $token): ?int {

        $token = trim(str_replace('Bearer ', '', $token));

        $decoded = JWT::decode($token, new \Firebase\JWT\Key($_ENV['JWT_SECRET'], 'HS256'));
        return $decoded->user_id ?? null;
    }
}