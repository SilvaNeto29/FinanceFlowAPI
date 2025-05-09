<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class UserController extends Controller
{

    public function getAll(): void
    {
        $user = new User;
        $data = $user->getAll();

        if (!$data) {
            $this->jsonResponse(['error' => 'Records not found', 'data' => []], 204);
        }

        $this->jsonResponse(['data' => $data], 200);
    }

    public function getById(int $id): void
    {

        $id = (int) $id;
        $user = new User();
        $userData = $user->get($id);

        if (!$userData) {
            $this->jsonResponse(['error' => 'User not found', 'data' => []], 404);
        }

        $this->jsonResponse(['data' => $userData], 200);
    }

    public function getByDoc(string $doc): void
    {

        $doc = (string) $doc;
        $user = new User();
        $userData = $user->getByDoc($doc);

        if (!$userData) {
            $this->jsonResponse(['error' => 'User not found', 'data' => []], 404);
        }

        $this->jsonResponse(['data' => $userData], 200);
    }

    
}