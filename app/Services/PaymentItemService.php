<?php
namespace App\Services;

use App\Constants\PaymentItemFrequency;
use App\Constants\PaymentItemType;
use App\Http\Resources\PaymentItemCollection;
use App\Http\Resources\PaymentItemResource;
use App\Interfaces\PaymentItemInterface;
use App\Models\PaymentCategory;
use App\Models\PaymentItem;
use App\Traits\HelpTrait;
use Carbon\Carbon;

class PaymentItemService implements PaymentItemInterface {

    use HelpTrait;
    private SessionService $session_service;

    public function __construct(SessionService $sessionService)
    {
        $this->session_service = $sessionService;
    }

    public function createPaymentItem($request, $payment_category_id)
    {
        $current_session = $this->session_service->getCurrentSession();

        $payment_category = PaymentCategory::findOrFail($payment_category_id);
        PaymentItem::create([
            'name'                => $request->name,
            'amount'              => $request->amount,
            'compulsory'          => $request->compulsory,
            'payment_category_id' => $payment_category->id,
            'description'         => $request->description,
            'updated_by'          => $request->user()->name,
            'type'                => $request->type,
            'frequency'           => $request->frequency,
            'session_id'          => $current_session->id,
            'reference'           => $this->setPaymentItemReference($request->reference, $request->type),
            'deadline'            => $request->deadline
        ]);
    }

    public function updatePaymentItem($request, $payment_item_id, $payment_category_id)
    {
        $updated =  $this->findPaymentItem($payment_item_id, $payment_category_id);
        $updated->update([
            'name'          => $request->name,
            'amount'        => $request->amount,
            'compulsory'    => $request->compulsory,
            'description'   => $request->description,
            'type'          => $request->type,
            'frequency'     => $request->frequency,
            'reference'     => $this->setPaymentItemReference($request->reference, $request->type),
            'deadline'      => $request->deadline
        ]);
    }

    public function getPaymentItemsByCategory($payment_category_id, $request)
    {
        $payment_items = $this->fetchPaymentItems($payment_category_id)
                                ->orderBy('payment_items.name', 'ASC')
                                ->paginate($request->per_page);
        return  new PaymentItemCollection($payment_items, $payment_items->total(), $payment_items->currentPage(), (int)$payment_items->perPage(), $payment_items->lastPage());

    }

    public function getPaymentItem($id, $payment_category_id)
    {
        return $this->findPaymentItem($id, $payment_category_id);

    }

    public function deletePaymentItem($id, $payment_category_id)
    {
        $payment_item = $this->findPaymentItem($id, $payment_category_id);

        $payment_item->delete();
    }

    public function filterPaymentItems($request) {

        $payment_items = $this->fetchPaymentItems($request->payment_category_id);
        if(isset($request->session_id)){
            $payment_items = $payment_items->where('session_id', $request->session_id);
        }
        if(isset($request->is_compulsory) && $request->is_compulsory != "ALL"){
            $payment_items = $payment_items->where('compulsory', $request->is_compulsory);
        }
        if(isset($request->type) && $request->type != "ALL"){
            $payment_items = $payment_items->where('type', $request->type);
        }
        if(isset($request->frequency) && $request->frequency != "ALL"){
            $payment_items = $payment_items->where('frequency', $request->frequency);
        }
        if(isset($request->state) && $request->state == "active"){
            $payment_items = $payment_items->whereDate('deadline', '>=', Carbon::now()->toDateString());
        }
        if (isset($request->state) && $request->state == "expired"){
            $payment_items = $payment_items->whereDate('deadline', '<=', Carbon::now()->toDateString());
        }
        if(isset($request->filter)){
            $payment_items = $payment_items->where('payment_items.name', 'LIKE', '%'.$request->filter.'%');
        }
        $payment_items_response =   isset($request->per_page) ? $payment_items->orderBy('payment_items.name')->paginate($request->per_page): $payment_items->orderBy('payment_items.name')->get();

        $total         =   isset($request->per_page) ? $payment_items_response->total()         : count($payment_items_response);
        $last_page     =   isset($request->per_page) ? $payment_items_response->lastPage()      : 0;
        $per_page      =   isset($request->per_page) ? (int) $payment_items_response->perPage() : 0;
        $current_page  =   isset($request->per_page) ? $payment_items_response->currentPage()   : 0;
        return new PaymentItemCollection($payment_items_response, $total, $last_page,
            $per_page, $current_page);
    }

