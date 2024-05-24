<?php

namespace App\Http\Controllers;

 use App\Constants\Roles;
 use App\Http\Requests\MemberRegRequest;
use App\Http\Requests\SearchRegistrationRequest;
use App\Http\Resources\MemberRegistrationResource;
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


    public function addRegistration(MemberRegRequest $request)
    {
        $this->registrationService->addRegistration($request);

        return $this->sendResponse("success", "Member registered successfully");
    }


    public function updateRegistration(MemberRegRequest $request)
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

        $organisation      = $request->user()->organisation;

        $registrations     = $this->prepareData($request);

        $admins            = $this->getOrganisationAdministrators();
        $president         = $admins[Roles::PRESIDENT];
        $treasurer         = $admins[Roles::TREASURER];
        $fin_sec           = $admins[Roles::FINANCIAL_SECRETARY];

        $data = [
            'title'               => 'Registered Members for '.$request->year,
            'date'                => date('d/m/Y'),
            'organisation'        => $organisation,
            'members'             => $registrations,
            'president'           => $president,
            'organisation_telephone'   => $this->setOrganisationTelephone($organisation->telephone),
            'treasurer'           => $treasurer,
            'fin_secretary'       => $fin_sec,
            'organisation_logo'   => $organisation->logo
        ];
        $pdf = PDF::loadView('MemberRegistration.RegisteredMembers', $data);

        return $pdf->download('RegisteredMembers.pdf');
    }
}
