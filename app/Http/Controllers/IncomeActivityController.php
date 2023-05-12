<?php

namespace App\Http\Controllers;


use App\Constants\Roles;
use App\Http\Resources\IncomeActivityResource;
use App\Http\Requests\IncomeActivityRequest;
use App\Models\User;
use App\Services\IncomeActivityService;
use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class IncomeActivityController extends Controller
{

    use ResponseTrait, HelpTrait;

    private IncomeActivityService $income_activity_service;

    public function __construct(IncomeActivityService $income_activity_service)
    {
        $this->income_activity_service = $income_activity_service;
    }


    public function createIncomeActivity(IncomeActivityRequest $request, $id)
    {
        $this->income_activity_service->createIncomeActivity($request, $id);

        return $this->sendResponse('success', 'Income activity saved successfully');
    }

    public function updateIncomeActivity(IncomeActivityRequest $request,  $id)
    {
        $this->income_activity_service->updateIncomeActivity($request, $id);

        return $this->sendResponse('success', 'Income activity updated successfully');
    }


    public function getIncomeActivitiesByOrganisation($id)
    {
        $income_activities = $this->income_activity_service->getIncomeActivities($id);

        return $this->sendResponse(IncomeActivityResource::collection($income_activities), 200);
    }


    public function getIncomeActivity($id)
    {
        $income_activity = $this->income_activity_service->getIncomeActivity($id);

        return $this->sendResponse(new IncomeActivityResource($income_activity), 200);
    }


    public function deleteIncomeActivity($id)
    {
        $this->income_activity_service->deleteIncomeActivity($id);

        return $this->sendResponse('success', 'Income activity deleted successfully');
    }

    public function approveIncomeActivity($id, Request $request)
    {
        $this->income_activity_service->approveIncomeActivity($id, $request->type);

        return $this->sendResponse('success', 'Income Activity approved successfully');
    }


    public function filterIncomeActivity(Request $request)
    {
        $income_activities = $this->income_activity_service->filterIncomeActivity($request);

        return $this->sendResponse(IncomeActivityResource::collection($income_activities), 200);
    }

    public function prepareData(Request $request) {
        $activities      = $this->filterIncomeActivity($request);

        $activities      = json_decode(json_encode($activities))->original->data;
        return $activities;
    }

    //I will have to pass the parameters in the request and run the query to download the data
    public function generateIncomeActivityPDF(Request $request)
    {
        $auth_user         = auth()->user();
        $organisation      = User::find($auth_user['id'])->organisation;

        $income_activities = $this->prepareData($request);

        $total             = $this->income_activity_service->calculateTotal($income_activities);

        $president         = $this->getOrganisationAdministrators(Roles::PRESIDENT);

        $treasurer         = $this->getOrganisationAdministrators(Roles::TREASURER);

        $fin_sec           = $this->getOrganisationAdministrators(Roles::FINANCIAL_SECRETARY);


        $data = [
            'title'               => 'Transaction Report For Income Activity',
            'date'                => date('m/d/Y'),
            'organisation'        => $organisation,
            'income_activities'   => $income_activities,
            'total'               => $total,
            'president'           => $president,
            'treasurer'           => $treasurer,
            'fin_secretary'       => $fin_sec,
            'organisation_telephone'   => $this->setOrganisationTelephone($organisation->telephone),
        ];

        $pdf = PDF::loadView('IncomeActivities.IncomeActivities', $data);

        return $pdf->download('IncomeActivities.pdf');
    }
}