    public function getPaymentItems($request) {
        $payment_items = array();
        $session_id = $request->session_id ?? $this->session_service->getCurrentSession()->id;

        $session_payment_items =  PaymentItem::where('session_id', $session_id)->get();
        foreach ($session_payment_items as $payment_item){
            if($payment_item->frequency == PaymentItemFrequency::QUARTERLY){
                $quarters = $this->getPaymentItemQuartersBySession($payment_item->frequency, $payment_item->created_at);
                $payment_items[] = new PaymentItemResource($payment_item, $quarters, []);
            }elseif ($payment_item->frequency == PaymentItemFrequency::MONTHLY) {
                $months = $this->getPaymentItemMonthsBySession($payment_item->frequency, $payment_item->created_at);
                $payment_items[] = new PaymentItemResource($payment_item, [], $months);
            }else {
                $payment_items[] = new PaymentItemResource($payment_item, [], []);
            }
        }

        return $payment_items;
    }

    public function getPaymentItemByType($type)
    {
        return PaymentItem::where('type', $type)->get();
    }

    public function updatePaymentItemReference($request)
    {
        $payment_item = PaymentItem::findOrFail($request->id);
        $updated_references = "";
        if(!is_null($payment_item->reference)){
            $references = explode("/", $payment_item->reference);
            foreach ($references as $key => $reference){
                if($reference == $request->reference_id){
                    unset($references[$key]);
                }else {
                    $updated_references = trim($reference) . "/" . trim($updated_references);
                }
            }
        }
        $payment_item->update([
            'reference' => trim($updated_references)
        ]);
    }

    public function getPaymentItemReferences($id) {
        $payment_item = PaymentItem::findOrFail($id);
        return $this->getReferenceResource($payment_item->reference);
    }

    public function getPaymentActivitiesByCategoryAndSession($category, $session){
        return PaymentItem::where('payment_category_id', $category)
                    ->where('session_id', $session)
                    ->orderBy('name', 'ASC')
                    ->get();
    }

    public function getPaymentActivitiesByCategoryAndSessionAndQuarter($category, $request, $current_year, $type)
    {

        $quarter_range = $this->getStartQuarter($current_year->year,  $request->quarter, $type);
        $start_quarter = $quarter_range[0];
        $end_quarter = $quarter_range[1];

        return PaymentItem::where('payment_category_id', $category)
            ->where('session_id', $current_year->id)
            ->whereBetween('created_at', [$start_quarter, $end_quarter])
            ->orderBy('name', 'ASC')
            ->get()->toArray();
    }

    public function getPaymentItemsBySessionAndFrequency($request)
    {
        return PaymentItem::where('session_id', $request->session_id)
            ->orWhere('frequency', PaymentItemFrequency::YEARLY)
            ->distinct()
            ->get()->toArray();
    }

    public function getPaymentItemsByFrequency($session_id, $frequency)
    {
        return PaymentItem::where('session_id', $session_id)
            ->where('frequency', $frequency)
            ->distinct()
            ->get()->toArray();
    }

    public function getPaymentItemsByType($session_id, $type)
    {
        return PaymentItem::where('session_id', $session_id)->where('type', $type)->distinct()->get()->toArray();
    }

    public function getPaymentItemsForBalanceSheet($session_id)
    {
        return PaymentItem::where('session_id', $session_id)->distinct()->orderBy('created_at')->get();
    }

    private function findPaymentItem($id, $payment_category_id)
    {
        return PaymentItem::where('id', $id)->where('payment_category_id', $payment_category_id)->firstOrFail();
    }

    private  function fetchPaymentItems($payment_category_id) {
        return PaymentItem::where('payment_category_id', $payment_category_id);
    }

    private function setPaymentItemReference($reference, $type)
    {
        if ($type == PaymentItemType::MEMBERS_WITH_ROLES){
            $reference = $this->getAllAdminsId();
        }
        if($type == PaymentItemType::MEMBERS_WITHOUT_ROLES){
            $reference = $this->getAllNoAdminsId();
        }
        return $reference;
    }

    private function getPaymentItemQuartersBySession($item_frequency, $item_created_at)
    {
        $quarters = $this->getQuarters();
        $current_quarter = $this->convertQuarterNameToNumber($this->getDateQuarter($item_frequency, $item_created_at));
        return array_splice($quarters, ($current_quarter - 1), count($quarters));
    }


}
