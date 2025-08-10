<?php

namespace App\Services;

use App\Models\User;
use App\Helpers\RouterHelper;
use App\Helpers\LogHelper;

class UserService
{
    private User $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function getAllUsers(): array
    {
        return $this->user->getAll();
    }

    public function getUserById(int $id): ?array
    {
        return $this->user->get($id);
    }

    public function getUserByDoc(string $doc): ?array
    {
        return $this->user->getByDoc($doc);
    }

    public function updateUser(int $id, array $data): bool
    {
        return $this->user->update($id, $data);
    }
    public function deleteUser(int $id, string $email): array
    {
        return $this->user->deleteWhere(['id' => $id, 'email' => $email]);
    }
}
