<?php

namespace App\Http\Controllers;


use App\Http\Resources\IncomeActivityResource;
use App\Http\Requests\IncomeActivityRequest;
use App\Services\IncomeActivityService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use PDF;

class IncomeActivityController extends Controller
{

    use ResponseTrait;

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


    public function filterIncomeActivity(Request $request)
    {
        $income_activities = $this->income_activity_service->filterIncomeActivity($request->organisation_id, $request->month, $request->year, $request->status);

        return $this->sendResponse($income_activities, 200);
    }

    //I will have to pass the parameters in the request and run the query to download the data
    public function generateIncomeActivityPDF()
    {
        $organisation      = auth()->user()->organisation;
        $income_activities = $this->income_activity_service->getIncomeActivities(1);
        $total             = $this->income_activity_service->calculateTotal($income_activities);
        $administrators    = ResponseTrait::getOrganisationAdministrators($organisation->users);
        $president         = $administrators[0];
        $treasurer         = $administrators[1];
        $fin_sec           = $administrators[2];


        $data = [
            'title'               => 'Transaction Report For Income Activity',
            'date'                => date('m/d/Y'),
            'organisation'        => $organisation,
            'income_activities'   => $income_activities,
            'total'               => $total,
            'president'         => $president,
            'treasurer'         => $treasurer,
            'fin_secretary'     => $fin_sec
        ];

        $pdf = PDF::loadView('IncomeActivities.IncomeActivities', $data);

        return $pdf->download('IncomeActivities.pdf');
    }
}
