<?php

namespace App\Http\Controllers;

use App\Constants\Roles;
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

    public function getAllPaymentItems(Request $request) {
        $data = $this->payment_item_service->getPaymentItems($request);

        return $this->sendResponse($data, 200);
    }

    public function getPaymentItemsByCategory($payment_category_id, Request $request)
    {
        $items = $this->payment_item_service->getPaymentItemsByCategory($payment_category_id, $request);

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
        $president         = $admins[Roles::PRESIDENT];
        $treasurer         = $admins[Roles::TREASURER];
        $fin_sec           = $admins[Roles::FINANCIAL_SECRETARY];


        $data = [
            'title'               => 'Payment Items under '.$request->category_name. ' Category',
            'date'                => date('d/m/Y'),
            'organisation'        => $organisation,
            'payment_items'       => $items,
            'president'           => $president,
            'treasurer'           => $treasurer,
            'fin_secretary'       => $fin_sec,
            'total'               => $this->computeTotalAmountByPaymentCategory($items),
            'organisation_logo'   => $organisation->logo,
            'organisation_telephone' => $this->setOrganisationTelephone($organisation->telephone),
        ];

        $pdf = PDF::loadView('PaymentItem.PaymentItems', $data)->setPaper('a4', 'portrait');
        $pdf->output();
        $domPdf = $pdf->getDomPDF();
        $canvas = $domPdf->getCanvas();
        $canvas->page_text(10, $canvas->get_height() - 20, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, [0, 0, 0]);
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
