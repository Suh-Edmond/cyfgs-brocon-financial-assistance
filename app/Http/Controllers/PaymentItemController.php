<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentItemRequest;
use App\Http\Resources\PaymentItemResource;
use App\Models\PaymentItem;
use App\Services\PaymentItemService;
use Illuminate\Http\Request;

class PaymentItemController extends Controller
{

    private $payment_item_service;


    public function __construct(PaymentItemService $payment_item_service)
    {
        $this->payment_item_service = $payment_item_service;
    }



    public function getPaymentItemsByCategory($id)
    {
        $items = $this->payment_item_service->getPaymentItemsByCategory($id);

        return response()->json(['data' => PaymentItemResource::collection($items)], 200);
    }


    public function store(PaymentItemRequest $request, $payment_category_id)
    {
        $this->payment_item_service->createPaymentItem($request, $payment_category_id);

        return response()->json(['message' => 'success', 'status' => "201"], 201);
    }


    public function getPaymentItem(PaymentItem $payment_item_id, $payment_category_id)
    {
        $payment_item = $this->payment_item_service->getPaymentItem($payment_item_id, $payment_category_id);

        return response()->json(['data'=>new PaymentItemResource($payment_item)], 200);
    }


    public function updatePaymentItem(PaymentItemRequest $request, $payment_item_id, $payment_category_id)
    {
       $this->payment_item_service->updatePaymentItem($request, $payment_item_id, $payment_category_id);

       return response()->json(['message' => 'success', 'status' => '204'], 204);
    }


    public function deletePaymentItem($payment_item_id, $payment_category_id)
    {
        $this->payment_item_service->deletePaymentItem($payment_item_id, $payment_category_id);

        return response()->json(['message' => 'success', 'status' => '204'], 204);
    }
}
