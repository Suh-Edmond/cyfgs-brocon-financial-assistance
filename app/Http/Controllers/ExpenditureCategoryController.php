<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenditureCategoryRequest;
use App\Http\Resources\ExpenditureCategoryCollection;
use App\Http\Resources\ExpenditureCategoryResource;
use App\Http\Resources\ExpenditureCollection;
use App\Services\ExpenditureCategoryService;

class ExpenditureCategoryController extends Controller
{

    private $expenditure_category_service;

    public function __construct(ExpenditureCategoryService $expenditure_category_service)
    {
        $this->expenditure_category_service = $expenditure_category_service;
    }



    public function getExpenditureCategories($organisation_id)
    {
        $expenditure_categories = $this->expenditure_category_service->getExpenditureCategories($organisation_id);

        return $this->sendResponse(ExpenditureCategoryResource::collection($expenditure_categories), 200);
    }


    public function createExpenditureCategory(ExpenditureCategoryRequest $request, $organisation_id)
    {
        $this->expenditure_category_service->createExpenditureCategory($request, $organisation_id);

        return $this->sendResponse('success', 'Expenditure Category created successfully', 201);
    }



    public function getExpenditureCategory($organisation_id, $id)
    {
        $expenditure_category = $this->expenditure_category_service->getExpenditureCategory($id, $organisation_id);

        return $this->sendResponse(new ExpenditureCategoryResource($expenditure_category), 200);
    }



    public function updateExpenditureCategory(ExpenditureCategoryRequest $request, $organisation_id, $id)
    {
        $this->expenditure_category_service->updateExpenditureCategory($request, $id, $organisation_id);

        return $this->sendResponse('success', 'Expenditure Category updated successfully', 202);
    }



    public function deleteExpenditureCategory($organisation_id, $id)
    {
        $this->expenditure_category_service->deleteExpenditureCategory($id, $organisation_id);

        return $this->sendResponse('success', 'Expenditure Category deleted successfully', 204);
    }
}
