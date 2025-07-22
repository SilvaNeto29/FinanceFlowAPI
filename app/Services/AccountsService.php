<?php

namespace App\Services;

use App\Helpers\RouterHelper;
use App\Models\Accounts;

class AccountsService {

    private Accounts $accounts;

    public function __construct() {
        $this->accounts = new Accounts();
    }

    public function getAccounts($id = null) : array {
        if ($id) {
            RouterHelper::isInt($id, ['error' => 'Unformated id']);
        }
        return $id ? $this->accounts->get($id) : $this->accounts->get();
    }

    public function create(){
        
        

        $userId = '1';
        $this->accounts->create([
            "user_id" => $userId,

        ]);
    }
}