<?php

namespace App\Controllers;
use App\Core\Controller;
use App\Helpers\LogHelper;
use App\Services\AccountsService;

class AccountsController extends Controller {

    private AccountsService $service;

    public function __constructor(AccountsService $accountsService) {
        $this->service = $accountsService;
    }

    public function get(): ?string{

        try {

            $data = $this->service->getAccounts();
            return $this->jsonResponse(['data' => $data]);

        } catch (\Throwable $th) {

            LogHelper::error('AccountsController@get failed', [
                'message' => $th->getMessage(),
                'trace'   => $th->getTraceAsString(),
            ]);
            return $this->jsonResponse(['error' => 'Internal Error'], 500);
        }
    }

}