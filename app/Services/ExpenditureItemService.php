<?php
namespace App\Services;

use App\Interfaces\ExpenditureItemInterface;
use App\Models\ExpenditureCategory;
use App\Models\ExpenditureItem;
use Illuminate\Support\Facades\DB;

class ExpenditureItemService implements ExpenditureItemInterface {

    public function createExpenditureItem($request, $expenditure_category_id)
    {
        $expenditure_category = ExpenditureCategory::findOrFail($expenditure_category_id);
        if(! $expenditure_category){
            return response()->json(['message' => 'Expenditure Category not found'], 404);
        }
        ExpenditureItem::create([
            'name'                      => $request->name,
            'amount'                    => $request->amount,
            'venue'                     => $request->venue,
            'comment'                   => $request->comment,
            'date'                      => $request->date,
            'expenditure_category_id'   => $expenditure_category->id,
        ]);
    }

    public function updateExpenditureItem($request, $id, $expenditure_category_id)
    {
        $expenditure_item = $this->findExpenditureItem($id, $expenditure_category_id);
        if(! $expenditure_item){
            return response()->json(['message'=> 'Expenditure Item not found', 'status' => '404'], 404);
        }
        $expenditure_item->update([
            'name'                      => $request->name,
            'amount'                    => $request->amount,
            'venue'                     => $request->venue,
            'comment'                   => $request->comment,
            'date'                      => $request->date
        ]);
    }

    public function getExpenditureItems($expenditure_category_id)
    {
        $items = ExpenditureItem::where('expenditure_category_id', $expenditure_category_id);

        return $items->toArray();
    }

    public function getExpenditureItem($id, $expenditure_category_id)
    {
        $expenditure_item = $this->findExpenditureItem($id, $expenditure_category_id);
        if(! $expenditure_item){
            return response()->json(['message'=> 'Expenditure Item not found', 'status' => '404'], 404);
        }

        return $expenditure_item;
    }

    public function deleteExpenditureItem($id, $expenditure_category_id)
    {
        $expenditure_item = $this->findExpenditureItem($id, $expenditure_category_id);
        if(! $expenditure_item){
            return response()->json(['message'=> 'Expenditure Item not found', 'status' => '404'], 404);
        }

        $expenditure_item->delete();
    }


    private function findExpenditureItem($id, $expenditure_category_id)
    {
        $expenditure_item = DB::table('expenditure_items')
                                        ->join('expenditure_categories', ['expenditure_categories.id' => 'expenditure_items.expenditure_category_id'])
                                        ->where('expenditure_items.id', $id)
                                        ->where('expenditure_items.expenditure_category_id', $expenditure_category_id)
                                        ->first();
        return $expenditure_item;
    }

    public function approveExpenditureItem($id)
    {
        $expenditure_item = ExpenditureItem::findOrFail($id);
        $expenditure_item->update(['approve' => true]);
    }
}
