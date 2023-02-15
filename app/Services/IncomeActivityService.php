<?php
namespace App\Services;

use App\Http\Resources\IncomeActivityCollection;
use App\Interfaces\IncomeActivityInterface;
use App\Models\IncomeActivity;
use App\Models\Organisation;
use App\Traits\HelpTrait;


class IncomeActivityService implements IncomeActivityInterface {

    use HelpTrait;

    public function createIncomeActivity($request, $id)
    {
        $organisation = Organisation::findOrFail($id);

        IncomeActivity::create([
            'name'              => $request->name,
            'description'       => $request->description,
            'date'              => $request->date,
            'amount'            => $request->amount,
            'venue'             => $request->venue,
            'organisation_id'   => $organisation->id,
            'updated_by'        =>$request->user()->name,
            'scan_picture'      => $request->scan_picture
        ]);
    }

    public function updateIncomeActivity($request, $id)
    {
        $activity = $this->findIncomeActivity($id);

        $activity->update([
            'name'              => $request->name,
            'description'       => $request->description,
            'date'              => $request->date,
            'amount'            => $request->amount,
            'venue'             => $request->venue,
        ]);
    }

    public function getIncomeActivities($organisation_id)
    {
        $activities = $this->findIncomeActivities($organisation_id)
                            ->orderBy('income_activities.name', 'ASC')
                            ->get();

        $total = $this->calculateTotal($activities);

        return new IncomeActivityCollection($activities, $total);
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

    public function filterIncomeActivity($organisation_id, $month, $year, $status)
    {
        $activities = $this->findIncomeActivities($organisation_id);
        if(!is_null($status)) {
            if($status != "ALL"){
                $activities = $activities->where('income_activities.approve', $status);
            }
        }
        if(!is_null($month)) {
            $activities = $activities ->WhereMonth('income_activities.date', $this->convertMonthNameToNumber($month));
        }
        if(!is_null($year)) {
            $activities = $activities->WhereYear('income_activities.date', $year);
        }
        $activities = $activities->orderBy('income_activities.name', 'ASC')->get();

        $total = $this->calculateTotal($activities);

        return new IncomeActivityCollection($activities, $total);
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
}

