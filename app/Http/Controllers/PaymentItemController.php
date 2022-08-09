<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentItemRequest;
use App\Http\Resources\PaymentItemResource;
use App\Interfaces\PaymentItemInterface;

class PaymentItemController extends Controller
{

    private $payment_item_service;


    public function __construct(PaymentItemInterface $payment_item_service)
    {
        $this->payment_item_service = $payment_item_service;
    }



    public function getPaymentItemsByCategory($payment_category_id)
    {
        $items = $this->payment_item_service->getPaymentItemsByCategory($payment_category_id);

        return response()->json(['data' => PaymentItemResource::collection($items)], 200);
    }


    public function createPaymentItem(PaymentItemRequest $request, $payment_category_id)
    {
        $this->payment_item_service->createPaymentItem($request, $payment_category_id);

        return response()->json(['message' => 'success', 'status' => "201"], 201);
    }


    public function getPaymentItem($payment_category_id, $id)
    {
        $payment_item = $this->payment_item_service->getPaymentItem($id, $payment_category_id);

        return response()->json(['data'=>new PaymentItemResource($payment_item)], 200);
    }


    public function updatePaymentItem(PaymentItemRequest $request, $payment_category_id, $id)
    {
       $this->payment_item_service->updatePaymentItem($request, $id, $payment_category_id);

       return response()->json(['message' => 'success', 'status' => '204'], 204);
    }


    public function deletePaymentItem($payment_category_id, $id)
    {
        $this->payment_item_service->deletePaymentItem($id, $payment_category_id);

        return response()->json(['message' => 'success', 'status' => '204'], 204);
    }
}
