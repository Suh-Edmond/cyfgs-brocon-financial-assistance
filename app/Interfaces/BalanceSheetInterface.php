<?php


namespace App\Interfaces;
interface BalanceSheetInterface {
    public function generateBalanceSheet($request);

    public function downloadBalanceSheet($request);
}
