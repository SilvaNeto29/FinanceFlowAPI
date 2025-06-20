<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Database;
use Medoo\Medoo;

class User extends Model
{
    protected Medoo $db;

    public function __construct()
    {
        $this->db = Database::connect();
        static::$table = "users";
    }

    public function get(int $id)
    {
        return $this->db->get(
            static::$table,
            ['name', 'email'],
            ['id' => $id]
        );
    }

    public function getByusername(string $username)
    {
        return $this->db->get(
            static::$table,
            ['name', 'email', 'username', 'age', 'phone'],
            ['username' => $username]
        );
    }

    public function getByEmail(string $email)
    {
        return $this->db->get(
            'users',
            ['name', 'email'],
            ['email' => $email]
        );
    }

    public function getAll()
    {
        return $this->db->select(
            static::$table,
            ['name', 'email'],
        );
    }
}
