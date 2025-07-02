<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Database;
use Medoo\Medoo;

class User extends Model
{
    protected Medoo $db;
    protected static string $table = "users";

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function get(int $id)
    {
        return $this->db->get(
            static::$table,
            ['id','name', 'email'],
            ['id' => $id]
        );
    }
    public function getByUsername(string $username)
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
            static::$table,
            ['id','email', 'password'],
            ['email' => $email]
        );
    }

    public function getByDoc(string $doc)
    {
        return $this->db->get(
            static::$table,
            ['name', 'doc'],
            ['doc' => $doc]
        );
    }

    public function getAll()
    {
        /** @phpstan-ignore-next-line */
        return $this->db->select(
            static::$table,
            ['name', 'email'],
        );
    }
}
