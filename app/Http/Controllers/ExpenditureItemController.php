<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenditureItemRequest;
use App\Http\Resources\ExpenditureItemResource;
use App\Services\ExpenditureItemService;
use Illuminate\Http\Request;

class ExpenditureItemController extends Controller
{

    private $expenditure_item_service;


    public function __construct(ExpenditureItemService $expenditure_item_service)
    {
        $this->expenditure_item_service = $expenditure_item_service;
    }

    public function createExpenditureItem(ExpenditureItemRequest $request, $id)
    {
        $this->expenditure_item_service->createExpenditureItem($request, $id);

        return $this->sendResponse('success', 'Expenditure Item created successfully', 201);
    }


    public function getExpenditureItems($expenditure_category_id, Request $request)
    {
        $items = $this->expenditure_item_service->getExpenditureItems($expenditure_category_id, $request->status);

        return $this->sendResponse($items, 200);
    }


    public function getExpenditureItem($expenditure_category_id, $id)
    {
        $item = $this->expenditure_item_service->getExpenditureItem($id, $expenditure_category_id);

        return $this->sendResponse($item, 200);
    }



    public function updateExpenditureItem(ExpenditureItemRequest $request, $expenditure_category_id, $id)
    {
        $this->expenditure_item_service->updateExpenditureItem($request, $id, $expenditure_category_id);

        return $this->sendResponse('success', 'Expenditure Item updated successfully', 204);
    }


    public function deleteExpenditureItem($expenditure_category_id, $id)
    {
        $this->expenditure_item_service->deleteExpenditureItem($id, $expenditure_category_id);

        return $this->sendResponse('success', 'Expenditure Item deleted successfully', 204);
    }

    public function approveExpenditureItem($id)
    {
        $this->expenditure_item_service->approveExpenditureItem($id);

        return $this->sendResponse('success', 'Expenditure Item approved successfully', 204);
    }
}
