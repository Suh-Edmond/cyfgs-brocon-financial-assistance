<?php
namespace App\Services;

use App\Http\Resources\ExpenditureItemResource;
use App\Interfaces\ExpenditureItemInterface;
use App\Models\ExpenditureCategory;
use App\Models\ExpenditureItem;
use App\Traits\HelpTrait;

class ExpenditureItemService implements ExpenditureItemInterface {

    use HelpTrait;


    public function createExpenditureItem($request, $expenditure_category_id)
    {
        $expenditure_category = ExpenditureCategory::findOrFail($expenditure_category_id);

        ExpenditureItem::create([
            'name'                      => $request->name,
            'amount'                    => $request->amount,
            'venue'                     => $request->venue,
            'comment'                   => $request->comment,
            'date'                      => $request->date,
            'expenditure_category_id'   => $expenditure_category->id,
            'scan_picture'              => $request->scan_picture,
            'updated_by'                => $request->user()->name
        ]);
    }

    public function updateExpenditureItem($request, $id, $expenditure_category_id)
    {
        $expenditure_item = $this->findExpenditureItem($id, $expenditure_category_id);

        $expenditure_item->update([
            'name'                      => $request->name,
            'amount'                    => $request->amount,
            'venue'                     => $request->venue,
            'comment'                   => $request->comment,
            'date'                      => $request->date,
            'scan_picture'              => $request->scan_picture
        ]);
    }

    public function getExpenditureItems($expenditure_category_id, $status)
    {
        $items = $this->findExpenditureItems($expenditure_category_id, $status);

        return $this->generateExpenditureItemResponse($items);
    }

    public function getExpenditureItem($id, $expenditure_category_id)
    {
        $expenditure_item = $this->findExpenditureItem($id, $expenditure_category_id);

        return new ExpenditureItemResource($expenditure_item,
                                        $this->calculateTotalAmountGiven($expenditure_item->expendiureDetails),
                                        $this->calculateTotalAmountSpent($expenditure_item->expendiureDetails),
                                        $this->calculateExpenditureBalanceByExpenditureItem($expenditure_item->expendiureDetails, $expenditure_item->amount));
    }

    public function deleteExpenditureItem($id, $expenditure_category_id)
    {
        $expenditure_item = $this->findExpenditureItem($id, $expenditure_category_id);

        $expenditure_item->delete();
    }

    public function approveExpenditureItem($id, $type)
    {
        $expenditure_item = ExpenditureItem::findOrFail($id);
        $expenditure_item->approve = $type;

        $expenditure_item->save();
    }

    public function getExpenditureItemByStatus($status)
    {

    }

    private function findExpenditureItem($id, $expenditure_category_id)
    {
        return ExpenditureItem::select('expenditure_items.*')
                                        ->join('expenditure_categories', ['expenditure_categories.id' => 'expenditure_items.expenditure_category_id'])
                                        ->where('expenditure_items.id', $id)
                                        ->where('expenditure_items.expenditure_category_id', $expenditure_category_id)
                                        ->firstOrFail();
    }

    private function findExpenditureItems($expenditure_category_id, $status)
    {
        $data = ExpenditureItem::select('expenditure_items.*')
                            ->join('expenditure_categories', ['expenditure_categories.id' => 'expenditure_items.expenditure_category_id'])
                            ->where('expenditure_items.expenditure_category_id', $expenditure_category_id);
        if($status != "ALL"){
            $data = $data->where('expenditure_items.approve', $status);
        }
        $data = $data->orderBy('expenditure_items.name', 'ASC')->get();

        return $data;
    }

    private function generateExpenditureItemResponse($items)
    {
        $response = array();
        foreach($items as $item)
        {
            array_push($response, new ExpenditureItemResource($item, $this->calculateTotalAmountGiven($item->expendiureDetails),
                                                                    $this->calculateTotalAmountSpent($item->expendiureDetails),
                                                                    $this->calculateExpenditureBalanceByExpenditureItem($item->expendiureDetails,
                                                                    $item->amount)));
        }

        return $response;
    }


    public function calculateTotal($items)
    {
        $total = 0;
        foreach($items as $item)
        {
            $total += $item->amount;
        }

        return $total;
    }

    public function getItem($id)
    {
        return ExpenditureItem::findOrFail($id);
    }

    public function getExpenditureByCategory($expenditure_category_id)
    {
        $items = ExpenditureItem::select('expenditure_items.*')
            ->join('expenditure_categories', ['expenditure_categories.id' => 'expenditure_items.expenditure_category_id'])
            ->where('expenditure_items.expenditure_category_id', $expenditure_category_id)
            ->orderBy('expenditure_items.name', 'ASC')
            ->get();

        return $this->generateExpenditureItemResponse($items);
    }

}
