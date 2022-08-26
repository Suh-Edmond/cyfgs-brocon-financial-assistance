<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenditureDetailRequest;
use App\Models\ExpenditureItem;
use App\Services\ExpenditureDetailService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use PDF;

class ExpenditureDetailController extends Controller
{

    use ResponseTrait;

    private $expenditure_detail_service;


    public function __construct(ExpenditureDetailService $expenditure_detail_service)
    {
        $this->expenditure_detail_service = $expenditure_detail_service;
    }



    public function createExpenditureDetail(ExpenditureDetailRequest $request, $id)
    {
        $this->expenditure_detail_service->createExpenditureDetail($request, $id);

        return $this->sendResponse('success', 'Expenditure detail saved successfully', 201);
    }


    public function updateExpenditureDetail(ExpenditureDetailRequest $request, $id)
    {
        $this->expenditure_detail_service->updateExpenditureDetail($request, $id);

        return $this->sendResponse('success', 'Expenditure detail updated successfully', 202);
    }


    public function getExpenditureDetails($expenditure_item_id)
    {
        $details = $this->expenditure_detail_service->getExpenditureDetails($expenditure_item_id);

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

        return $this->sendResponse('success', 'Expenditure detail deleted successfully', 204);
    }

    public function approveExpenditureDetail($id)
    {
        $this->expenditure_detail_service->approveExpenditureDetail($id);

        return $this->sendResponse('success', 'Expenditure detail approved successfully', 204);
    }

    public function filterExpenditureDetails(Request $request)
    {
        $details = $this->expenditure_detail_service->filterExpenditureDetail($request->expenditure_item_id, $request->status);

        return $this->sendResponse($details, 200);
    }

    public function downloadExpenditureDetail(Request $request)
    {
        $organisation        = auth()->user()->organisation;
        $expenditure_details = $this->expenditure_detail_service->getExpenditureDetails($request->expenditure_item_id);
        $administrators      = ResponseTrait::getOrganisationAdministrators($organisation->users);
        $president           = $administrators[0];
        $treasurer           = $administrators[1];
        $fin_sec             = $administrators[2];
        $total_amount_given = $this->calculateTotalAmountGiven($expenditure_details);
        $total_amount_spent = $this->calculateTotalAmountSpent($expenditure_details);
        $balance            = $this->calculateExpenditureBalanceByExpenditureItem($expenditure_details, $expenditure_details[0]->expenditureItem->amount);

        $data = [
            'title'                 => 'Expenditure Details for '.$expenditure_details[0]->expenditureItem->name,
            'date'                  => date('m/d/Y'),
            'organisation'          => $organisation,
            'expenditure_details'   => $expenditure_details,
            'president'             => $president,
            'treasurer'             => $treasurer,
            'fin_secretary'         => $fin_sec,
            'total_amount_given'    => $total_amount_given,
            'total_amount_spent'    => $total_amount_spent,
            'balance'               => $balance,
            'item'                  => $expenditure_details[0]->expenditureItem
        ];

        $pdf = PDF::loadView('ExpenditureDetail.ExpenditureDetail', $data);

        return $pdf->download('Expenditure_Details.pdf');
    }
}
