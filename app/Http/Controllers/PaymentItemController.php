<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentItemRequest;
use App\Http\Resources\PaymentItemCollection;
use App\Http\Resources\PaymentItemResource;
use App\Interfaces\PaymentItemInterface;
use App\Services\PaymentItemService;

class PaymentItemController extends Controller
{

    private $payment_item_service;


    public function __construct(PaymentItemService $payment_item_service)
    {
        $this->payment_item_service = $payment_item_service;
    }



    public function getPaymentItemsByCategory($payment_category_id)
    {
        $items = $this->payment_item_service->getPaymentItemsByCategory($payment_category_id);

        return $this->sendResponse($items, 200);
    }


    public function createPaymentItem(PaymentItemRequest $request, $payment_category_id)
    {
        $this->payment_item_service->createPaymentItem($request, $payment_category_id);

        return $this->sendResponse('success', 'Payment Item created Successfully', 201);
    }


    public function getPaymentItem($payment_category_id, $id)
    {
        $payment_item = $this->payment_item_service->getPaymentItem($id, $payment_category_id);

        return $this->sendResponse(new PaymentItemResource($payment_item), 200);
    }


    public function updatePaymentItem(PaymentItemRequest $request, $payment_category_id, $id)
    {
       $this->payment_item_service->updatePaymentItem($request, $id, $payment_category_id);

       return $this->sendResponse('success', 'Payment Item created Successfully', 204);
    }


    public function deletePaymentItem($payment_category_id, $id)
    {
        $this->payment_item_service->deletePaymentItem($id, $payment_category_id);

        return $this->sendResponse('success', 'Payment Item deleted successfully', 204);
    }
}
