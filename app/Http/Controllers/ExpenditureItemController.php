<?php

namespace App\Http\Controllers;

 use App\Constants\Roles;
 use App\Http\Requests\ExpenditureItemRequest;
 use App\Services\ExpenditureItemService;
use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ExpenditureItemController extends Controller
{

    use ResponseTrait, HelpTrait;

    private ExpenditureItemService $expenditure_item_service;


    public function __construct(ExpenditureItemService $expenditure_item_service)
    {
        $this->expenditure_item_service = $expenditure_item_service;
    }

    public function createExpenditureItem(ExpenditureItemRequest $request, $id)
    {
        $this->expenditure_item_service->createExpenditureItem($request, $id);

        return $this->sendResponse('success', 'Expenditure Item created successfully');
    }


    public function getExpenditureItems($expenditure_category_id, Request $request)
    {
        $items = $this->expenditure_item_service->getExpenditureByCategory($expenditure_category_id, $request);

        return $this->sendResponse($items, 200);
    }


    public function getExpenditureItem($expenditure_category_id, $id)
    {
        $item = $this->expenditure_item_service->getExpenditureItem($id, $expenditure_category_id);

        return $this->sendResponse($item, 200);
    }

    public function getItem($id)
    {
        $item = $this->expenditure_item_service->getItem($id);

        return $this->sendResponse($item, 200);
    }

    public function getExpenditureByCategory($expenditure_category_id, Request $request)
    {

        $data = $this->expenditure_item_service->getExpenditureByCategory($expenditure_category_id, $request);

        return $this->sendResponse($data, 200);
    }


    public function updateExpenditureItem(ExpenditureItemRequest $request, $expenditure_category_id, $id)
    {
        $this->expenditure_item_service->updateExpenditureItem($request, $id, $expenditure_category_id);

        return $this->sendResponse('success', 'Expenditure Item updated successfully');
    }


    public function deleteExpenditureItem($expenditure_category_id, $id)
    {
        $this->expenditure_item_service->deleteExpenditureItem($id, $expenditure_category_id);

        return $this->sendResponse('success', 'Expenditure Item deleted successfully');
    }

    public function approveExpenditureItem($id, Request $request)
    {
        $this->expenditure_item_service->approveExpenditureItem($id, $request->type);

        return $this->sendResponse('success', 'Expenditure Item approved successfully');
    }

    public function getExpenditureItemByPaymentItem($payment_item_id, Request $request)
    {
        $data = $this->expenditure_item_service->getExpenditureItemsByPaymentItem($payment_item_id, $request);

        return $this->sendResponse($data, 200);
    }

    public function filterExpenditureItems(Request $request)
    {

        $data = $this->expenditure_item_service->filterExpenditureItems($request);

        return $this->sendResponse($data, 200);
    }

    public function prepareDataForDownload(Request $request){
        $data = $this->expenditure_item_service->downloadExpenditureItems($request);
        $data      = json_decode(json_encode($data));
        return $data;
    }

    public function downloadExpenditureItems(Request $request)
    {
        $organisation      = $request->user()->organisation;
        $expenditure_items = $this->prepareDataForDownload($request);
        $admins            = $this->getOrganisationAdministrators();
        $president         = $admins[Roles::PRESIDENT];
        $treasurer         = $admins[Roles::TREASURER];
        $fin_sec           = $admins[Roles::FINANCIAL_SECRETARY];
        $data = [
            'title'               => 'Expenditure Items under '.$request->category_name,
            'date'                => date('d/m/Y'),
            'organisation'        => $organisation,
            'expenditure_items'   => $expenditure_items->data,
            'president'           => $president,
            'organisation_telephone'   => $this->setOrganisationTelephone($organisation->telephone),
            'treasurer'           => $treasurer,
            'fin_secretary'       => $fin_sec,
            'total'               => $this->computeTotalAmountByPaymentCategory($expenditure_items->data),
            'organisation_logo'   => $organisation->logo,
        ];

        $pdf = PDF::loadView('ExpenditureItem.ExpenditureItems', $data)->setPaper('a4', 'portrait');
        $pdf->output();
        $domPdf = $pdf->getDomPDF();
        $canvas = $domPdf->getCanvas();
        $canvas->page_text(10, $canvas->get_height() - 20, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, [0, 0, 0]);


        return $pdf->download('Expenditure_items.pdf');
    }
}
