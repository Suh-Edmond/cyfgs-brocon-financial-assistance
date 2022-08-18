<?php
namespace App\Services;

use App\Http\Resources\IncomeActivityCollection;
use App\Interfaces\IncomeActivityInterface;
use App\Models\IncomeActivity;
use App\Models\Organisation;

class IncomeActivityService implements IncomeActivityInterface {

    public function createIncomeActivity($request, $id)
    {
        $organisation = Organisation::findOrFail($id);
        IncomeActivity::create([
            'name'              => $request->name,
            'description'       => $request->description,
            'date'              => $request->date,
            'amount'            => $request->amount,
            'venue'             => $request->venue,
            'organisation_id'   => $organisation->id
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
        $activity = $this->findIncomeActivity($id);

        return $activity;
    }

    public function deleteIncomeActivity($id)
    {
        $activity = $this->findIncomeActivity($id);

        $activity->delete();
    }

    public function approveIncomeActivity($id)
    {
        $activity = $this->findIncomeActivity($id);
        $activity->approve = 1;
        $activity->save();
    }

    public function filterIncomeActivity($organisation_id, $month, $year, $status)
    {
        $activities = $this->findIncomeActivities($organisation_id)
                            ->where('income_activities.approve', $status)
                            ->WhereMonth('income_activities.date', $month)
                            ->WhereYear('income_activities.date', $year)
                            ->orderBy('income_activities.name', 'ASC')
                            ->get();

        $total = $this->calculateTotal($activities);

        return new IncomeActivityCollection($activities, $total);
    }


    private function findIncomeActivity($id)
    {
        return IncomeActivity::findOrFail($id);
    }

    private function findIncomeActivities($organisation_id)
    {
        $income_activities = IncomeActivity::select('income_activities.*')
                            ->join('organisations', ['organisations.id' => 'income_activities.organisation_id'])
                            ->where('income_activities.organisation_id', $organisation_id);

        return $income_activities;
    }

    private function calculateTotal($activities)
    {
        $total = 0;
        foreach($activities as $activity){
            $total += $activity->amount;
        }

        return $total;
    }
}

