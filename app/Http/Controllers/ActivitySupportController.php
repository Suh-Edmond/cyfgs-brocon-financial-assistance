<?php

namespace App\Http\Controllers;

use App\Constants\Roles;
use App\Http\Requests\CreateUpdateActivitySupportRequest;
use App\Http\Resources\ActivitySupportResource;
use App\Models\User;
use App\Services\ActivitySupportService;
use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ActivitySupportController extends Controller
{
    use ResponseTrait, HelpTrait;

    private ActivitySupportService  $activity_support_service;

    public function __construct(ActivitySupportService  $activity_support_service)
    {
        $this->activity_support_service = $activity_support_service;
    }

    public function fetchAll()
    {
        $data = $this->activity_support_service->fetchAllActivitySupport();

        return $this->sendResponse(ActivitySupportResource::collection($data), 200);
    }

    public function createActivitySupport(CreateUpdateActivitySupportRequest $request)
    {
        $this->activity_support_service->createActivitySupport($request);

        return $this->sendResponse('success', 201);
    }


    public function fetchActivitySupport($id)
    {
        $data = $this->activity_support_service->getActivitySupport($id);

        return $this->sendResponse(new ActivitySupportResource($data), 200);
    }


    public function updateActivitySupport(CreateUpdateActivitySupportRequest $request,  $id)
    {
        $this->activity_support_service->updateActivitySupport($id, $request);

        return $this->sendResponse('success', 204);

    }


    public function deleteActivitySupport($id)
    {
        $this->activity_support_service->deleteActivitySupport($id);

        return $this->sendResponse('success', 204);
    }

    public function filterActivitySupport(Request $request)
    {
        $data = $this->activity_support_service->filterActivitySupport($request);

        return $this->sendResponse(ActivitySupportResource::collection($data), 200);
    }

    public function getActivitySupportsByPaymentItem($id)
    {
        $data = $this->activity_support_service->getActivitySupportsByPaymentItem($id);

        return $this->sendResponse(ActivitySupportResource::collection($data), 200);
    }

    public function changeActivityState($id, Request $request)
    {
        $this->activity_support_service->changeActivityState($id, $request);

        return $this->sendResponse('success', 204);
    }

    private function prepareData(Request $request) {
        $sponsorships      = $this->filterActivitySupport($request);
        $sponsorships      = json_decode(json_encode($sponsorships))->original->data;

        return $sponsorships;
    }

    public function downloadActivitySupport(Request $request)
    {
        $auth_user         = auth()->user();

        $organisation      = User::find($auth_user['id'])->organisation;

        $supports          = $this->prepareData($request);

        $president         = $this->getOrganisationAdministrators(Roles::PRESIDENT);

        $treasurer         = $this->getOrganisationAdministrators(Roles::TREASURER);

        $fin_sec           = $this->getOrganisationAdministrators(Roles::FINANCIAL_SECRETARY);

        $data = [
            'title'               => 'Sponsorships for '.$supports[0]->payment_item->name,
            'date'                => date('m/d/Y'),
            'organisation'        => $organisation,
            'supports'            => $supports,
            'president'           => $president,
            'organisation_telephone'   => $this->setOrganisationTelephone($organisation->telephone),
            'treasurer'           => $treasurer,
            'fin_secretary'       => $fin_sec,
            'total'                => $this->computeTotalContribution($supports),
        ];
        $pdf = PDF::loadView('ActivitySupport.Support', $data);

        return $pdf->download('ActivitySupport.pdf');
    }
}
