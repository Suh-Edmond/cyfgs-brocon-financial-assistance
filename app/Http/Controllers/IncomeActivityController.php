<?php

namespace App\Http\Controllers;


use App\Http\Resources\IncomeActivityResource;
use App\Http\Requests\IncomeActivityRequest;
use App\Services\IncomeActivityService;

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

        return $this->sendResponse('success', 'Income activity saved successfully', 201);
    }

    public function updateIncomeActivity(IncomeActivityRequest $request,  $id)
    {
        $this->income_activity_service->updateIncomeActivity($request, $id);

        return $this->sendResponse('success', 'Income activity updated successfully', 202);
    }


    public function getIncomeActivitiesByOrganisation($id)
    {
        $income_activities = $this->income_activity_service->getIncomeActivities($id);

        return $this->sendResponse($income_activities, 200);
    }


    public function getIncomeActivity($id)
    {
        $income_activity = $this->income_activity_service->getIncomeActivity($id);

        return $this->sendResponse(new IncomeActivityResource($income_activity), 200);
    }


    public function deleteIncomeActivity($id)
    {
        $this->income_activity_service->deleteIncomeActivity($id);

        return $this->sendResponse('success', 'Income activity deleted successfully', 204);
    }

    public function approveIncomeActivity($id)
    {
        $this->income_activity_service->approveIncomeActivity($id);

        return $this->sendResponse('success', 'Income Activity approved successfully', 204);
    }
}
