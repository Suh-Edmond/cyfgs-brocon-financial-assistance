<?php
namespace App\Services;

use App\Constants\PaymentStatus;
use App\Exceptions\BusinessValidationException;
use App\Http\Resources\ExpenditureItemCollection;
use App\Http\Resources\ExpenditureItemResource;
use App\Http\Resources\QuarterlyExpenditureResource;
use App\Interfaces\ExpenditureItemInterface;
use App\Models\ExpenditureCategory;
use App\Models\ExpenditureItem;
use App\Models\PaymentItem;
use App\Traits\HelpTrait;
use Illuminate\Support\Facades\DB;

class ExpenditureItemService implements ExpenditureItemInterface {

    use HelpTrait;
    private SessionService $session_service;

    public function __construct(SessionService $sessionService)
    {
        $this->session_service = $sessionService;
    }

    public function createExpenditureItem($request, $expenditure_category_id)
    {
        $expenditure_category = ExpenditureCategory::findOrFail($expenditure_category_id);
        $payment_item = PaymentItem::findOrFail($request->payment_item_id);
        $current_session = $this->session_service->getCurrentSession();

        ExpenditureItem::create([
            'name'                      => $request->name,
            'amount'                    => $request->amount,
            'venue'                     => $request->venue,
            'comment'                   => $request->comment,
            'date'                      => $request->date,
            'expenditure_category_id'   => $expenditure_category->id,
            'scan_picture'              => $request->scan_picture,
            'updated_by'                => $request->user()->name,
            'payment_item_id'           => $payment_item->id,
            'session_id'                => $current_session->id,
        ]);
    }

    public function updateExpenditureItem($request, $id, $expenditure_category_id)
    {
        $expenditure_item = $this->findExpenditureItem($id, $expenditure_category_id);

        if($expenditure_item->approve == PaymentStatus::PENDING){
            $expenditure_item->update([
                'name'                      => $request->name,
                'amount'                    => $request->amount,
                'venue'                     => $request->venue,
                'comment'                   => $request->comment,
                'date'                      => $request->date,
                'scan_picture'              => $request->scan_picture
            ]);
        }else {
            throw new BusinessValidationException("Expenditure Item cannot be updated after been approved or declined");
        }
    }

    public function getExpenditureItems($expenditure_category_id, $request)
    {
        $items = $this->findExpenditureItems($expenditure_category_id, $request->status, $request->session_id);

        return $this->generateExpenditureItemResponse($items);
    }

    public function getExpenditureItem($id, $expenditure_category_id)
    {
        $expenditure_item = $this->findExpenditureItem($id, $expenditure_category_id);
        $amount_given = $this->calculateTotalAmountGiven($expenditure_item->expenditureDetails);
        $amount_spent =  $this->calculateTotalAmountSpent($expenditure_item->expenditureDetails);
        $balance = $this->calculateExpenditureBalanceByExpenditureItem($amount_given, $amount_spent, $expenditure_item->amount);
        return new ExpenditureItemResource($expenditure_item, $amount_given, $amount_spent, $balance);
    }

    public function deleteExpenditureItem($id, $expenditure_category_id)
    {
        $expenditure_item = $this->findExpenditureItem($id, $expenditure_category_id);

        $expenditure_item->delete();
    }

    public function approveExpenditureItem($id, $type)
    {
        $expenditure_item = ExpenditureItem::findOrFail($id);
        if($expenditure_item->approve == PaymentStatus::PENDING){
            $expenditure_item->approve = $type;
            $expenditure_item->save();
        }
    }

    private function findExpenditureItem($id, $expenditure_category_id)
    {
        return ExpenditureItem::select('expenditure_items.*')
                                        ->join('expenditure_categories', ['expenditure_categories.id' => 'expenditure_items.expenditure_category_id'])
                                        ->where('expenditure_items.id', $id)
                                        ->where('expenditure_items.expenditure_category_id', $expenditure_category_id)
                                        ->firstOrFail();
    }

    private function findExpenditureItems($expenditure_category_id, $status, $session_id)
    {
        $data = ExpenditureItem::select('expenditure_items.*')
                            ->join('expenditure_categories', ['expenditure_categories.id' => 'expenditure_items.expenditure_category_id'])
                            ->join('sessions', ['sessions.id' => 'expenditure_items.session_id'])
                            ->where('expenditure_items.expenditure_category_id', $expenditure_category_id);
        if($status != "ALL"){
            $data = $data->where('expenditure_items.approve', $status);
        }
        if(!is_null($session_id)){
            $data = $data->where('expenditure_items.session_id', $session_id);
        }
        $data = $data->orderBy('expenditure_items.name', 'ASC')->get();

        return $data;
    }

