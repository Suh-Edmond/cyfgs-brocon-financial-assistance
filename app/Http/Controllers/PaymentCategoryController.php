<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentCategoryRequest;
use App\Http\Resources\PaymentCategoryResource;
use App\Interfaces\PaymentCategoryInterface;
use App\Services\PaymentCategoryService;

class PaymentCategoryController extends Controller
{

    private $payment_category_service;

    public function __construct(PaymentCategoryService $payment_category_service)
    {
        $this->payment_category_service = $payment_category_service;
    }



    public function createPaymentCategory(PaymentCategoryRequest $request, $organisation_id)
    {
        $this->payment_category_service->createPaymentCategory($request, $organisation_id);

        return response()->json(['message' => 'success', 'status' =>'201'], 201);
    }


    public function getPaymentCategories($organisation_id)
    {
        $payment_categories = $this->payment_category_service->getPaymentCategories($organisation_id);

        return response()->json(['data' => PaymentCategoryResource::collection($payment_categories)], 200);
    }


    public function getPaymentCategory($organisation_id, $id)
    {
        $payment_category = $this->payment_category_service->getPaymentCategory($id, $organisation_id);

        return response()->json(['data' => new PaymentCategoryResource($payment_category)], 200);
    }


    public function updatePaymentCategory(PaymentCategoryRequest $request, $id, $organisation_id)
    {
        $this->payment_category_service->updatePaymentCategory($request, $id,  $organisation_id);

        return response()->json(['message' => 'success', 'status' => '204'], 204);
    }


    public function deletePaymentCategory($id, $organisation_id)
    {
       $this->payment_category_service->deletePaymentCategory($id, $organisation_id);

       return response()->json(['message' => 'success', 'status' => '204'], 204);
    }
}
