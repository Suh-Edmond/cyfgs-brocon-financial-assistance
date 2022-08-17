<?php
namespace App\Services;

use App\Http\Resources\IncomeActivityCollection;
use App\Http\Resources\IncomeActivityResource;
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
        $total = 0.0;
        $activities = IncomeActivity::where('organisation_id', $organisation_id)->get();

        foreach($activities as $activity){
            $total += $activity->amount;
        }

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


    private function findIncomeActivity($id)
    {
        return IncomeActivity::findOrFail($id);
    }
}