    private function generateExpenditureItemResponse($items)
    {
        $expenses = [];
        foreach ($items as $item){
            $amount_given = $this->calculateTotalAmountGiven($item->expenditureDetails);
            $amount_spent =  $this->calculateTotalAmountSpent($item->expenditureDetails);
            $balance = $this->calculateExpenditureBalanceByExpenditureItem($amount_given, $amount_spent, $item->amount);
            array_push($expenses, new ExpenditureItemResource($item, $amount_given, $amount_spent, $balance));
        }
        return ($expenses);
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

    public function getExpenditureByCategory($expenditure_category_id, $request)
    {
        $items = ExpenditureItem::select('expenditure_items.*')
            ->join('expenditure_categories', ['expenditure_categories.id' => 'expenditure_items.expenditure_category_id'])
            ->join('sessions', ['sessions.id' => 'expenditure_items.session_id'])
            ->where('expenditure_items.expenditure_category_id', $expenditure_category_id)
            ->where('expenditure_items.session_id', $request->session_id)
            ->orderBy('expenditure_items.name', 'ASC');
        if(isset($request->payment_item_id)){
            $items = $items->where('expenditure_items.payment_item_id', $request->payment_item_id);
        }
        if(isset($request->status)  && $request->status != "ALL") {
            $items = $items->where('expenditure_items.approve', $request->status);
        }
        $paginated_data  = $items->paginate($request->per_page);
        return (new ExpenditureItemCollection($this->generateExpenditureItemResponse($paginated_data), $paginated_data->total(),
            $paginated_data->lastPage(), (int)$paginated_data->perPage(), $paginated_data->currentPage()));
    }

    public function getExpenditureItemsByPaymentItem($item, $request)
    {
        $items = ExpenditureItem::select('expenditure_items.*')
            ->join('payment_items', ['payment_items.id' => 'expenditure_items.payment_item_id'])
            ->where('expenditure_items.payment_item_id', $item);
        if(!is_null($request->status) && $request->status != "ALL"){
            $items = $items->where('expenditure_items.approve', $request->status);
        }
        $items = $items->orderBy('expenditure_items.name', 'ASC')->get();
        return $this->generateExpenditureItemResponse($items);
    }

    public function filterExpenditureItems($request)
    {

        $items = ExpenditureItem::select('expenditure_items.*')
            ->join('payment_items', ['payment_items.id' => 'expenditure_items.payment_item_id'])
            ->join('expenditure_categories', ['expenditure_categories.id' => 'expenditure_items.expenditure_category_id'])
            ->join('sessions', ['sessions.id' => 'expenditure_items.session_id'])
            ->where('expenditure_categories.id', $request->expenditure_category_id)
            ->where('expenditure_items.session_id', $request->session_id);
        if(!is_null($request->payment_item_id)){
            $items = $items->where('expenditure_items.payment_item_id', $request->payment_item_id);
        }
        if(!is_null($request->status)  && $request->status != "ALL") {
            $items = $items->where('expenditure_items.approve', $request->status);
        }
        $items = $items->orderBy('expenditure_items.name', 'ASC');
        $paginated_data  = $items->paginate($request->per_page);
        return (new ExpenditureItemCollection($this->generateExpenditureItemResponse($paginated_data), $paginated_data->total(),
            $paginated_data->lastPage(), $paginated_data->perPage(), $paginated_data->currentPage()));
    }

    public function downloadExpenditureItems($request)
    {
        return $this->filterExpenditureItems($request);
    }

    public function getExpensesByCategoryAndQuarter($category_id, $start_quarter, $end_quarter){
        return  DB::table('expenditure_items')
                ->join('payment_items', 'payment_items.id', '=', 'expenditure_items.payment_item_id')
                ->join('expenditure_categories', 'expenditure_categories.id' , '=', 'expenditure_items.expenditure_category_id')
                ->where('expenditure_items.approve', PaymentStatus::APPROVED)
                ->where('expenditure_categories.id', $category_id)
                ->whereBetween('expenditure_items.created_at', [$start_quarter, $end_quarter])
                ->select( 'expenditure_items.name', 'expenditure_items.amount', 'expenditure_items.id')
                ->orderBy('name')
                ->get();
    }

    public function getExpensesByCategoryAndYear($category_id, $year){
        return  DB::table('expenditure_items')
            ->join('payment_items', 'payment_items.id', '=', 'expenditure_items.payment_item_id')
            ->join('expenditure_categories', 'expenditure_categories.id' , '=', 'expenditure_items.expenditure_category_id')
            ->join('sessions', 'sessions.id' , '=', 'expenditure_items.session_id')
            ->where('expenditure_items.approve', PaymentStatus::APPROVED)
            ->where('expenditure_categories.id', $category_id)
            ->where('expenditure_items.session_id', $year)
            ->select( 'expenditure_items.name', 'expenditure_items.amount', 'expenditure_items.id')
            ->orderBy('name')
            ->get();
    }

}
