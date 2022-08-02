<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenditureItemRequest;
use App\Http\Resources\ExpenditureItemResource;
use App\Services\ExpenditureItemService;

class ExpenditureItemController extends Controller
{

    private $expenditure_item_service;


    public function __construct(ExpenditureItemService $expenditure_item_service)
    {
        $this->expenditure_item_service = $expenditure_item_service;
    }




    public function getExpenditureItems($expenditure_category_id)
    {
        $items = $this->expenditure_item_service->getExpenditureItems($expenditure_category_id);

        return response()->json(['data', ExpenditureItemResource::collection($items)], 200);
    }


    public function createExpenditureItem(ExpenditureItemRequest $request, $expenditure_category_id)
    {
        $this->expenditure_item_service->createExpenditureItem($request, $expenditure_category_id);

        return response()->json(['message' => 'success', 'status' => '201'], 201);
    }



    public function getExpenditureItem($expenditure_category_id, $id)
    {
        $item = $this->expenditure_item_service->getExpenditureItem($id, $expenditure_category_id);

        return response()->json(['data', new ExpenditureItemResource($item)], 200);
    }



    public function updateExpenditureItem(ExpenditureItemRequest $request, $expenditure_category_id, $id)
    {
        $this->expenditure_item_service->updateExpenditureItem($request, $id, $expenditure_category_id);

        return response()->json(['message' => 'success', 'status' => '204'], 204);
    }


    public function deleteExpenditureItem($expenditure_category_id, $id)
    {
        $this->expenditure_item_service->deleteExpenditureItem($id, $expenditure_category_id);

        return response()->json(['message' => 'success', 'status' => '204'], 204);
    }
}
