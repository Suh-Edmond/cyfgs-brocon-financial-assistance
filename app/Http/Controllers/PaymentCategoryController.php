<?php

namespace App\Http\Controllers;

use App\Constants\Roles;
use App\Http\Requests\PaymentCategoryRequest;
use App\Http\Resources\PaymentCategoryResource;
use App\Models\User;
use App\Services\PaymentCategoryService;
use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;


class PaymentCategoryController extends Controller
{

    use ResponseTrait, HelpTrait;


    private PaymentCategoryService $payment_category_service;

    public function __construct(PaymentCategoryService $payment_category_service)
    {
        $this->payment_category_service = $payment_category_service;
    }



    public function createPaymentCategory(PaymentCategoryRequest $request, $organisation_id)
    {
        $this->payment_category_service->createPaymentCategory($request, $organisation_id);

        return $this->sendResponse('success', 'Payment Category created successfully');
    }


    public function getPaymentCategories($organisation_id)
    {
        $payment_categories = $this->payment_category_service->getPaymentCategories($organisation_id);

        return $this->sendResponse($payment_categories, 200);

    }


    public function getPaymentCategory($organisation_id, $id)
    {
        $payment_category = $this->payment_category_service->getPaymentCategory($id, $organisation_id);

        return $this->sendResponse(new PaymentCategoryResource($payment_category), 'success');
    }


    public function updatePaymentCategory(PaymentCategoryRequest $request, $organisation_id, $id)
    {
        $this->payment_category_service->updatePaymentCategory($request, $id,  $organisation_id);

        return $this->sendResponse('success', 'Payment Category updated successfully');
    }


    public function deletePaymentCategory($organisation_id, $id)
    {
       $this->payment_category_service->deletePaymentCategory($id, $organisation_id);

       return $this->sendResponse('success', 'Payment Category deleted successfully');
    }


    public  function filterPaymentCategory(Request $request){
        $categories = $this->payment_category_service->filterPaymentCategory($request);
        return $this->sendResponse($categories, 200);
    }


    public function downloadPaymentCategory(Request $request)
    {
        $auth_user         = auth()->user();
        $organisation      = User::find($auth_user['id'])->organisation;

        $president         = $this->getOrganisationAdministrators(Roles::PRESIDENT);
        $treasurer         = $this->getOrganisationAdministrators(Roles::TREASURER);
        $fin_sec           = $this->getOrganisationAdministrators(Roles::FINANCIAL_SECRETARY);

        $data = [
            'title'             =>'Payment Categories',
            'date'              => date('m/d/Y'),
            'organisation'      => $organisation,
            'organisation_telephone' => $this->setOrganisationTelephone($organisation->telephone),
            'categories'        => $this->payment_category_service->getPaymentCategories($request['organisation_id']),
            'president'         => $president,
            'treasurer'         => $treasurer,
            'fin_secretary'     => $fin_sec
        ];

        $pdf = PDF::loadView('PaymentCategory.PaymentCategories', $data);

        return $pdf->download('Payment_Categories.pdf');
    }
}
