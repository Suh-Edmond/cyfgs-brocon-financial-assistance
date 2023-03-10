<?php
namespace App\Services;

use App\Http\Resources\IncomeActivityCollection;
use App\Interfaces\IncomeActivityInterface;
use App\Models\IncomeActivity;
use App\Models\Organisation;
use App\Models\PaymentItem;
use App\Traits\HelpTrait;


class IncomeActivityService implements IncomeActivityInterface {

    use HelpTrait;

    public function createIncomeActivity($request, $id)
    {
        $organisation = Organisation::findOrFail($id);
        $payment_item_id = $this->getPaymentItemId($request->payment_item_id);

        IncomeActivity::create([
            'name'              => $request->name,
            'description'       => $request->description,
            'date'              => $request->date,
            'amount'            => $request->amount,
            'venue'             => $request->venue,
            'organisation_id'   => $organisation->id,
            'payment_item_id'   => $payment_item_id,
            'updated_by'        =>$request->user()->name,
            'scan_picture'      => $request->scan_picture
        ]);
    }

    public function updateIncomeActivity($request, $id)
    {
        $activity = $this->findIncomeActivity($id);
        $payment_item_id = $this->getPaymentItemId($request->payment_item_id);

        $activity->update([
            'name'              => $request->name,
            'description'       => $request->description,
            'date'              => $request->date,
            'amount'            => $request->amount,
            'venue'             => $request->venue,
            'payment_item_id'   => $payment_item_id
        ]);
    }

    public function getIncomeActivities($organisation_id)
    {
        return $this->findIncomeActivities($organisation_id)
                            ->orderBy('income_activities.name', 'ASC')
                            ->get();
    }

    public function getIncomeActivity($id)
    {
        return $this->findIncomeActivity($id);
    }

    public function deleteIncomeActivity($id)
    {
        $activity = $this->findIncomeActivity($id);

        $activity->delete();
    }

    public function approveIncomeActivity($id, $type)
    {
        $activity = $this->findIncomeActivity($id);
        $activity->approve = $type;
        $activity->save();
    }

    public function filterIncomeActivity($request)
    {
        $activities = $this->findIncomeActivities($request->organisation_id);
        if(!is_null($request->payment_item_id)){
            $activities = $activities->where('income_activities.payment_item_id', $request->payment_item_id);
        }
        if(!is_null($request->status)) {
            if($request->status != "ALL"){
                $activities = $activities->where('income_activities.approve', $request->status);
            }
        }
        if(!is_null($request->month)) {
            $activities = $activities ->WhereMonth('income_activities.date', $this->convertMonthNameToNumber($request->month));
        }
        $activities = $activities->orderBy('income_activities.name', 'ASC')->get();

        return $activities;
    }


    public function generateIncomeActivityPdf()
    {

    }

    private function findIncomeActivity($id)
    {
        return IncomeActivity::findOrFail($id);
    }

    private function findIncomeActivities($organisation_id)
    {
        return IncomeActivity::select('income_activities.*')
                            ->join('organisations', ['organisations.id' => 'income_activities.organisation_id'])
                            ->where('income_activities.organisation_id', $organisation_id);
    }

    public function calculateTotal($activities)
    {
        $total = 0;
        foreach($activities as $activity){
            $total += $activity->amount;
        }

        return $total;
    }

    private function getPaymentItemId($id){
        $item_id = null;
        if(!is_null($id)){
            $item = PaymentItem::findOrFail($id);
        }
        return $item->id;
    }
}

