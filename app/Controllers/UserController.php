<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class UserController extends Controller
{
    private User $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function getAll(): void
    {
        $data = $this->user->getAll();

        if (!$data) {
            $this->jsonResponse(['error' => 'Records not found', 'data' => []], 204);
        }

        $this->jsonResponse(['data' => $data], 200);
    }

    public function getById(int $id): void
    {
        $userData = $this->user->get($id);

        if (!$userData) {
            $this->jsonResponse(['error' => 'User not found', 'data' => []], 404);
        }

        $this->jsonResponse(['data' => $userData], 200);
    }

    public function getByDoc(string $doc): void
    {
        $userData = $this->user->getByDoc($doc);

        if (!$userData) {
            $this->jsonResponse(['error' => 'User not found', 'data' => []], 404);
        }

        $this->jsonResponse(['data' => $userData], 200);
    }
}