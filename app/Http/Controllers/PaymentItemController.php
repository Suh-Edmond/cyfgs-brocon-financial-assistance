<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentItemRequest;
use App\Http\Resources\PaymentItemResource;
use App\Models\PaymentCategory;
use App\Services\PaymentItemService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use PDF;

class PaymentItemController extends Controller
{

    use ResponseTrait;
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

    public function downloadPaymentItem(Request $request)
    {
        $organisation      = auth()->user()->organisation;
        $items             = $this->payment_item_service->getPaymentItemsByCategory($request->payment_category_id);
        $administrators    = ResponseTrait::getOrganisationAdministrators($organisation->users);
        $president         = $administrators[0];
        $treasurer         = $administrators[1];
        $fin_sec           = $administrators[2];


        $data = [
            'title'               => 'Payment Items for '.$items[0]->paymentCategory->name,
            'date'                => date('m/d/Y'),
            'organisation'        => $organisation,
            'payment_items'       => $items,
            'president'           => $president,
            'treasurer'           => $treasurer,
            'fin_secretary'       => $fin_sec,
            'total'               => ResponseTrait::computeTotalAmountByPaymentCategory($items)
        ];

        $pdf = PDF::loadView('PaymentItem.PaymentItems', $data);

        return $pdf->download('Payment_items.pdf');
    }
}
