<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use Firebase\JWT\JWT;
use App\Helpers\RouterHelper;
use App\Models\User;
use Exception;

class AuthController
{
    private const ACCESS_TOKEN_DURATION = 900; // 15 minutos
    private const REFRESH_TOKEN_DURATION = 604800; // 7 dias

    public function signin(Request $request): void
    {
        try {
            $data = $request->body();

            // Validação dos campos obrigatórios
            if (empty($data->name) || empty($data->username) || empty($data->email) || empty($data->password)) {
                RouterHelper::respond(['error' => 'Name, username, email and password are required'], 400);
                return;
            }

            // Validação de email
            if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
                RouterHelper::respond(['error' => 'Invalid email format'], 400);
                return;
            }

            // Validação de senha (mínimo 8 caracteres)
            if (strlen($data->password) < 8) {
                RouterHelper::respond(['error' => 'Password must be at least 8 characters long'], 400);
                return;
            }

            if (strlen($data->username) < 4) {
                RouterHelper::respond(['error' => 'Username must be at least 4 characters long'], 400);
                return;
            }

            // Verifica se o email já está em uso
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

            // Cria o hash da senha
            $passwordHash = password_hash($data->password, PASSWORD_DEFAULT, ['cost' => 12]);

            // Prepara os dados do usuário
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

            // Busca o usuário recém-criado
            $user = $user->get($userId);

            // Gera tokens para o novo usuário
            $tokens = $this->generateTokens($user);

            // Armazena o refresh token
            $refreshTokenData = [
                'user_id' => $userId,
                'token' => $tokens['refresh_token'],
                'expires_at' => $tokens['refresh_expires_at'],
                'revoked' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $user->insert('refresh_tokens', $refreshTokenData);

            // Responde com os tokens e dados básicos do usuário
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
            // Log do erro
            error_log('Error creating user: ' . $e->getMessage());

            RouterHelper::respond([
                'error' => 'Failed to create user',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gera novos tokens de acesso e refresh
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

        // Busca o usuário pelo email
        global $db;
        $user = $db->get('users', '*', ['email' => $data->email]);

        if (!$user || !password_verify($data->password, $user['password'])) {
            RouterHelper::respond(['error' => 'Invalid credentials'], 401);
            return;
        }

        $tokens = $this->generateTokens($user);

        // Armazena o refresh token
        $refreshTokenData = [
            'user_id' => $user['id'],
            'token' => $tokens['refresh_token'],
            'expires_at' => $tokens['refresh_expires_at'],
            'revoked' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $db->insert('refresh_tokens', $refreshTokenData);

        RouterHelper::respond([
            'access_token' => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'],
            'expires_in' => self::ACCESS_TOKEN_DURATION
        ], 200);
    }

    /**
     * Renovação de token usando refresh token
     */
    public function refresh(Request $request): void
    {
        $data = $request->body();

        if (!isset($data['refresh_token'])) {
            RouterHelper::respond(['error' => 'Refresh token is required'], 400);
            return;
        }

        try {
            $decoded = JWT::decode($data['refresh_token'], new \Firebase\JWT\Key($_ENV['JWT_REFRESH_SECRET'], 'HS256'));

            global $db;
            $refreshToken = $db->get('refresh_tokens', '*', [
                'token' => $data['refresh_token'],
                'revoked' => 0
            ]);

            if (!$refreshToken || strtotime($refreshToken['expires_at']) < time()) {
                RouterHelper::respond(['error' => 'Invalid refresh token'], 401);
                return;
            }

            $user = $db->get('users', '*', ['id' => $decoded->user_id]);

            if (!$user) {
                RouterHelper::respond(['error' => 'User not found'], 404);
                return;
            }

            $tokens = $this->generateTokens($user);

            // Atualiza o refresh token
            $db->update(
                'refresh_tokens',
                [
                    'token' => $tokens['refresh_token'],
                    'expires_at' => $tokens['refresh_expires_at']
                ],
                ['id' => $refreshToken['id']]
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
     * Logout do usuário
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
     * Adiciona token à blacklist
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
            // Token inválido, ignora
        }
    }
}