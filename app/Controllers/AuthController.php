<?php

namespace App\Controllers;

use App\Core\Request;
use App\Models\RefreshTokens;
use Firebase\JWT\JWT;
use App\Helpers\RouterHelper;
use App\Models\User;
use Exception;

class AuthController
{
    private const ACCESS_TOKEN_DURATION = 900; // 15 minutes
    private const REFRESH_TOKEN_DURATION = 43200; // 12 hours

    public function register(Request $request): void
    {
        try {
            $data = $request->body();

            // Required fields validation
            if (empty($data->name) || empty($data->username) || empty($data->email) || empty($data->password)) {
                RouterHelper::respond(['error' => 'Name, username, email and password are required'], 400);
                return;
            }

            // Email validation
            if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
                RouterHelper::respond(['error' => 'Invalid email format'], 400);
                return;
            }

            // Password validation (minimum 8 characters)
            if (strlen($data->password) < 8) {
                RouterHelper::respond(['error' => 'Password must be at least 8 characters long'], 400);
                return;
            }

            // Username validation (minimum 4 characters)
            if (strlen($data->username) < 4) {
                RouterHelper::respond(['error' => 'Username must be at least 4 characters long'], 400);
                return;
            }

            // Check if email is already in use
            $user = new User();
            $existingUser = $user->getByEmail($data->email);

            if ($existingUser) {
                RouterHelper::respond(['error' => 'Email already in use'], 409);
                return;
            }

            $existingUser = $user->getByUsername($data->username);

            if ($existingUser) {
                RouterHelper::respond(['error' => 'Username already in use'], 409);
                return;
            }

            // Create password hash
            $passwordHash = password_hash($data->password, PASSWORD_DEFAULT, ['cost' => 12]);

            // Prepare user data
            $userData = [
                'name' => $data->name,
                'username' => $data->username,
                'email' => $data->email,
                'password' => $passwordHash,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $userId = $user->create($userData);

            if (!$userId) {
                throw new Exception('Failed to create user');
            }

            // Get the newly created user
            $user = $user->get($userId);

            // Generate tokens for the new user
            $tokens = $this->generateTokens($user);

            // Store the refresh token
            $refreshTokenData = [
                'user_id' => $userId,
                'token' => $tokens['refresh_token'],
                'expires_at' => $tokens['refresh_expires_at'],
                'revoked' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $tokenData = (new RefreshTokens())->create($refreshTokenData);

            if(!$tokenData) {
                RouterHelper::respond(['error' => 'Token cannot be created'], 500);
            }

            // Respond with tokens and basic user data
            RouterHelper::respond([
                'message' => 'User created successfully',
                'user' => [
                    'id' => $userId,
                    'name' => $user['name'],
                    'email' => $user['email']
                ],
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'],
                'expires_in' => self::ACCESS_TOKEN_DURATION
            ], 201);
        } catch (Exception $e) {
            // Error log
            error_log('Error creating user: ' . $e->getMessage());

            RouterHelper::respond([
                'error' => 'Failed to create user',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generates new access and refresh tokens
     */
    private function generateTokens(array $user): array
    {
        $now = time();
        $accessExpires = $now + self::ACCESS_TOKEN_DURATION;
        $refreshExpires = $now + self::REFRESH_TOKEN_DURATION;

        $accessPayload = [
            'user_id' => $user['id'],
            'email' => $user['email'],
            'iat' => $now,
            'exp' => $accessExpires
        ];

        $refreshPayload = [
            'user_id' => $user['id'],
            'iat' => $now,
            'exp' => $refreshExpires
        ];

        return [
            'access_token' => JWT::encode($accessPayload, $_ENV['JWT_SECRET'], 'HS256'),
            'refresh_token' => JWT::encode($refreshPayload, $_ENV['JWT_REFRESH_SECRET'], 'HS256'),
            'refresh_expires_at' => date('Y-m-d H:i:s', $refreshExpires)
        ];
    }

    /**
     * User login
     */
    public function login(Request $request): void
    {
        $data = $request->body();

        if (!isset($data->email) || !isset($data->password)) {
            RouterHelper::respond(['error' => 'Email and password are required'], 400);
            return;
        }

        $user = new User();
        $userData = $user->getByEmail($data->email);

        if (!$userData || !password_verify($data->password, $userData['password'])) {
            RouterHelper::respond(['error' => 'Invalid credentials'], 401);
            return;
        }

        $tokens = $this->generateTokens($userData);

        // Store the refresh token
        $refreshTokenData = [
            'user_id' => $userData['id'],
            'token' => $tokens['refresh_token'],
            'expires_at' => $tokens['refresh_expires_at'],
            'revoked' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $tokenData = (new RefreshTokens())->create($refreshTokenData);

        if(!$tokenData) {
            RouterHelper::respond(['error' => 'Token cannot be created'], 500);
        }

        RouterHelper::respond([
            'access_token' => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'],
            'expires_in' => self::ACCESS_TOKEN_DURATION
        ], 200);
    }

    /**
     * Token renewal using refresh token
     */
    public function refresh(Request $request): void
    {
        $data = $request->body();

        if (!isset($data->refresh_token)) {
            RouterHelper::respond(['error' => 'Refresh token is required'], 400);
            return;
        }

        try {
            $decoded = JWT::decode($data->refresh_token, new \Firebase\JWT\Key($_ENV['JWT_REFRESH_SECRET'], 'HS256'));

            $refreshToken = new RefreshTokens();
            $refreshTokenData = $refreshToken->getActive($data->refresh_token);

            if (!$refreshTokenData || strtotime($refreshTokenData['expires_at']) < time()) {
                RouterHelper::respond(['error' => 'Invalid refresh token'], 401);
                return;
            }

            $user = (new User())->get($decoded->user_id);

            if (!$user) {
                RouterHelper::respond(['error' => 'User not found'], 404);
                return;
            }

            $tokens = $this->generateTokens($user);

            // Update the refresh token
            $refreshToken->update(
            $refreshTokenData['id'],
            ['token' => $tokens['refresh_token'],
                   'expires_at' => $tokens['refresh_expires_at']]
            );

            RouterHelper::respond([
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'],
                'expires_in' => self::ACCESS_TOKEN_DURATION
            ], 200);
        } catch (Exception $e) {
            RouterHelper::respond(['error' => 'Invalid refresh token'], 401);
        }
    }

    /**
     * User logout
     */
    public function logout(Request $request): void
    {
        $auth = $request->header('Authorization');
        if (!$auth || !str_starts_with($auth, 'Bearer ')) {
            RouterHelper::respond(['error' => 'Missing token'], 401);
            return;
        }

        $token = trim(str_replace('Bearer', '', $auth));
        $refreshToken = $request->body()['refresh_token'] ?? null;

        if ($refreshToken) {
            global $db;
            $db->update(
                'refresh_tokens',
                ['revoked' => 1],
                ['token' => $refreshToken]
            );
        }

        $this->addToBlacklist($token);
        RouterHelper::respond(['message' => 'Logged out successfully'], 200);
    }

    /**
     * Add token to blacklist
     */
    private function addToBlacklist(string $token): void
    {
        try {
            $decoded = JWT::decode($token, new \Firebase\JWT\Key($_ENV['JWT_SECRET'], 'HS256'));

            global $db;
            $db->insert('token_blacklist', [
                'token' => $token,
                'expires_at' => date('Y-m-d H:i:s', $decoded->exp),
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            // Invalid token, ignore
        }
    }
}