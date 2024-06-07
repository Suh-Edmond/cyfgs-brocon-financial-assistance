<?php

namespace App\Http\Controllers;

use App\Services\BalanceSheetService;
use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class BalanceSheetController extends Controller
{
    use ResponseTrait, HelpTrait;

    private BalanceSheetService $balanceSheetService;

    public function __construct(BalanceSheetService $balanceSheetService)
    {
        $this->balanceSheetService = $balanceSheetService;
    }


    public function generateBalanceSheet(Request $request)
    {
        $data = $this->balanceSheetService->generateBalanceSheet($request);
        return $this->sendResponse($data, 200);
    }

    public function downloadBalanceSheet(Request $request)
    {
        $data = $this->balanceSheetService->downloadBalanceSheet($request);
//        dd($data);
    }
}
