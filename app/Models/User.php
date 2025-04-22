<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Database;
use Medoo\Medoo;

class User extends Model {
    protected Medoo $db;

    public function __construct() {
        $this->db = Database::connect();
        static::$table = "user";
    }

    public function get(int $id) {
        return $this->db->get(
            static::$table, 
            ['name','doc','age','phone'], 
            ['id' => $id]
        );
    }
    
    public function getAll() {
        return $this->db->select(
            static::$table ,
            ['name','doc','age','phone']
        );
    }   
}