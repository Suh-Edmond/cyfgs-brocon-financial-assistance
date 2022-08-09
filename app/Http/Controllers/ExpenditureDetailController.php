<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenditureDetailRequest;
use App\Http\Resources\ExpenditureDetailResource;
use App\Interfaces\ExpenditureDetailInterface;


class ExpenditureDetailController extends Controller
{

    private $expenditure_detail_service;


    public function __construct(ExpenditureDetailInterface $expenditure_detail_service)
    {
        $this->expenditure_detail_service = $expenditure_detail_service;
    }



    public function createExpenditureDetail(ExpenditureDetailRequest $request, $id)
    {
        $this->expenditure_detail_service->createExpenditureDetail($request, $id);

        return response()->json(['message' => "success", "status" => "ok"], 201);
    }


    public function updateExpenditureDetail(ExpenditureDetailRequest $request, $id)
    {
        $this->expenditure_detail_service->updateExpenditureDetail($request, $id);

        return response()->json(['message' => "success", "status" => "ok"], 202);
    }


    public function getExpenditureDetails($expenditure_item_id)
    {
        $details = $this->expenditure_detail_service->getExpenditureDetails($expenditure_item_id);

        return response()->json(['data' => ExpenditureDetailResource::collection($details), 'status' => 'ok'], 200);
    }


    public function getExpenditureDetail($id)
    {
        $detail  = $this->expenditure_detail_service->getExpenditureDetail($id);

        return response()->json(['data' => new ExpenditureDetailResource($detail), 'status' => 'ok'], 200);
    }


    public function deleteExpenditureDetail($id)
    {
        $this->expenditure_detail_service->deleteExpenditureDetail($id);

        return response()->json(['message' => "success", "status" => "ok"], 204);
    }

    public function approveExpenditureDetail($id)
    {
        $this->expenditure_detail_service->approveExpenditureDetail($id);

        return response()->json(['message' => 'success', 'status' => 'ok'], 204);
    }

    public function filterExpenditureDetails($item, $status)
    {
        $details = $this->expenditure_detail_service->filterExpenditureDetail($item, $status);

        return response()->json(['data' => ExpenditureDetailResource::collection($details), 'status' => 'ok'], 200);
    }
}
