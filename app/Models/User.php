<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Database;
use Medoo\Medoo;

class User extends Model {
    protected Medoo $db;
    protected string $table;

    public function __construct() {
        $this->db = Database::connect();
        $this->table = "user";
    }

    public function busca() {
        return $this->db->get($this->table, "*");
    }    
}