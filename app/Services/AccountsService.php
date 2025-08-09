<?php

namespace App\Services;

use App\Core\Request;
use App\Helpers\RouterHelper;
use App\Models\Accounts;
use App\Helpers\LogHelper;


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

    public function create($request) : int {

        if 
        (   
            !$request->user_id       || 
            !$request->bank_code     ||
            !$request->agency_number || 
            !$request->account_number
        ){
            RouterHelper::respond(["The body is incomplete or poorly formatted"], 400);
        }

        $data = $this->accounts->create([
            "user_id" => $request->user_id,
            "bank_code" => $request->bank_code ?: 99,
            "agency_number" => $request->agency_number,
            "account_number" => $request->account_number,
            "type" => $request->type ?: "CHECKING" ,
            "balance" => $request->balance ?: 0.00,
            "status" => $request->status ?: "ACTIVE",
            "created_at" => date("d-m-Y H:i:s"),
            "updated_at" => date("d-m-Y H:i:s"),
        ]);

        return $data;
    }

    public function edit($request): bool
    {
        // Validação das chaves obrigatórias
        $required = [
            'id',
            'user_id',
            'bank_code',
            'agency_number',
            'account_number',
            'type',
            'balance',
            'status'
        ];
        foreach ($required as $key) {
            if (!isset($request->$key)) {
                return RouterHelper::respond(["Missing or invalid field: $key"], 400);
            }
        }

        $data = $this->accounts->updateWhere([
            "bank_code" => $request->bank_code,
            "agency_number" => $request->agency_number,
            "account_number" => $request->account_number,
            "type" => $request->type,
            "balance" => $request->balance,
            "status" => $request->status,
            "updated_at" => date("d-m-Y H:i:s"),
        ], ['id' => $request->id, 'user_id' => $request->user_id]);

        return $data;
    }

    public function delete($request): array
    {
        try {

            if(isset($request->id)) {
                RouterHelper::isInt($request->id, ['error' => 'Unformated id']);
            } else {
                return RouterHelper::respond(['error' => 'ID is required'], 400);
            }

            $data = $this->accounts->deleteWhere(['id' => $request->id, 'user_id' => $request->user_id]);
            
            if(!$data['success']) {
                return RouterHelper::respond(['error' => 'Failed to delete account'], 500);
            }
            
            return $data;

        } catch (\Throwable $th) {

            LogHelper::error('AccountsService@delete failed', [
                'message' => $th->getMessage(),
                'trace'   => $th->getTraceAsString(),
            ]);
            return RouterHelper::respond(['error' => 'Internal Error'], 500);
        }
    }
}