<?php

namespace App\Http\Controllers;

use App\Constants\Roles;
use App\Http\Requests\ExpenditureCategoryRequest;
use App\Http\Requests\UpdateExpenditureCategoryRequest;
use App\Http\Resources\ExpenditureCategoryResource;
use App\Services\ExpenditureCategoryService;
use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ExpenditureCategoryController extends Controller
{

    use ResponseTrait,HelpTrait;

    private ExpenditureCategoryService $expenditure_category_service;

    public function __construct(ExpenditureCategoryService $expenditure_category_service)
    {
        $this->expenditure_category_service = $expenditure_category_service;
    }

    public function getExpenditureCategories($organisation_id, Request $request)
    {
        $expenditure_categories = $this->expenditure_category_service->getExpenditureCategories($organisation_id, $request);
        return $this->sendResponse(($expenditure_categories), 200);
    }

    public function getAllExpenditureCategories(Request $request)
    {
        $categories = $this->expenditure_category_service->getAllExpenditureCategories($request->organisation_id);
        return $this->sendResponse(ExpenditureCategoryResource::collection($categories), "success");
    }

    public function createExpenditureCategory(ExpenditureCategoryRequest $request, $organisation_id)
    {
        $this->expenditure_category_service->createExpenditureCategory($request, $organisation_id);

        return $this->sendResponse('success', 'Expenditure Category created successfully');
    }



    public function getExpenditureCategory($organisation_id, $id)
    {
        $expenditure_category = $this->expenditure_category_service->getExpenditureCategory($id, $organisation_id);

        return $this->sendResponse(new ExpenditureCategoryResource($expenditure_category), 200);
    }



    public function updateExpenditureCategory(UpdateExpenditureCategoryRequest $request, $organisation_id, $id)
    {
        $this->expenditure_category_service->updateExpenditureCategory($request, $id, $organisation_id);

        return $this->sendResponse('success', 'Expenditure Category updated successfully');
    }



    public function deleteExpenditureCategory($organisation_id, $id)
    {
        $this->expenditure_category_service->deleteExpenditureCategory($id, $organisation_id);

        return $this->sendResponse('success', 'Expenditure Category deleted successfully');
    }

    public function filterExpenditureCategories(Request $request)
    {
        $data = $this->expenditure_category_service->filterExpenditureCategory($request);

        return $this->sendResponse($data,'success');
    }

    public function downloadExpenditureCategory(Request $request)
    {
        $organisation      = $request->user()->organisation;
        $expenditure_categories = $this->expenditure_category_service->getExpenditureCategories($request->organisation_id, $request);
        $admins            = $this->getOrganisationAdministrators();
        $president         = $admins[Roles::PRESIDENT];
        $treasurer         = $admins[Roles::TREASURER];
        $fin_sec           = $admins[Roles::FINANCIAL_SECRETARY];

        $data = [
            'title'                    => 'Expenditure Categories',
            'date'                     => date('m/d/Y'),
            'organisation'             => $organisation,
            'expenditure_categories'   => $expenditure_categories,
            'organisation_telephone'   => $this->setOrganisationTelephone($organisation->telephone),
            'president'                => $president,
            'treasurer'                => $treasurer,
            'fin_secretary'            => $fin_sec,
            'organisation_logo'         => env('FILE_DOWNLOAD_URL_PATH').$organisation->logo
        ];

        $pdf = PDF::loadView('ExpenditureCategory.ExpenditureCategories', $data);
        $pdf->output();
        $domPdf = $pdf->getDomPDF();
        $canvas = $domPdf->getCanvas();
        $canvas->page_text(10, $canvas->get_height() - 20, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, [0, 0, 0]);

        return $pdf->download('Expenditure_Categories.pdf');
    }
}
