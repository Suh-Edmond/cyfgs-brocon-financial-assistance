<?php


namespace App\Services;


use App\Constants\PaymentStatus;
use App\Http\Resources\ActivitySupportCollection;
use App\Interfaces\ActivitySupportInterface;
use App\Models\ActivitySupport;
use App\Models\PaymentItem;
use App\Traits\HelpTrait;
use Illuminate\Support\Facades\DB;

class ActivitySupportService implements ActivitySupportInterface
{
    use HelpTrait;
    private SessionService $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    public function createActivitySupport($request)
    {
        $current_session = $this->sessionService->getCurrentSession();
        $payment_item = $this->findPaymentItem($request->payment_item_id);
        ActivitySupport::create([
            'code'              => $this->generateCode(10),
            'amount_deposited'  => $request->amount_deposited,
            'comment'           => $request->comment,
            'supporter'         => $request->supporter,
            'payment_item_id'   => $payment_item->id,
            'scan_picture'      => $request->scan_picture,
            'updated_by'        => $request->user()->name,
            'session_id'           => $current_session->id
        ]);
    }

    public function updateActivitySupport($id, $request)
    {
        $payment_item = $this->findPaymentItem($request->payment_item_id);
        $support = $this->findActivitySupport($id);
        $support->update([
            'amount_deposited'  => $request->amount_deposited,
            'comment'           => $request->comment,
            'supporter'         => $request->supporter,
            'payment_item_id'   => $payment_item->id,
            'scan_picture'      => $request->scan_picture,
            'updated_by'        => $request->user()->name,
        ]);
    }

    public function getActivitySupportsByPaymentItem($id)
    {
        $current_session = $this->sessionService->getCurrentSession();
        return ActivitySupport::where('payment_item_id', $id)->where('session_id',$current_session->id)->orderBy('created_at', 'DESC')->get();

    }

    public function getActivitySupport($id)
    {
        return $this->findActivitySupport($id);
    }

    public function deleteActivitySupport($id)
    {
        $deleted = $this->findActivitySupport($id);
        $deleted->delete();
    }

    public function filterActivitySupport($request)
    {
        $current_session = $this->sessionService->getCurrentSession();
        $supports = ActivitySupport::where('session_id',$current_session->id);
        if(!is_null($request->payment_item_id)){
            $supports = $supports->where('payment_item_id', $request->payment_item_id);
        }
        if(!is_null($request->status) && $request->status != "ALL"){
            $supports = $supports->where('approve', $request->status);
        }
        if(isset($request->filter)){
            $supports = $supports->where('activity_supports.supporter', 'LIKE', '%'.$request->filter.'%');
        }
        return $supports->orderBy('created_at', 'DESC')->get();
    }

    public function fetchAllActivitySupport($request)
    {
        $current_session = $this->sessionService->getCurrentSession();
        $sponsorships =  !isset($request->session_id) ? ActivitySupport::where('session_id',$current_session->id): null;
        if(isset($request->session_id)){
            $sponsorships = ActivitySupport::where('session_id', $request->session_id);
        }
        if(!is_null($request->payment_item_id)){
            $sponsorships = $sponsorships->where('payment_item_id', $request->payment_item_id);
        }
        if(!is_null($request->status) && $request->status != "ALL"){
            $sponsorships = $sponsorships->where('approve', $request->status);
        }
        if(isset($request->filter)){
            $sponsorships = $sponsorships->where('activity_supports.supporter', 'LIKE', '%'.$request->filter.'%');
        }
        $sponsorships = $sponsorships->orderBy('created_at', 'DESC');
        $total_sponsorship = $this->computeTotalSponsorship($sponsorships->get());
        $paginated_data    = $sponsorships->paginate($request->per_page);

        return new ActivitySupportCollection($paginated_data, $total_sponsorship, $paginated_data->total(),
        $paginated_data->lastPage(), (int)$paginated_data->perPage(), $paginated_data->currentPage());
    }

    public function changeActivityState($id, $request)
    {
        $sponsorship = ActivitySupport::findOrFail($id);

        $sponsorship->approve = $request->type;

        $sponsorship->save();
    }

    public function getSponsorshipIncomePerQuarterly($current_year, $payment_item, $start_quarter, $end_quarter): array
    {

        return  DB::table('activity_supports')
            ->join('payment_items', 'payment_items.id', '=', 'activity_supports.payment_item_id')
            ->join('sessions', 'sessions.id' , '=', 'activity_supports.session_id')
            ->where('activity_supports.approve', PaymentStatus::APPROVED)
            ->where('payment_items.id', $payment_item['id'])
            ->where('sessions.id', $current_year->id)
            ->whereBetween('activity_supports.created_at', [$start_quarter, $end_quarter])
            ->select('activity_supports.id', 'activity_supports.supporter as name', 'activity_supports.amount_deposited as amount', 'sessions.year')
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function getSponsorshipIncomePerYear($year, $payment_item): array
    {
        return ActivitySupport::where('approve', PaymentStatus::APPROVED)
                        ->where('session_id', $year)
                        ->where('payment_item_id', $payment_item['id'])
                        ->select('id', 'supporter as name', 'amount_deposited as amount')
                        ->orderBy('name')
                        ->get()
                        ->toArray();
    }

    public function getSponsorshipPerActivity($id)
    {
        return ActivitySupport::where('payment_item_id', $id)
                       ->where('approve', PaymentStatus::APPROVED)
                       ->select('supporter as name', 'amount_deposited as amount')
                       ->orderBy('name')
                       ->get();
    }

    private function findPaymentItem($payment_item)
    {
        return PaymentItem::findOrFail($payment_item);
    }
    private function findActivitySupport($id) {
        return ActivitySupport::findOrFail($id);
    }
}
