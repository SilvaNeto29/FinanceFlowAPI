<?php

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class AuthApiTest extends TestCase
{
    private Client $client;
    private static string $testUsername;
    private static string $testEmail;
    private static string $testPassword = 'password123';
    private static $accessToken;
    private static $refreshToken;

    public static function setUpBeforeClass(): void
    {
        self::$testUsername = 'apitestuser_' . uniqid();
        self::$testEmail = self::$testUsername . '@example.com';
    }

    protected function setUp(): void
    {
        $this->client = new Client([
            'base_uri' => 'http://localhost:8000',
            'http_errors' => false,
        ]);
    }

    public function testRegister()
    {
        $response = $this->client->post('/api/v1/auth/register', [
            'json' => [
                'name' => 'API Test User',
                'username' => self::$testUsername,
                'email' => self::$testEmail,
                'password' => self::$testPassword,
            ]
        ]);

        $this->assertEquals(201, $response->getStatusCode(), 'Register should return 201');
        $data = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('user', $data);
        $this->assertArrayHasKey('access_token', $data);
        $this->assertArrayHasKey('refresh_token', $data);

        self::$accessToken = $data['access_token'];
        self::$refreshToken = $data['refresh_token'];
    }

    public function testLogin()
    {
        $response = $this->client->post('/api/v1/auth/login', [
            'json' => [
                'email' => self::$testEmail,
                'password' => self::$testPassword,
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode(), 'Login should return 200');
        $data = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('access_token', $data);
        $this->assertArrayHasKey('refresh_token', $data);

        self::$accessToken = $data['access_token'];
        self::$refreshToken = $data['refresh_token'];
    }

    public function testRefresh()
    {
        $response = $this->client->post('/api/v1/auth/refresh', [
            'json' => [
                'refresh_token' => self::$refreshToken,
            ]
        ]);

        $body = (string) $response->getBody();
        $data = json_decode($body, true);

        // Debug: Mostre o status e o corpo se $data for null
        if ($data === null) {
            $this->fail("Resposta inválida do endpoint /api/v1/auth/refresh. Status: " . $response->getStatusCode() . ". Body: " . $body);
        }

        $this->assertEquals(200, $response->getStatusCode(), 'Refresh should return 200');
        $this->assertArrayHasKey('access_token', $data);
        $this->assertArrayHasKey('refresh_token', $data);

        self::$accessToken = $data['access_token'];
        self::$refreshToken = $data['refresh_token'];
    }

    public function testLogout()
    {
        $response = $this->client->post('/api/v1/auth/logout', [
            'headers' => [
                'Authorization' => 'Bearer ' . self::$accessToken,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'refresh_token' => self::$refreshToken,
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode(), 'Logout should return 200');
        $data = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('message', $data);
    }
}