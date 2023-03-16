<?php

namespace App\Http\Controllers;

use App\Constants\Roles;
use App\Models\User;
use App\Services\ReportGenerationService;
use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class GenerateReportController extends Controller
{
    use ResponseTrait, HelpTrait;

    private ReportGenerationService $report_generation_service;

    public function __construct(ReportGenerationService  $generationService)
    {
        $this->report_generation_service = $generationService;
    }

    public function generateReportByActivity($id)
    {
        $data = $this->report_generation_service->generateReportPerActivity($id);

        return $this->sendResponse($data, 200);
    }

    public function downloadReportByActivity($id, Request $request)
    {
        $data = $this->report_generation_service->generateReportPerActivity($id);

        $data[0] = json_decode(json_encode($data[0]));

        $data[1] = json_decode(json_encode($data[1]));

        $auth_user         = auth()->user();

        $organisation      = User::find($auth_user['id'])->organisation;

        $president         = $this->getOrganisationAdministrators(Roles::PRESIDENT);

        $treasurer         = $this->getOrganisationAdministrators(Roles::TREASURER);

        $fin_sec           = $this->getOrganisationAdministrators(Roles::FINANCIAL_SECRETARY);

        if(count($data) > 0) {
            $data = [
                'title'               => 'Financial Report for '.$request->payment_actvity,
                'date'                => date('m/d/Y'),
                'organisation'        => $organisation,
                'incomes'             => $data[0],
                'expenditures'        => $data[1],
                'president'           => $president,
                'organisation_telephone'   => $this->setOrganisationTelephone($organisation->telephone),
                'treasurer'           => $treasurer,
                'fin_secretary'       => $fin_sec,
                'total_income'        => $this->calculateTotal($data[0]),
                'total_amount_given'        => $this->calculateTotalAmountGiven($data[1]),
                'total_amount_spent'        => $this->calculateTotalAmountSpent($data[1]),
                'balance'                   => $this->calculateBalance($data[1]),
                'net_balance'               => ($this->calculateTotal($data[0]) - $this->calculateTotalAmountSpent($data[1])) + $this->calculateBalance($data[1])
            ];
            $pdf = PDF::loadView('Reports.ActivityReport', $data);
        }

        return $pdf->download('Financial_Report.pdf');
    }

    public function generateQuarterlyReport(Request $request)
    {
        $data = $this->report_generation_service->generateQuarterlyReport();
//        dd($data->toDateTimeString());
    }
}
