<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Controllers\UserController;
use App\Models\User;

class UserControllerTest extends TestCase
{
    private UserController $controller;

    protected function setUp(): void
    {
        $this->controller = new UserController();
    }

    public function testGetAllReturnsDataOrError()
    {
        $response = $this->controller->getAll();
        $this->assertIsString($response, 'Response should be a string');
        $data = json_decode($response, true);
        $this->assertIsArray($data, 'Decoded response should be an array');
        $this->assertTrue(isset($data['data']) || isset($data['error']), 'Response should contain data or error');
        if (isset($data['error'])) {
            $this->assertEquals('Records not found', $data['error'], 'Error message should match');
        } else {
            $this->assertIsArray($data['data'], 'Data should be an array');
        }
    }

    public function testGetByIdReturnsDataOrError()
    {
        $response = $this->controller->getById(1);
        $this->assertIsString($response, 'Response should be a string');
        $data = json_decode($response, true);
        $this->assertIsArray($data, 'Decoded response should be an array');
        $this->assertTrue(isset($data['data']) || isset($data['error']), 'Response should contain data or error');
        if (isset($data['error'])) {
            $this->assertEquals('User not found', $data['error'], 'Error message should match');
        } else {
            $this->assertIsArray($data['data'], 'Data should be an array');
        }
    }

    public function testGetByDocReturnsDataOrError()
    {
        $response = $this->controller->getByDoc('12345678900');
        $this->assertIsString($response, 'Response should be a string');
        $data = json_decode($response, true);
        $this->assertIsArray($data, 'Decoded response should be an array');
        $this->assertTrue(isset($data['data']) || isset($data['error']), 'Response should contain data or error');
        if (isset($data['error'])) {
            $this->assertEquals('User not found', $data['error'], 'Error message should match');
        } else {
            $this->assertIsArray($data['data'], 'Data should be an array');
        }
    }
}
