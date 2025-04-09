<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionHistoryRequest;
use App\Interfaces\TransactionHistoryInterface;
use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;

class TransactionHistoryController extends Controller
{
    use ResponseTrait, HelpTrait;
    private TransactionHistoryInterface $transactionHistory;

    public function __construct(TransactionHistoryInterface $transactionHistory)
    {
        $this->transactionHistory = $transactionHistory;
    }

    public function createTransactionHistory(TransactionHistoryRequest $request){
        $data = $this->transactionHistory->createTransactionHistory($request);
        return $this->sendResponse(($data), 200);
    }


}
