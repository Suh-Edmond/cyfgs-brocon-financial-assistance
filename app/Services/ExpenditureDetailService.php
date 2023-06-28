<?php
namespace App\Services;

use App\Constants\PaymentStatus;
use App\Exceptions\BusinessValidationException;
use App\Http\Resources\ExpenditureDetailCollection;
use App\Http\Resources\ExpenditureDetailResource;
use App\Interfaces\ExpenditureDetailInterface;
use App\Models\ExpenditureDetail;
use App\Models\ExpenditureItem;
use App\Traits\HelpTrait;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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
        if($detail->approve == PaymentStatus::PENDING){
            $detail->update([
                'name'                  => $request->name,
                'amount_spent'          => $request->amount_spent,
                'amount_given'          => $request->amount_given,
                'comment'               => $request->comment,
                'scan_picture'          => $request->scan_picture
            ]);
        }else {
            throw new BusinessValidationException("Expenditure Item cannot be updated after been approved or declined");
        }
    }

    public function getExpenditureDetails($expenditure_item_id)
    {
        $expenditure_item_name = null;
        $expenditure_item_amount = 0;
        $details            = $this->findExpenditureDetails($expenditure_item_id);
        $response           = $this->generateResponseForExpenditureDetails($details);
        if(count($details) > 0){
            $expenditure_item_name = $details[0]->expenditureItem->name;
            $expenditure_item_amount = $details[0]->expenditureItem->amount;
        }
        $total_amount_given = collect($details)->sum('amount_given');
        $total_amount_spent = collect($details)->sum('amount_spent');
        $balance            = ($expenditure_item_amount - $total_amount_given) + ($total_amount_given - $total_amount_spent);

        return  [$response, ['expenditure_item_name' => $expenditure_item_name], ['expenditure_item_amount' => $expenditure_item_amount],
            ['total_amount_given' => $total_amount_given], ['total_amount_spent' => $total_amount_spent], ['balance' => $balance]];
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

    public function findExpenditureDetailsByItemAndQuarter($item, $start_quarter, $end_quarter){
        return DB::table('expenditure_details')
            ->join('expenditure_items', 'expenditure_items.id', '=', 'expenditure_details.expenditure_item_id')
            ->join('sessions', 'sessions.id', '=', 'expenditure_items.session_id')
            ->where('expenditure_items.approve', PaymentStatus::APPROVED)
            ->where('expenditure_details.expenditure_item_id', $item)
            ->whereBetween('expenditure_items.created_at', [$start_quarter, $end_quarter])
            ->select( 'expenditure_details.name', 'expenditure_details.amount_spent', 'expenditure_details.id')
            ->orderBy('name')
            ->get();
    }

    public function findExpenditureDetailsByItemAndYear($item, $year){
        return DB::table('expenditure_details')
            ->join('expenditure_items', 'expenditure_items.id', '=', 'expenditure_details.expenditure_item_id')
            ->join('sessions', 'sessions.id', '=', 'expenditure_items.session_id')
            ->where('expenditure_items.approve', PaymentStatus::APPROVED)
            ->where('expenditure_details.expenditure_item_id', $item)
            ->where('expenditure_items.session_id', $year)
            ->select( 'expenditure_details.name', 'expenditure_details.amount_spent', 'expenditure_details.id')
            ->orderBy('name')
            ->get();
    }

    public function getExpenditureActivities($payment_activity): Collection
    {
        return DB::table('expenditure_details')
            ->join('expenditure_items', 'expenditure_items.id', '=', 'expenditure_details.expenditure_item_id')
            ->join('payment_items', 'payment_items.id', '=', 'expenditure_items.payment_item_id')
            ->where('payment_items.id', $payment_activity)
            ->where('expenditure_details.approve', PaymentStatus::APPROVED)
            ->select('expenditure_details.name', 'expenditure_details.amount_given', 'expenditure_details.amount_spent')
            ->orderBy('expenditure_details.name', 'DESC')->get();
    }


}
