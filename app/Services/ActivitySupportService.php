<?php


namespace App\Services;


use App\Interfaces\ActivitySupportInterface;
use App\Models\ActivitySupport;
use App\Models\PaymentItem;
use App\Traits\HelpTrait;

class ActivitySupportService implements ActivitySupportInterface
{
    use HelpTrait;

    public function createActivitySupport($request)
    {
        $payment_item = $this->findPaymentItem($request->payment_item_id);
        ActivitySupport::create([
            'code'              => $this->generateCode(10),
            'amount_deposited'  => $request->amount_deposited,
            'comment'           => $request->comment,
            'supporter'         => $request->supporter,
            'payment_item_id'   => $payment_item->id,
            'scan_picture'      => $request->scan_picture,
            'updated_by'        => $request->user()->name,
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
        return ActivitySupport::where('payment_item_id', $id)->orderBy('created_at', 'DESC')->get();

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
        $supports = ActivitySupport::where('payment_item_id', $request->payment_item_id);
        if(!is_null($request->status)){
            $supports = $supports->where('approve', $request->status);
        }
        return $supports->orderBy('created_at', 'DESC')->get();
    }

    private function findPaymentItem($payment_item)
    {
        return PaymentItem::findOrFail($payment_item);
    }
    private function findActivitySupport($id) {
        return ActivitySupport::findOrFail($id);
    }
}
