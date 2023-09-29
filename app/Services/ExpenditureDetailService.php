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
    private PaymentItemService $paymentItemService;

    public function __construct(PaymentItemService $paymentItemService)
    {
        $this->paymentItemService = $paymentItemService;
    }

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

    public function getExpenditureDetails($expenditure_item_id, $request)
    {
        $expenditure_item   = ExpenditureItem::findOrFail($expenditure_item_id);
        $details            = $this->findExpenditureDetails($expenditure_item->id, $request->status);
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

    public function filterExpenditureDetail($id, $status)
    {
        $expenditure_item_name = null;
        $expenditure_item_amount = 0;
        $details = ExpenditureDetail::select('expenditure_details.*', 'expenditure_items.amount as expenditure_item_amount')
                                    ->join('expenditure_items', ['expenditure_items.id' => 'expenditure_details.expenditure_item_id'])
                                    ->where('expenditure_items.id', $id);
        if(!is_null($status) && $status != "ALL"){
            $details = $details->Where('expenditure_details.approve', $status);
        }
        $details = $details ->orderBy('expenditure_details.name', 'ASC')->get();
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
        return $this->filterExpenditureDetail($request->expenditure_item_id, $request->status);
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

    public function computeTotalExpendituresByYearly($request)
    {
        $expenses =  DB::table('expenditure_details')
            ->join('expenditure_items', 'expenditure_items.id', '=', 'expenditure_details.expenditure_item_id')
            ->join('sessions', 'sessions.id', '=', 'expenditure_items.session_id')
            ->where('sessions.id', $request->session_id)
            ->where('expenditure_items.approve', PaymentStatus::APPROVED)
            ->selectRaw('SUM(expenditure_details.amount_spent) as total')
            ->first();

        return is_null($expenses->total) ? 0 : $expenses->total;
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
        if(isset($status) && $status != "ALL"){
            $details = $details->Where('expenditure_details.approve', $status);
        }
        return $details;
    }

    private function getMonthlyExpenditures($session_id)
    {
        $expenditures = [];
        for ($month = 1; $month <= 12; $month ++){
            $expenditure = DB::table('expenditure_details')
                        ->join('expenditure_items', 'expenditure_items.id', '=', 'expenditure_details.expenditure_item_id')
                        ->join('sessions', 'sessions.id', '=', 'expenditure_items.session_id')
                        ->where('sessions.id', $session_id)
                        ->where('expenditure_items.approve', PaymentStatus::APPROVED)
                        ->whereMonth('expenditure_items.date', $month)
                        ->selectRaw('SUM(expenditure_details.amount_spent) as total')
                        ->first();
            array_push($expenditures, is_null($expenditure->total) ? 0 : $expenditure->total);
        }

        return $expenditures;
    }

    private function getExpendituresByPaymentItems($items, $session_id)
    {
        $expenditures = [];
        foreach ($items as $item){
            $expenditure = DB::table('expenditure_details')
                            ->join('expenditure_items', 'expenditure_items.id', '=', 'expenditure_details.expenditure_item_id')
                            ->join('sessions', 'sessions.id', '=', 'expenditure_items.session_id')
                            ->join('payment_items', 'payment_items.id', '=', 'expenditure_items.payment_item_id')
                            ->where('sessions.id', $session_id)
                            ->where('payment_items.id', $item->id)
                            ->where('expenditure_items.approve', PaymentStatus::APPROVED)
                            ->selectRaw('SUM(expenditure_details.amount_spent) as total')
                            ->first();
            $amount = is_null($expenditure->total) ? 0 : $expenditure->total;
            array_push($expenditures, ["name" => $item->name, "amount" => $amount]);
        }

        return $expenditures;
    }

    private function setExpenditureItemAmount($data) {
        return count($data->toArray()) == 0 ? 0: $data[0]->expenditure_item_amount;
    }

}
