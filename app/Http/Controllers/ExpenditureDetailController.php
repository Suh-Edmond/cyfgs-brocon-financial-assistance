<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenditureDetailRequest;
use App\Services\ExpenditureDetailService;
use Illuminate\Http\Request;

class ExpenditureDetailController extends Controller
{

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
}
