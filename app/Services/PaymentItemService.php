<?php
namespace App\Services;

use App\Constants\PaymentItemFrequency;
use App\Constants\PaymentItemType;
use App\Http\Resources\PaymentItemCollection;
use App\Interfaces\PaymentItemInterface;
use App\Models\PaymentCategory;
use App\Models\PaymentItem;
use App\Traits\HelpTrait;
use Illuminate\Support\Facades\DB;

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
            'reference'           => $this->setPaymentItemReference($request->type)
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
            'reference'     => $this->setPaymentItemReference($request->type)
        ]);
    }

    public function getPaymentItemsByCategory($payment_category_id)
    {
        $payment_items = $this->fetchPaymentItems($payment_category_id)
//                                ->orWhere('frequency', PaymentItemFrequency::YEARLY)
//                                ->orWhere('frequency', PaymentItemFrequency::MONTHLY)
//                                ->orWhere('frequency',PaymentItemFrequency::ONE_TIME)
//                                ->orWhere('frequency',PaymentItemFrequency::QUARTERLY)
                                ->orderBy('payment_items.name', 'ASC')
                                ->get();
        return new PaymentItemCollection($payment_items, 0);

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
        if(!is_null($request->compulsory)){
            $payment_items = $payment_items->where('compulsory', $request->compulsory);
        }
        if(!is_null($request->type) && $request->type !== "ALL"){
            $payment_items = $payment_items->where('type', $request->type);
        }
        if(!is_null($request->frequency) && $request->frequency !== "ALL"){
            $payment_items = $payment_items->where('frequency', $request->frequency);
        }
        $payment_items = $payment_items->orderBy('payment_items.name', 'DESC')->get();

        return new PaymentItemCollection($payment_items, 0);
    }

    public function getPaymentItems() {
        $current_session = $this->session_service->getCurrentSession();
        return PaymentItem::where('session_id', $current_session->id)->get();
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
        return PaymentItem::select('payment_items.id', 'payment_items.name','payment_items.amount')
            ->join('payment_categories', ['payment_categories.id'  => 'payment_items.payment_category_id'])
            ->where('payment_items.payment_category_id', $category)
            ->where('session_id', $session)
            ->orderBy('payment_items.name', 'ASC')
            ->get();

    }

    public function getPaymentItemsBySessionAndFrequency($request)
    {
        return DB::table('payment_items')
            ->join('payment_categories', 'payment_categories.id', '=', 'payment_items.payment_category_id')
            ->join('sessions', 'sessions.id', '=', 'payment_items.session_id')
            ->where('payment_items.session_id', $request->session_id)
            ->orWhere('payment_items.frequency', PaymentItemFrequency::YEARLY)
            ->select('payment_items.id', 'payment_items.name', 'payment_items.amount', 'payment_items.session_id')
            ->distinct()
            ->get()->toArray();
    }

    public function getPaymentItemsByFrequency($session_id, $frequency)
    {
        return DB::table('payment_items')
            ->join('payment_categories', 'payment_categories.id', '=', 'payment_items.payment_category_id')
            ->join('sessions', 'sessions.id', '=', 'payment_items.session_id')
            ->where('payment_items.session_id', $session_id)
            ->Where('payment_items.frequency', $frequency)
            ->select('payment_items.id', 'payment_items.name', 'payment_items.amount', 'payment_items.session_id')
            ->distinct()
            ->get()->toArray();
    }

    public function getPaymentItemsByType($session_id, $type)
    {
        return DB::table('payment_items')
            ->join('payment_categories', 'payment_categories.id', '=', 'payment_items.payment_category_id')
            ->join('sessions', 'sessions.id', '=', 'payment_items.session_id')
            ->where('payment_items.session_id', $session_id)
            ->Where('payment_items.type', $type)
            ->select('payment_items.id', 'payment_items.name', 'payment_items.amount', 'payment_items.session_id')
            ->distinct()
            ->get()->toArray();
    }

    private function findPaymentItem($id, $payment_category_id)
    {
        return PaymentItem::select('payment_items.*')->join('payment_categories', ['payment_categories.id' => 'payment_items.payment_category_id'])
            ->where('payment_items.id', $id)
            ->where('payment_items.payment_category_id', $payment_category_id)
            ->firstOrFail();
    }

    private  function fetchPaymentItems($payment_category_id) {
        $current_session = $this->session_service->getCurrentSession();
        return PaymentItem::select('payment_items.*')
            ->join('payment_categories', ['payment_categories.id'  => 'payment_items.payment_category_id'])
            ->where('payment_items.payment_category_id', $payment_category_id)
            ->where('session_id', $current_session->id);

    }

    private function setPaymentItemReference($type)
    {
        $reference = null;
        if ($type == PaymentItemType::MEMBERS_WITH_ROLES){
            $reference = $this->getAllAdminsId();
        }
        if($type == PaymentItemType::MEMBERS_WITHOUT_ROLES){
            $reference = $this->getAllNoAdminsId();
        }
        return $reference;
    }
}
