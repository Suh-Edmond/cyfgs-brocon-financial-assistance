<?php

namespace App\Http\Controllers;

use App\Constants\Roles;
use App\Http\Requests\ChangeRegistrationStateRequest;
use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\SearchRegistrationRequest;
use App\Http\Resources\MemberRegistrationResource;
use App\Models\User;
use App\Services\RegistrationService;
use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class MemberRegistrationController extends Controller
{
    use ResponseTrait, HelpTrait;

    private RegistrationService  $registrationService;

    public function __construct(RegistrationService $registrationService)
    {
        $this->registrationService = $registrationService;
    }

    public function getRegistrations(SearchRegistrationRequest $request)
    {
        $data = $this->registrationService->getRegistrations($request);

        return $this->sendResponse(MemberRegistrationResource::collection($data), 200);
    }


    public function addRegistration(RegistrationRequest $request)
    {
        $this->registrationService->addRegistration($request);

        return $this->sendResponse("success", "Member registered successfully");
    }


    public function updateRegistration(RegistrationRequest $request)
    {
        $this->registrationService->updatedRegistration($request);
        return $this->sendResponse("success", "Member updated successfully");
    }


    public function deleteRegistration($id)
    {
        $this->registrationService->deleteRegistration($id);
        return $this->sendResponse("success", "Member deleted successfully");
    }

    public function approveRegisteredMember(Request $request)
    {
        $this->registrationService->approveRegisteredMember($request);
        return $this->sendResponse("success", "Member approved successfully");
    }


    private function prepareData(SearchRegistrationRequest $request) {
        $registrations      = $this->getRegistrations($request);
        $registrations      = json_decode(json_encode($registrations))->original->data;

        return $registrations;
    }


    public function downloadRegisteredMembers(SearchRegistrationRequest $request)
    {
        $auth_user         = auth()->user();

        $organisation      = User::find($auth_user['id'])->organisation;

        $registrations     = $this->prepareData($request);

        $president         = $this->getOrganisationAdministrators(Roles::PRESIDENT);

        $treasurer         = $this->getOrganisationAdministrators(Roles::TREASURER);

        $fin_sec           = $this->getOrganisationAdministrators(Roles::FINANCIAL_SECRETARY);

        $data = [
            'title'               => 'Registered Members for '.$request->year,
            'date'                => date('m/d/Y'),
            'organisation'        => $organisation,
            'members'             => $registrations,
            'president'           => $president,
            'organisation_telephone'   => $this->setOrganisationTelephone($organisation->telephone),
            'treasurer'           => $treasurer,
            'fin_secretary'       => $fin_sec,
        ];
        $pdf = PDF::loadView('MemberRegistration.RegisteredMembers', $data);

        return $pdf->download('RegisteredMembers.pdf');
    }
}
