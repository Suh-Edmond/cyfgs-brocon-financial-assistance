<?php
namespace App\Services;

use App\Constants\Constants;
use App\Constants\PaymentStatus;
use App\Exceptions\BusinessValidationException;
use App\Http\Resources\ExpenditureItemCollection;
use App\Http\Resources\ExpenditureItemResource;
use App\Interfaces\ExpenditureItemInterface;
use App\Models\ExpenditureCategory;
use App\Models\ExpenditureItem;
use App\Models\PaymentItem;
use App\Traits\HelpTrait;

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
            throw new BusinessValidationException("Expenditure Item cannot be updated after been approved or declined", 403);
        }
    }

    public function getExpenditureItems($expenditure_category_id, $request)
    {
        $items = $this->findExpenditureItems($expenditure_category_id, $request->status, $request->session_id);

        return $this->generateExpenditureItemResponse($items);
    }

    public function getExpenditureItem($id, $expenditure_category_id): ExpenditureItemResource
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
        return ExpenditureItem::where('id', $id)->where('expenditure_category_id', $expenditure_category_id)->firstOrFail();
    }

    private function findExpenditureItems($expenditure_category_id, $status, $session_id)
    {

        $data = ExpenditureItem::where('expenditure_category_id', $expenditure_category_id);
        if($status != Constants::ALL){
            $data = $data->where('approve', $status);
        }
        if(!is_null($session_id)){
            $data = $data->where('session_id', $session_id);
        }
        return $data->orderBy('name', 'ASC')->get();
    }

    private function generateExpenditureItemResponse($items)
    {
        $expenses = [];
        foreach ($items as $item){
            $amount_given = $this->calculateTotalAmountGiven($item->expenditureDetails);
            $amount_spent =  $this->calculateTotalAmountSpent($item->expenditureDetails);
            $balance = $this->calculateExpenditureBalanceByExpenditureItem($amount_given, $amount_spent, $item->amount);

            $expenses[] = new ExpenditureItemResource($item, $amount_given, $amount_spent, $balance);
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
        $items = ExpenditureItem::where('expenditure_category_id', $expenditure_category_id)
                        ->where('session_id', $request->session_id)
                        ->orderBy('name', 'ASC');
        if(isset($request->payment_item_id)){
            $items = $items->where('payment_item_id', $request->payment_item_id);
        }
        if(isset($request->status)  && $request->status != Constants::ALL) {
            $items = $items->where('approve', $request->status);
        }
        if(isset($request->filter)){
            $items = $items->where('name', 'LIKE', '%'.$request->filter.'%');
        }
        $paginated_data  = $items->paginate($request->per_page);
        return (new ExpenditureItemCollection($this->generateExpenditureItemResponse($paginated_data), $paginated_data->total(),
            $paginated_data->lastPage(), (int)$paginated_data->perPage(), $paginated_data->currentPage()));
    }

    public function getExpenditureItemsByPaymentItem($item, $request)
    {
        $items = ExpenditureItem::where('payment_item_id', $item);

        if(!is_null($request->status) && $request->status != Constants::ALL){
            $items = $items->where('approve', $request->status);
        }
        $items = $items->orderBy('name', 'ASC')->get();
        return $this->generateExpenditureItemResponse($items);
    }

    public function filterExpenditureItems($request)
    {

        $items = ExpenditureItem::where('expenditure_category_id', $request->expenditure_category_id);
        if(isset($request->session_id)){
            $items = $items->where('session_id', $request->session_id);
        }
        if(isset($request->payment_item_id)){
            $items = $items->where('payment_item_id', $request->payment_item_id);
        }
        if(isset($request->filter)){
            $items = $items->where('name', 'LIKE', '%'.$request->filter.'%');
        }
        $items = $items->orderBy('name', 'ASC');

        $expenditure_items  =isset($request->per_page)? $items->paginate($request->per_page) : $items->get();

        $total = isset($request->per_page) ? $expenditure_items->total() : count($expenditure_items);
        $last_page = isset($request->per_page) ? $expenditure_items->lastPage(): 0;
        $per_page = isset($request->per_page) ? (int)$expenditure_items->perPage() : 0;
        $current_page = isset($request->per_page) ? $expenditure_items->currentPage() : 0;

        return new ExpenditureItemCollection($this->generateExpenditureItemResponse($expenditure_items), $total, $last_page,
            $per_page, $current_page);
    }

    public function downloadExpenditureItems($request)
    {
        return $this->filterExpenditureItems($request);
    }

    public function getExpensesByCategoryAndQuarter($category_id, $start_quarter, $end_quarter){
       return ExpenditureItem::where('expenditure_category_id', $category_id)
                       ->where('approve', PaymentStatus::APPROVED)
                       ->whereBetween('created_at', [$start_quarter, $end_quarter])
                       ->orderBy('name')
                       ->get();
    }

    public function getExpensesByCategoryAndYear($category_id, $year){
        return ExpenditureItem::where('expenditure_category_id', $category_id)
                        ->where('approve', PaymentStatus::APPROVED)
                        ->where('session_id', $year)
                        ->orderBy('name')
                        ->get();
    }


}
