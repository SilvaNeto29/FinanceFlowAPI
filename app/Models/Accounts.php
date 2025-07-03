<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Database;
use Medoo\Medoo;

class Accounts extends Model
{
    protected Medoo $db;
    private const TABLE = 'accounts';

    public function __construct()
    {
        $this->db = Database::connect();
    }


}
