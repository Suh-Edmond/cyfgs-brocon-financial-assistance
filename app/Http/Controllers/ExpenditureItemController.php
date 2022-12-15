<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenditureItemRequest;
use App\Models\User;
use App\Services\ExpenditureItemService;
use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use PDF;
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
        $items = $this->expenditure_item_service->getExpenditureItems($expenditure_category_id, $request->status);

        return $this->sendResponse($items, 200);
    }


    public function getExpenditureItem($expenditure_category_id, $id)
    {
        $item = $this->expenditure_item_service->getExpenditureItem($id, $expenditure_category_id);

        return $this->sendResponse($item, 200);
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

    public function approveExpenditureItem($id)
    {
        $this->expenditure_item_service->approveExpenditureItem($id);

        return $this->sendResponse('success', 'Expenditure Item approved successfully');
    }

    public function downloadExpenditureItems(Request $request)
    {
        $auth_user         = auth()->user();
        $organisation      = User::find($auth_user['id'])->organisation;
        $expenditure_items = $this->expenditure_item_service->getExpenditureItems($request->expenditure_category_id, $request->status);
        $administrators    = $this->getOrganisationAdministrators($organisation->users);
        $president         = $administrators[0];
        $treasurer         = $administrators[1];
        $fin_sec           = $administrators[2];


        $data = [
            'title'               => 'Expenditure Items',
            'date'                => date('m/d/Y'),
            'organisation'        => $organisation,
            'expenditure_items'   => $expenditure_items,
            'president'           => $president,
            'treasurer'           => $treasurer,
            'fin_secretary'       => $fin_sec,
            'total'               => $this->expenditure_item_service->calculateTotal($expenditure_items)
        ];

        $pdf = PDF::loadView('ExpenditureItem.ExpenditureItems', $data);

        return $pdf->download('Expenditure_items.pdf');
    }
}
