<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Services\UserService;


class UserController extends Controller
{
    private UserService $service;

    public function __construct()
    {
        $this->service = new UserService();
    }

    public function getAll(): string
    {
        $data = $this->service->getAllUsers();
        if (!$data) {
            return $this->jsonResponse(['error' => 'Records not found', 'data' => []], 204);
        }
        return $this->jsonResponse(['data' => $data], 200);
    }

    public function getById(int $id): string
    {
        $userData = $this->service->getUserById($id);
        if (!$userData) {
            return $this->jsonResponse(['error' => 'User not found', 'data' => []], 404);
        }
        return $this->jsonResponse(['data' => $userData], 200);
    }

    public function getByDoc(string $doc): string
    {
        $userData = $this->service->getUserByDoc($doc);
        if (!$userData) {
            return $this->jsonResponse(['error' => 'User not found', 'data' => []], 404);
        }
        return $this->jsonResponse(['data' => $userData], 200);
    }

    public function update(int $id, $request): string
    {
        $data = $request->body();
        if (empty($data->name) || empty($data->email)) {
            return $this->jsonResponse(['error' => 'Name and email are required'], 400);
        }
        $userData = $this->service->getUserById($id);
        if (!$userData) {
            return $this->jsonResponse(['error' => 'User not found', 'data' => []], 404);
        }
        $updated = $this->service->updateUser($id, [
            'name' => $data->name,
            'email' => $data->email
        ]);
        if (!$updated) {
            return $this->jsonResponse(['error' => 'Failed to update user', 'data' => []], 500);
        }
        return $this->jsonResponse(['message' => 'User updated successfully'], 200);
    }
    
    public function delete(int $id): string
    {
        $userData = $this->service->getUserById($id);
        if (!$userData) {
            return $this->jsonResponse(['error' => 'User not found', 'data' => []], 404);
        }
        $deleted = $this->service->deleteUser($id);
        if (!$deleted) {
            return $this->jsonResponse(['error' => 'Failed to delete user', 'data' => []], 500);
        }
        return $this->jsonResponse(['message' => 'User deleted successfully'], 200);
    }

}