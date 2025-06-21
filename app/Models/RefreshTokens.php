<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Database;
use Medoo\Medoo;

class RefreshTokens extends Model
{
    protected Medoo $db;
    protected static string $table = "refresh_tokens";

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getActive(string $token){
        return $this->db->get(
            static::$table,
            '*',
            ['token' => $token,'revoked' => 0]
        );
    }
}

