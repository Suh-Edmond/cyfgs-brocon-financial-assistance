<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenditureCategoryRequest;
use App\Http\Resources\ExpenditureCategoryResource;
use App\Services\ExpenditureCategoryService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use PDF;
class ExpenditureCategoryController extends Controller
{

    use ResponseTrait;

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

    public function downloadExpenditureCategory(Request $request)
    {
        $organisation      = auth()->user()->organisation;
        $expenditure_categories = $this->expenditure_category_service->getExpenditureCategories($request->organisation_id);

        $administrators    = ResponseTrait::getOrganisationAdministrators($organisation->users);
        $president         = $administrators[0];
        $treasurer         = $administrators[1];
        $fin_sec           = $administrators[2];


        $data = [
            'title'                    => 'Expenditure Categories',
            'date'                     => date('m/d/Y'),
            'organisation'             => $organisation,
            'expenditure_categories'   => $expenditure_categories,
            'president'                => $president,
            'treasurer'                => $treasurer,
            'fin_secretary'            => $fin_sec
        ];

        $pdf = PDF::loadView('ExpenditureCategory.ExpenditureCategories', $data);

        return $pdf->download('Payment_items.pdf');
    }
}
