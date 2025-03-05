<?php
namespace App\Services;

use App\Constants\Constants;
use App\Constants\PaymentStatus;
use App\Exceptions\BusinessValidationException;
use App\Http\Resources\ExpenditureDetailCollection;
use App\Http\Resources\ExpenditureDetailResource;
use App\Interfaces\ExpenditureDetailInterface;
use App\Models\ExpenditureDetail;
use App\Models\ExpenditureItem;
use App\Traits\HelpTrait;
use Illuminate\Support\Collection;

class ExpenditureDetailService implements ExpenditureDetailInterface {

    use HelpTrait;
    private PaymentItemService $paymentItemService;

    public function __construct(PaymentItemService $paymentItemService)
    {
        $this->paymentItemService = $paymentItemService;
    }

    public function createExpenditureDetail($request, $id)
    {

        $item = ExpenditureItem::findOrFail($id);
        if($item->approve == PaymentStatus::APPROVED){
            throw new BusinessValidationException("Expenditure Item Details can't ne added after approval of the expenditure", 403);
        }
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
            throw new BusinessValidationException("Expenditure Item cannot be updated after been approved or declined", 403);
        }
    }

    public function getExpenditureDetails($expenditure_item_id, $request)
    {
        $expenditure_item   = ExpenditureItem::findOrFail($expenditure_item_id);
        $details            = $this->findExpenditureDetails($expenditure_item->id, $request->status);
        if(isset($request->filter)){
            $details = $details->where('expenditure_details.name', 'LIKE', '%'.$request->filter.'%');
        }
        $expenditure_items  = !is_null($request->per_page) ? $details->paginate($request->per_page) : $details->get();
        $response           = $this->generateResponseForExpenditureDetails($expenditure_items);

        $total_amount_given = collect($details->get())->sum('amount_given');
        $total_amount_spent = collect($details->get())->sum('amount_spent');
        $balance            = ($expenditure_item->amount - $total_amount_given) + ($total_amount_given - $total_amount_spent);

        $total = !is_null($request->per_page) ? $expenditure_items->total() : count($expenditure_items);
        $last_page = !is_null($request->per_page) ? $expenditure_items->lastPage(): 0;
        $per_page = !is_null($request->per_page) ? (int)$expenditure_items->perPage() : 0;
        $current_page = !is_null($request->per_page) ? $expenditure_items->currentPage() : 0;

        return new ExpenditureDetailCollection($response, $expenditure_item->name, $expenditure_item->amount, $total_amount_given,
            $total_amount_spent, $balance, $total, $last_page,
            $per_page, $current_page);
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

    public function filterExpenditureDetail($item, $status, $request)
    {

        $expenditure_item_name = null;
        $expenditure_item_amount = 0;
        $details = ExpenditureDetail::where('expenditure_item_id', $item);

        if(!is_null($status) && $status != Constants::ALL){
            $details = $details->where('approve', $status);
        }
        if(isset($request->filter)){
            $details = $details->where('name', 'LIKE', '%'.$request->filter.'%');
        }
        $details = $details ->orderBy('name', 'ASC')->get();
        $detail_response           = $this->generateResponseForExpenditureDetails($details);
        if(count($details) > 0){
            $expenditure_item_name = $details[0]->expenditureItem->name;
            $expenditure_item_amount = $details[0]->expenditureItem->amount;
        }
        $total_amount_given = collect($details)->sum('amount_given');
        $total_amount_spent = collect($details)->sum('amount_spent');
        $balance            = ($expenditure_item_amount - $total_amount_given) + ($total_amount_given - $total_amount_spent);

        return  [$detail_response, ['expenditure_item_name' => $expenditure_item_name], ['expenditure_item_amount' => $expenditure_item_amount],
            ['total_amount_given' => $total_amount_given], ['total_amount_spent' => $total_amount_spent], ['balance' => $balance]];
    }

    public function setDataForDownload($request) {
        return $this->filterExpenditureDetail($request->expenditure_item_id, $request->status, $request);
    }

    public function findExpenditureDetailsByItemAndQuarter($item, $start_quarter, $end_quarter){
        return ExpenditureDetail::where('expenditure_item_id', $item)
                          ->where('approve', PaymentStatus::APPROVED)
                          ->whereBetween('created_at', [$start_quarter, $end_quarter])
                          ->orderBy('name')
                          ->get();
    }

    public function findExpenditureDetailsByItemAndYear($item, $year){
        return ExpenditureDetail::where('expenditure_item_id', $item)
                          ->where('approve', PaymentStatus::APPROVED)
                          ->where('session_id', $year)
                          ->orderBy('name')
                          ->get();
    }

    public function getExpenditureActivities($payment_activity): Collection
    {
        return ExpenditureDetail::where('payment_item_id', $payment_activity)
                        ->where('approve', PaymentStatus::APPROVED)
                        ->orderBy('name', 'DESC')->get();

    }

    public function computeTotalExpendituresByYearly($request)
    {
        $session_id = $request->session_id;
        $approveExpenses = ExpenditureItem::where('session_id', $session_id)
            ->where('approve', PaymentStatus::APPROVED)
            ->get();

        return collect($approveExpenses)->map(function ($ex){
            return $ex->expenditureDetails()->sum('amount_spent');
        })->sum();
    }

    public function getExpenditureStatistics($request)
    {
        $payment_items = $this->paymentItemService->getPaymentItemsBySessionAndFrequency($request);
        $monthly_expenditures = $this->getMonthlyExpenditures($request->session_id);
        $expenditures_by_items = $this->getExpendituresByPaymentItems($payment_items, $request->session_id);

        return ["monthly_expenses" => $monthly_expenditures, "expenses_by_items" => $expenditures_by_items];
    }

    private function findExpenditureDetail($id)
    {
        return ExpenditureDetail::findOrFail($id);
    }

    private function findExpenditureDetails($id, $status)
    {
        $details = ExpenditureDetail::select('expenditure_details.*', 'expenditure_items.amount as expenditure_item_amount')
                                ->join('expenditure_items', ['expenditure_items.id' => 'expenditure_details.expenditure_item_id'])
                                ->where('expenditure_items.id', $id)
                                ->orderBy('expenditure_details.name', 'ASC');
        if(isset($status) && $status != Constants::ALL){
            $details = $details->Where('expenditure_details.approve', $status);
        }
        return $details;
    }

    private function getMonthlyExpenditures($session_id)
    {
        $expenditures = [];
        for ($month = 1; $month <= 12; $month++) {
            $approveExpenses = ExpenditureItem::where('session_id', $session_id)
                                            ->where('approve', PaymentStatus::APPROVED)
                                            ->whereMonth('date', $month)
                                            ->get();
            $total = collect($approveExpenses)->map(function ($ex){
                return $ex->expenditureDetails()->sum('amount_spent');
            })->sum();
            $expenditures[] = $total;
        }

        return $expenditures;
    }

    private function getExpendituresByPaymentItems($items, $session_id)
    {
        $expenditures = [];
        foreach ($items as $item){
            $approvedPaymentItemExpenditures = ExpenditureItem::where('session_id', $session_id)
                                                ->where('approve', PaymentStatus::APPROVED)
                                                ->where('payment_item_id', $item->id)
                                                ->get();
            $amount = collect($approvedPaymentItemExpenditures)->map(function ($ex){
                return $ex->expenditureDetails()->sum('amount_spent');
            })->sum();

            $expenditures[] = ["name" => $item->name, "amount" => $amount];
        }

        return $expenditures;
    }

    private function setExpenditureItemAmount($data) {
        return count($data->toArray()) == 0 ? 0: $data[0]->expenditure_item_amount;
    }


    public function approveBulkExpenditureItem($request)
    {
        foreach (json_decode(json_encode($request->all())) as $data){
            $detail = ExpenditureDetail::find($data->id);
            if($detail->approve == PaymentStatus::PENDING){
                $detail->approve = $data->type;
                $detail->save();
            }
        }
    }
}
