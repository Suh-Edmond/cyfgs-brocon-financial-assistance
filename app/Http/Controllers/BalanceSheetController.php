<?php

namespace App\Http\Controllers;

use App\Constants\Roles;
use App\Services\BalanceSheetService;
use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Barryvdh\DomPDF\Facade\Pdf;
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
        $balance_sheet_data = json_decode(json_encode($this->balanceSheetService->downloadBalanceSheet($request)));
        $organisation      = $request->user()->organisation;

        $data = [
            'title'             => "Balance Sheet for the Year ".$request->year,
            'date'              => date('d/m/Y'),
            'organisation'      => $organisation,
            'contributions'     => $balance_sheet_data->members_contributions,
            'organisation_telephone'   => $this->setOrganisationTelephone($organisation->telephone),
            'president'         => $balance_sheet_data->president,
            'treasurer'         => $balance_sheet_data->treasurer,
            'fin_secretary'     => $balance_sheet_data->fin_sec,
            'organisation_logo' => $organisation->logo,
            'columns'           => $balance_sheet_data->column_names,
            'column_title'      => "Column Definitions",
            'yearly_total'      => $balance_sheet_data->total_yearly_contribution,
            'yearly_expected'   => $balance_sheet_data->total_year_expected_amount,
            'year_balance'      => $balance_sheet_data->total_yearly_balance,
            'col_span'          => 2,
        ];

        $pdf = PDF::loadView('BalanceSheet.BalanceSheet', $data);
        $pdf->output();
        $domPdf = $pdf->getDomPDF();
        $canvas = $domPdf->getCanvas();
        $canvas->page_text(10, $canvas->get_height() - 20, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, [0, 0, 0]);

        return $pdf->download('BalanceSheet.pdf');
    }
}
