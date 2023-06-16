<?php

namespace App\Http\Controllers;

use App\Constants\Roles;
use App\Models\User;
use App\Services\ReportGenerationService;
use App\Services\SessionService;
use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class GenerateReportController extends Controller
{
    use ResponseTrait, HelpTrait;

    private ReportGenerationService $report_generation_service;
    private SessionService  $session_service;

    public function __construct(ReportGenerationService  $generationService, SessionService $sessionService)
    {
        $this->report_generation_service = $generationService;
        $this->session_service = $sessionService;
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
         if(count($data) > 0) {
            $data = [
                'title'               => 'Financial Report for '.$request->payment_actvity. " ". $this->session_service->getCurrentSession()->year,
                'date'                => date('m/d/Y'),
                'organisation'        => $organisation,
                'incomes'             => $data[0],
                'expenditures'        => $data[1],
                'president'           => $data[7]['president'],
                'organisation_telephone' => $this->setOrganisationTelephone($organisation->telephone),
                'fin_secretary'       => $data[8]['fin_sec'],
                'treasurer'           => $data[9]['treasurer'],
                'total_income'        => $data[2]['total_income'],
                'total_amount_given'  => $data[3]['total_amount_given'],
                'total_amount_spent'  => $data[4]['total_amount_spent'],
                'balance'             => $data[5]['balance'],
                'total_balance'       => $data[6]['total_balance'],

            ];
            $pdf = PDF::loadView('Reports.ActivityReport', $data);
        }
        return $pdf->download('Activity_Financial_Report.pdf');
    }


    public function generateQuarterlyReport(Request $request)
    {

        $data = $this->report_generation_service->generateQuarterlyReport($request);

        return $this->sendResponse($data, 200);
    }

    public function downloadQuarterlyReport(Request $request)
    {
        $data = $this->report_generation_service->downloadQuarterlyReport($request);

        $auth_user = auth()->user();

        $organisation = User::find($auth_user['id'])->organisation;

        $president = $this->getOrganisationAdministrators(Roles::PRESIDENT);

        $treasurer = $this->getOrganisationAdministrators(Roles::TREASURER);

        $fin_sec = $this->getOrganisationAdministrators(Roles::FINANCIAL_SECRETARY);


        if (count($data) > 0) {
            $payload = [
                'title'                  => 'Financial Report From ' . $this->convertNumberToQuarterName($request->quarter) . " ". $this->session_service->getCurrentSession()->year,
                'date'                   => date('m/d/Y'),
                'organisation'           => $organisation,
                'incomes'                => $data[0],
                'total_income'           => $data[2] + $data[4],
                'expenditures'           => $data[1],
                'total_expenditures'     => $data[3],
                'bal_brought_forward'    => $data[4],
                'president'              => $president,
                'organisation_telephone' => $this->setOrganisationTelephone($organisation->telephone),
                'treasurer'              => $treasurer,
                'fin_secretary'          => $fin_sec,
                'balance'                => $data[2] - $data[3],
                'year'                   => $this->session_service->getCurrentSession()->year,
            ];
            $pdf = PDF::loadView('Reports.QuarterReport', $payload);
        }
        return $pdf->download('Quarter_Financial_Report.pdf');
    }
}
