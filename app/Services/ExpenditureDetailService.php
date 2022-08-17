<?php
namespace App\Services;

use App\Interfaces\ExpenditureDetailInterface;
use App\Models\ExpenditureDetail;
use App\Models\ExpenditureItem;

class ExpenditureDetailService implements ExpenditureDetailInterface {

    public function createExpenditureDetail($request, $id)
    {
        $item = ExpenditureItem::findOrFail($id);
        ExpenditureDetail::create([
            'name'                  => $request->name,
            'amount_spend'          => $request->amount_spend,
            'amount_given'          => $request->amount_given,
            'comment'               => $request->comment,
            'expenditure_item_id'   => $item->id
        ]);

    }

    public function updateExpenditureDetail($request, $id)
    {
        $detail = $this->findExpenditureDetail($id);

        $detail->update([
            'name'                  => $request->name,
            'amount_spend'          => $request->amount_spend,
            'amount_given'          => $request->amount_given,
            'comment'               => $request->comment,
        ]);
    }

    public function getExpenditureDetails($expenditure_item_id)
    {
        $details = ExpenditureDetail::where('expenditure_item_id', $expenditure_item_id)->sum('amount_spent');

        $details;
    }

    public function getExpenditureDetail($id)
    {
        $detail = $this->findExpenditureDetail($id);

        return $detail;
    }

    public function deleteExpenditureDetail($id)
    {
        $detail = $this->findExpenditureDetail($id);

        $detail->delete();
    }

    public function approveExpenditureDetail($id)
    {
        $detail = $this->findExpenditureDetail($id);

        $detail->update(['approve' => true]);
    }

    public function calculateExpenditureBalance($id, $expenditure_item_id)
    {

    }

    public function filterExpenditureDetail($item, $status)
    {
        $details = ExpenditureDetail::select('expenditure_details.*')
                                    ->join('expenditure_items', ['expenditure_items.id' => 'expenditure_details.expenditure_item_id'])
                                    ->where('expenditure_items.id', $item)
                                    ->orWhere('expenditure_details.status', $status)
                                    ->get();
        return $details;
    }

    private function findExpenditureDetail($id)
    {
        return ExpenditureDetail::findOrFail($id);
    }
}
