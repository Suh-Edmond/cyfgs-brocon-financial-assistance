<?php

namespace App\Http\Controllers;

use App\Models\IncomeActivity;
use App\Services\IncomeActivityService;
use App\Http\Resources\IncomeActivityResource;
use App\Http\Requests\IncomeActivityRequest;

class IncomeActivityController extends Controller
{

    private $income_activity_service;

    public function __construct(IncomeActivityService $income_activity_service)
    {
        $this->income_activity_service = $income_activity_service;
    }


    public function createIncomeActivity(IncomeActivityRequest $request, $id)
    {
        $this->income_activity_service->createIncomeActivity($request, $id);

        return response()->json(['message' => 'success', 'status' => 'ok'], 201);
    }

    public function updateIncomeActivity(IncomeActivityRequest $request,  $id)
    {
        $this->income_activity_service->updateIncomeActivity($request, $id);

        return response()->json(['message' => 'success', 'status' => 'ok'], 202);
    }


    public function getIncomeActivitiesByOrganisation($id)
    {
        $income_activities = $this->income_activity_service->getIncomeActivities($id);

        return response()->json(['data' => IncomeActivityResource::collection($income_activities)], 200);
    }


    public function getIncomeActivity($id)
    {
        $income_activity = $this->income_activity_service->getIncomeActivity($id);

        return response()->json(['data' => new IncomeActivity($income_activity)], 200);
    }


    public function deleteIncomeActivity($id)
    {
        $this->income_activity_service->deleteIncomeActivity($id);

        return response()->json(['message' => 'success', 'status' => 'ok'], 204);
    }

    public function approveIncomeActivity($id)
    {
        $this->income_activity_service->approveIncomeActivity($id);

        return response()->json(['message' => 'success', 'status' => 'ok'], 204);
    }
}
