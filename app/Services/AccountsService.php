<?php

namespace App\Services;
use App\Models\Accounts;

class AccountsService {

    private Accounts $accounts;

    public function __construct(Accounts $accounts) {
        $this->accounts = $accounts;
    }

    public function getAccounts() : array {
        return $this->accounts->all();
    }
}