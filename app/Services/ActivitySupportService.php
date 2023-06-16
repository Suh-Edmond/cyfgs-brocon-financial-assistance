<?php


namespace App\Services;


use App\Constants\PaymentStatus;
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
        return $supports->orderBy('created_at', 'DESC')->get();
    }

    public function fetchAllActivitySupport()
    {
        $current_session = $this->sessionService->getCurrentSession();
        return ActivitySupport::where('session_id',$current_session->id)->get();
    }

    public function changeActivityState($id, $request)
    {
        $sponsorship = ActivitySupport::findOrFail($id);

        $sponsorship->approve = $request->type;

        $sponsorship->save();
    }

    public function getSponsorshipIncomePerQuarterly($quarter_num, $current_year): array
    {
        $start_quarter = $this->getStartQuarter($current_year->year, $quarter_num)[0];
        $end_quarter = $this->getStartQuarter($current_year->year, $quarter_num)[1];
        return  DB::table('activity_supports')
            ->join('payment_items', 'payment_items.id', '=', 'activity_supports.payment_item_id')
            ->join('sessions', 'sessions.id' , '=', 'activity_supports.session_id')
            ->where('activity_supports.approve', PaymentStatus::APPROVED)
            ->whereBetween('activity_supports.created_at', [$start_quarter, $end_quarter])
            ->select('activity_supports.id', 'activity_supports.supporter as name', 'activity_supports.amount_deposited as amount', 'sessions.year')
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function getSponsorshipPerActivity($id)
    {
        return DB::table('activity_supports')
            ->join('payment_items', 'payment_items.id', '=', 'activity_supports.payment_item_id')
            ->where('activity_supports.payment_item_id', $id)
            ->where('activity_supports.approve', PaymentStatus::APPROVED)
            ->select('activity_supports.supporter as name', 'activity_supports.amount_deposited as amount')
            ->orderBy('activity_supports.supporter')
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
