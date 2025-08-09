<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Controllers\AccountsController;
use App\Core\Request;

class AccountsControllerTest extends TestCase
{
    private AccountsController $controller;

    protected function setUp(): void
    {
        $this->controller = new AccountsController();
    }

    public function testGetReturnsAccountDataOrNotFound()
    {
        $response = $this->controller->get(1);
        $this->assertIsString($response);
        $data = json_decode($response, true);
        $this->assertIsArray($data);
        $this->assertTrue(isset($data['data']) || isset($data['error']));
    }

    public function testAllReturnsAccountsList()
    {
        $response = $this->controller->all();
        $this->assertIsString($response);
        $data = json_decode($response, true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('data', $data);
        $this->assertIsArray($data['data']);
    }

    public function testCreateReturnsCreatedOrError()
    {
        $request = new class extends Request {
            public function body() {
                return (object)[
                    'user_id' => 1,
                    'bank_code' => 'BRADESCO',
                    'agency_number' => '0001',
                    'account_number' => '12345678-9',
                    'type' => 'CHECKING',
                    'balance' => 10000,
                    'status' => 'ACTIVE',
                    'name' => 'Test Account'
                ];
            }
        };
        $response = $this->controller->create($request);
        $this->assertIsString($response);
        $data = json_decode($response, true);
        $this->assertIsArray($data);
        $this->assertTrue(isset($data['message']) || isset($data['error']));
    }

    public function testEditReturnsUpdatedOrError()
    {
        $request = new class extends Request {
            public function body() {
                return (object)['id' => 1, 'name' => 'Updated Account'];
            }
        };
        $response = $this->controller->edit($request);
        $this->assertIsString($response);
        $data = json_decode($response, true);
        $this->assertIsArray($data);
        $this->assertTrue(isset($data['message']) || isset($data['error']));
    }

    public function testDeleteReturnsDeletedOrError()
    {
        $request = new class extends Request {
            public function body() {
                return (object)[
                    'id' => 1,
                    'user_id' => 1
                ];
            }
        };
        $response = $this->controller->delete($request);
        $this->assertIsString($response);
        $data = json_decode($response, true);
        $this->assertIsArray($data);
        $this->assertTrue(isset($data['message']) || isset($data['error']));
    }
}
