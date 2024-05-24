<?php

namespace App\Http\Controllers;

use App\Services\ReportGenerationService;
use App\Services\SessionService;
use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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

    public function downloadReportByActivity($id, Request $request): Response
    {
        $data = $this->report_generation_service->generateReportPerActivity($id);

        $data[0] = json_decode(json_encode($data[0]));

        $data[1] = json_decode(json_encode($data[1]));

        $auth_user         = $request->user();
        $organisation      = $auth_user->organisation;
        $data = [
            'title'               => 'Financial Report for '.$request->payment_activity,
            'date'                => date('d/m/Y'),
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
            'organisation_logo'   => $organisation->logo

        ];

        $pdf = PDF::loadView('Reports.ActivityReport', $data);
        $pdf->output();
        $domPdf = $pdf->getDomPDF();
        $canvas = $domPdf->getCanvas();
        $canvas->page_text(10, $canvas->get_height() - 20, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, [0, 0, 0]);

        return $pdf->download('Activity_Financial_Report.pdf');
    }


    public function generateQuarterlyReport(Request $request)//query param: ?quarter=example_quarter
    {
        $data = $this->report_generation_service->generateQuarterlyReport($request);

        return $this->sendResponse($data, 200);
    }

    public function downloadQuarterlyReport(Request $request): Response //query param: ?quarter=example_quarter
    {
        $data = $this->report_generation_service->downloadQuarterlyReport($request);

        $auth_user = $request->user();

        $organisation = $auth_user->organisation;

        $payload = [
            'title'                  => 'Financial Report From ' . $this->convertNumberToQuarterName($request->quarter) . " ". $this->session_service->getCurrentSession()->year,
            'date'                   => date('d/m/Y'),
            'organisation'           => $organisation,
            'incomes'                => $data[0],
            'total_income'           => $data[2]['total_income'] + $data[4]['balance_brought_forward'],
            'expenditures'           => $data[1],
            'total_expenditure'     => $data[3]['total_expenditure'],
            'bal_brought_forward'    => $data[4]['balance_brought_forward'],
            'president'              => $data[5]['president'],
            'organisation_telephone' => $this->setOrganisationTelephone($organisation->telephone),
            'treasurer'              => $data[6]['treasurer'],
            'fin_secretary'          => $data[7]['fin_sec'],
            'balance'                => ($data[2]['total_income'] + $data[4]['balance_brought_forward']) - $data[3]['total_expenditure'],
            'year'                   => $this->session_service->getCurrentSession()->year,
            'organisation_logo'      => $organisation->logo
        ];
        $pdf = PDF::loadView('Reports.QuarterReport', $payload);
        $pdf->output();
        $domPdf = $pdf->getDomPDF();
        $canvas = $domPdf->getCanvas();
        $canvas->page_text(10, $canvas->get_height() - 20, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, [0, 0, 0]);

        return $pdf->download('Quarter_Financial_Report.pdf');
    }

    public function generateYearlyReport(Request $request)
    {
        $data = $this->report_generation_service->generateYearlyReport($request);

        return $this->sendResponse($data, 200);
    }

    public function downloadYearlyReport(Request $request)
    {
        $data = $this->report_generation_service->downloadYearlyReport($request);

        $auth_user = $request->user();

        $organisation = $auth_user->organisation;

        $payload = [
            'title'                  => 'Financial Report for the Year  ' . $request->year_label,
            'date'                   => date('d/m/Y'),
            'organisation'           => $organisation,
            'incomes'                => $data[0],
            'total_income'           => $data[2]['total_income'] + $data[4]['bal_brought_forward'],
            'expenditures'           => $data[1],
            'total_expenditure'      => $data[3]['total_expenditure'],
            'bal_brought_forward'    => $data[4]['bal_brought_forward'],
            'president'              => $data[5]['president'],
            'organisation_telephone' => $this->setOrganisationTelephone($organisation->telephone),
            'treasurer'              => $data[6]['treasurer'],
            'fin_secretary'          => $data[7]['fin_sec'],
            'balance'                => ($data[2]['total_income'] + $data[4]['bal_brought_forward']) - $data[3]['total_expenditure'],
            'year'                   => $request->year_label,
            'organisation_logo'      => $organisation->logo
        ];
        $pdf = PDF::loadView('Reports.YearlyReport', $payload);
        $pdf->output();
        $domPdf = $pdf->getDomPDF();
        $canvas = $domPdf->getCanvas();
        $canvas->page_text(10, $canvas->get_height() - 20, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, [0, 0, 0]);

        return $pdf->download('Yearly_Financial_Report.pdf');
    }
}
