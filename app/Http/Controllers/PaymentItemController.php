<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentItemRequest;
use App\Http\Requests\UpdatePaymentItemReferenceRequest;
use App\Http\Resources\PaymentItemResource;
use App\Services\PaymentItemService;
use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentItemController extends Controller
{

    use ResponseTrait, HelpTrait;
    private PaymentItemService $payment_item_service;


    public function __construct(PaymentItemService $payment_item_service)
    {
        $this->payment_item_service = $payment_item_service;
    }

    public function getAllPaymentItems() {
        $data = $this->payment_item_service->getPaymentItems();

        return $this->sendResponse(PaymentItemResource::collection($data), 200);
    }

    public function getPaymentItemsByCategory($payment_category_id)
    {
        $items = $this->payment_item_service->getPaymentItemsByCategory($payment_category_id);

        return $this->sendResponse($items, 200);
    }


    public function createPaymentItem(PaymentItemRequest $request, $payment_category_id)
    {
        $this->payment_item_service->createPaymentItem($request, $payment_category_id);

        return $this->sendResponse('success', 'Payment Item created Successfully');
    }


    public function getPaymentItem($payment_category_id, $id)
    {
        $payment_item = $this->payment_item_service->getPaymentItem($id, $payment_category_id);

        return $this->sendResponse(new PaymentItemResource($payment_item), 200);
    }


    public function updatePaymentItem(PaymentItemRequest $request, $payment_category_id, $id)
    {
       $this->payment_item_service->updatePaymentItem($request, $id, $payment_category_id);

       return $this->sendResponse('success', 'Payment Item created Successfully');
    }


    public function deletePaymentItem($payment_category_id, $id)
    {
        $this->payment_item_service->deletePaymentItem($id, $payment_category_id);

        return $this->sendResponse('success', 'Payment Item deleted successfully');
    }

    public function filterPaymentItems(Request $request)
    {
        $data = $this->payment_item_service->filterPaymentItems($request);

        return $this->sendResponse($data, 200);
    }

    public function getPaymentItemByType(Request $request)
    {
        $data = $this->payment_item_service->getPaymentItemByType($request->type);
        return $this->sendResponse(PaymentItemResource::collection($data), 200);
    }

    public function downloadPaymentItem(Request $request)
    {
        $organisation      = $request->user()->organisation;
        $items             = $this->payment_item_service->filterPaymentItems($request);
        $admins            = $this->getOrganisationAdministrators();
        $president         = $admins[0];
        $treasurer         = $admins[2];
        $fin_sec           = $admins[1];


        $data = [
            'title'               => 'Payment Items for '.$items[0]->paymentCategory->name,
            'date'                => date('m/d/Y'),
            'organisation'        => $organisation,
            'payment_items'       => $items,
            'president'           => $president,
            'treasurer'           => $treasurer,
            'fin_secretary'       => $fin_sec,
            'total'               => $this->computeTotalAmountByPaymentCategory($items)
        ];

        $pdf = PDF::loadView('PaymentItem.PaymentItems', $data);

        return $pdf->download('Payment_items.pdf');
    }

    public function updatePaymentItemReference(UpdatePaymentItemReferenceRequest $request)
    {
        $this->payment_item_service->updatePaymentItemReference($request);

        return $this->sendResponse('success', 'reference updated successfully');
    }

    public function getPaymentItemReferences($id)
    {
        $data = $this->payment_item_service->getPaymentItemReferences($id);

        return $this->sendResponse($data, 'success');
    }
}
