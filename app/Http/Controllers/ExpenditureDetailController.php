<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenditureDetailRequest;
use App\Services\ExpenditureDetailService;
use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ExpenditureDetailController extends Controller
{

    use ResponseTrait, HelpTrait;

    private ExpenditureDetailService $expenditure_detail_service;


    public function __construct(ExpenditureDetailService $expenditure_detail_service)
    {
        $this->expenditure_detail_service = $expenditure_detail_service;
    }



    public function createExpenditureDetail(ExpenditureDetailRequest $request, $id)
    {
        $this->expenditure_detail_service->createExpenditureDetail($request, $id);

        return $this->sendResponse('success', 'Expenditure detail saved successfully');
    }


    public function updateExpenditureDetail(ExpenditureDetailRequest $request, $id)
    {
        $this->expenditure_detail_service->updateExpenditureDetail($request, $id);

        return $this->sendResponse('success', 'Expenditure detail updated successfully');
    }


    public function getExpenditureDetails($expenditure_item_id, Request $request)
    {
        $details = $this->expenditure_detail_service->getExpenditureDetails($expenditure_item_id, $request);
        return $this->sendResponse($details, 200);
    }


    public function getExpenditureDetail($id)
    {
        $detail  = $this->expenditure_detail_service->getExpenditureDetail($id);

        return $this->sendResponse($detail, 200);
    }


    public function deleteExpenditureDetail($id)
    {
        $this->expenditure_detail_service->deleteExpenditureDetail($id);

        return $this->sendResponse('success', 'Expenditure detail deleted successfully');
    }

    public function approveExpenditureDetail($id, Request $request)
    {
        $this->expenditure_detail_service->approveExpenditureDetail($id, $request->type);

        return $this->sendResponse('success', 'Expenditure detail approved successfully');
    }

    public function filterExpenditureDetails(Request $request)
    {
        $details = $this->expenditure_detail_service->filterExpenditureDetail($request->expenditure_item_id, $request->status);

        return $this->sendResponse($details, 200);
    }

    public function downloadExpenditureDetail(Request $request)
    {
        $organisation      = $request->user()->organisation;

        $expenditure_details = $this->expenditure_detail_service->setDataForDownload($request);

        $admins            = $this->getOrganisationAdministrators();
        $president         = $admins[0];
        $treasurer         = count($admins) == 3 ? $admins[2]: null;
        $fin_sec           = count($admins) == 3 ? $admins[1] : null;

        $total_amount_given = $expenditure_details[3]['total_amount_given'];

        $total_amount_spent = $expenditure_details[4]['total_amount_spent'];

        $balance            = $expenditure_details[5]['balance'];
        $data = [
            'title'                 => 'Detail Expenses for '.$request->expenditure_item_name,
            'date'                  => date('m/d/Y'),
            'organisation'          => $organisation,
            'expenditure_details'   => $expenditure_details[0],
            'president'             => $president,
            'treasurer'             => $treasurer,
            'fin_secretary'         => $fin_sec,
            'organisation_telephone'   => $this->setOrganisationTelephone($organisation->telephone),
            'total_amount_given'    => $total_amount_given,
            'total_amount_spent'    => $total_amount_spent,
            'balance'               => $total_amount_given - $total_amount_spent,
            'net_balance'            => $balance,
            'item_name'             => $expenditure_details[1]['expenditure_item_name'],
            'item_amount'           => $expenditure_details[2]['expenditure_item_amount'],
            'organisation_logo'     => env('FILE_DOWNLOAD_URL_PATH').$organisation->logo
        ];

        $pdf = PDF::loadView('ExpenditureDetail.ExpenditureDetail', $data);
        $pdf->output();
        $domPdf = $pdf->getDomPDF();
        $canvas = $domPdf->getCanvas();
        $canvas->page_text(10, $canvas->get_height() - 20, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, [0, 0, 0]);

        return $pdf->download('Expenditure_Details.pdf');
    }


    public function computeTotalExpendituresByYearly(Request $request)
    {
        $total = $this->expenditure_detail_service->computeTotalExpendituresByYearly($request);

        return $this->sendResponse($total, 200);
    }


    public function getExpenditureStatistics(Request $request)
    {
        $data = $this->expenditure_detail_service->getExpenditureStatistics($request);

        return $this->sendResponse($data, 200);
    }
}
