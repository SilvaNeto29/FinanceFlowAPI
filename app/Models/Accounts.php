<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Database;
use Medoo\Medoo;

class Accounts extends Model
{
    protected Medoo $db;
    protected static string $table = "accounts";

    public function __construct()
    {
        $this->db = Database::connect();
        
    }

    public function get ($id = null){
        return $id ? 
            $this->db->select(self::$table, '*', ['id' => $id]) :
            $this->db->select(self::$table, '*');
    }

}
