<?php

namespace App\Controllers;
use App\Core\Controller;
use App\Core\Request;
use App\Helpers\LogHelper;
use App\Services\AccountsService;

class AccountsController extends Controller {

    private AccountsService $service;

    public function __construct() {
        $this->service = new AccountsService();
    }

    public function get($id): ?string{

        try {

            $data = $this->service->getAccounts($id);
            return $this->jsonResponse(['data' => $data]);

        } catch (\Throwable $th) {

            LogHelper::error('AccountsController@get failed', [
                'message' => $th->getMessage(),
                'trace'   => $th->getTraceAsString(),
            ]);
            return $this->jsonResponse(['error' => 'Internal Error'], 500);
        }
    }

    public function all(): ?string{

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

    public function create(Request $request) : string {
        
        try {
            
            $data = $this->service->create();

        } catch (\Throwable $th) {
            //throw $th;
        }

        return '';
    }
}