<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Database;
use Medoo\Medoo;

class TokenBlacklist extends Model
{
    protected Medoo $db;
    protected static string $table = "token_blacklist";

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function isTokenActive(string $token): bool{
        return $this->db->get(static::$table, 'token', ['token' => $token]) ?: false;
    }
}

