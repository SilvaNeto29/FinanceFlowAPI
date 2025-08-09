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
            return $data ? 
                $this->jsonResponse(['data' => $data]) :
                $this->jsonResponse(['error' => 'Account not found'], 404);

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

            LogHelper::error('AccountsController@all failed', [
                'message' => $th->getMessage(),
                'trace'   => $th->getTraceAsString(),
            ]);
            return $this->jsonResponse(['error' => 'Internal Error'], 500);
        }
    }

    public function create(Request $request): ?string
    {
        try {
 
            $data = $this->service->create($request->body());
            return $data > 0 ? 
                $this->jsonResponse(['message' => 'Account created'], 201) :
                $this->jsonResponse(['error' => 'Internal Error'], 500);

        } catch (\Throwable $th) {
            
            LogHelper::error('AccountsController@create failed', [
                'message' => $th->getMessage(),
                'trace'   => $th->getTraceAsString(),
            ]);
            return $this->jsonResponse(['error' => 'Internal Error'], 500);
        }
    }

    public function edit(Request $request): ?string
    {
        try {

            $data = $this->service->edit($request->body());

            return $data ? 
                $this->jsonResponse(['message' => 'Account updated']) :
                $this->jsonResponse(['error' => 'Internal Error'], 500);

        } catch (\Throwable $th) {

            LogHelper::error('AccountsController@edit failed', [
                'message' => $th->getMessage(),
                'trace'   => $th->getTraceAsString(),
            ]);
            return $this->jsonResponse(['error' => 'Internal Error'], 500);
        }
    }

    public function delete(Request $request): ?string
    {
        try {

            $data = $this->service->delete($request->body());
            return $data ? 
                $this->jsonResponse(['message' => 'Account deleted']) :
                $this->jsonResponse(['error' => 'Internal Error'], 500);

        } catch (\Throwable $th) {

            LogHelper::error('AccountsController@delete failed', [
                'message' => $th->getMessage(),
                'trace'   => $th->getTraceAsString(),
            ]);
            return $this->jsonResponse(['error' => 'Internal Error'], 500);
        }
    }
}