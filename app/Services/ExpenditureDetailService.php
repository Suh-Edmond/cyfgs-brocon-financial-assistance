<?php
namespace App\Services;

use App\Http\Resources\ExpenditureDetailCollection;
use App\Http\Resources\ExpenditureDetailResource;
use App\Interfaces\ExpenditureDetailInterface;
use App\Models\ExpenditureDetail;
use App\Models\ExpenditureItem;
use App\Traits\HelpTrait;

class ExpenditureDetailService implements ExpenditureDetailInterface {

    use HelpTrait;

    public function createExpenditureDetail($request, $id)
    {

        $item = ExpenditureItem::findOrFail($id);

        ExpenditureDetail::create([
            'name'                  => $request->name,
            'amount_spent'          => $request->amount_spent,
            'amount_given'          => $request->amount_given,
            'comment'               => $request->comment,
            'expenditure_item_id'   => $item->id,
            'scan_picture'          => $request->scan_picture,
            'updated_by'            => $request->user()->name
        ]);

    }

    public function updateExpenditureDetail($request, $id)
    {

        $detail = $this->findExpenditureDetail($id);

        $detail->update([
            'name'                  => $request->name,
            'amount_spent'          => $request->amount_spent,
            'amount_given'          => $request->amount_given,
            'comment'               => $request->comment,
            'scan_picture'          => $request->scan_picture
        ]);
    }

    public function getExpenditureDetails($expenditure_item_id)
    {
        $details            = $this->findExpenditureDetails($expenditure_item_id);

        $response           = $this->generateResponseForExpenditureDetails($details);
        $item_amount        = $this->setExpenditureItemAmount($details);
        $total_amount_given = $this->calculateTotalAmountGiven($details);
        $total_amount_spent = $this->calculateTotalAmountSpent($details);
        $balance            = $this->calculateExpenditureBalanceByExpenditureItem($details, $item_amount);
        return new ExpenditureDetailCollection($response, $item_amount, $total_amount_given, $total_amount_spent, $balance);
    }

    public function getExpenditureDetail($id)
    {
        $detail = $this->findExpenditureDetail($id);
        $balance = $this->calculateExpenditureBalance($detail);

        return new ExpenditureDetailResource($detail, $balance);
    }

    public function deleteExpenditureDetail($id)
    {
        $detail = $this->findExpenditureDetail($id);

        $detail->delete();
    }

    public function approveExpenditureDetail($id, $type)
    {
        $detail = $this->findExpenditureDetail($id);
        $detail->approve = $type;
        $detail->save();
    }

    public function filterExpenditureDetail($id, $status)
    {
        $details = ExpenditureDetail::select('expenditure_details.*', 'expenditure_items.amount as expenditure_item_amount')
                                    ->join('expenditure_items', ['expenditure_items.id' => 'expenditure_details.expenditure_item_id'])
                                    ->where('expenditure_items.id', $id);
        if($status != "ALL"){
            $details = $details->Where('expenditure_details.approve', $status);
        }
        $details = $details ->orderBy('expenditure_details.name', 'ASC')->get();

        $response           = $this->generateResponseForExpenditureDetails($details);
        $item_amount        = $this->setExpenditureItemAmount($details);
        $total_amount_given = $this->calculateTotalAmountGiven($details);
        $total_amount_spent = $this->calculateTotalAmountSpent($details);
        $balance            = $this->calculateExpenditureBalanceByExpenditureItem($details, $item_amount);
        return new ExpenditureDetailCollection($response, $item_amount, $total_amount_given, $total_amount_spent, $balance);
    }

    private function findExpenditureDetail($id)
    {
        return ExpenditureDetail::findOrFail($id);
    }

    private function findExpenditureDetails($id)
    {
        return ExpenditureDetail::select('expenditure_details.*', 'expenditure_items.amount as expenditure_item_amount')
                                ->join('expenditure_items', ['expenditure_items.id' => 'expenditure_details.expenditure_item_id'])
                                ->where('expenditure_items.id', $id)
                                ->orderBy('expenditure_details.name', 'ASC')
                                ->get();
    }

    private function setExpenditureItemAmount($data) {
        return count($data->toArray()) == 0 ? 0: $data[0]->expenditure_item_amount;
    }

    public function setDataForDownload($request) {
        if(is_null($request->status)) {
            return $this->getExpenditureDetails($request->expenditure_item_id);
        }
        else {
            return $this->filterExpenditureDetail($request->expenditure_item_id, $request->status);
        }
    }


}
