<?php

namespace App\Http\Controllers;

use App\Constants\Roles;
use App\Http\Requests\UserSavingRequest;
use App\Http\Requests\UpdateUserSavingRequest;
use App\Http\Resources\UserSavingResource;
use App\Models\User;
use App\Services\UsersavingService;
use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use function PHPUnit\Framework\isEmpty;

class UserSavingController extends Controller
{

    use ResponseTrait, HelpTrait;

    private UsersavingService $user_saving_service;

    public function __construct(UsersavingService $user_saving_service)
    {
        $this->user_saving_service = $user_saving_service;
    }



    public function getUserSavings($user_id)
    {
        $user_savings = $this->user_saving_service->getUserSavings($user_id);

        return $this->sendResponse($user_savings, 200);
    }


    public function createUserSaving(UserSavingRequest $request)
    {
        $this->user_saving_service->createUserSaving($request);

        return $this->sendResponse('success', 'User saving saved successfully');
    }


    public function getUserSaving($user_id, $id)
    {
        $user_saving = $this->user_saving_service->getUserSaving($id, $user_id);

        return $this->sendResponse(new UserSavingResource($user_saving), 200);
    }


    public function updateUserSaving(UpdateUserSavingRequest $request, $user_id, $id)
    {
        $this->user_saving_service->updateUserSaving($request, $id, $user_id);

        return $this->sendResponse('success', 'User saving updated successfully');
    }


    public function deleteUserSaving($user_id, $id)
    {
        $this->user_saving_service->deleteUserSaving($id, $user_id);

        return $this->sendResponse('success', 'User saving deleted sucessfully');
    }


    public function approveUserSaving($id, Request $request)
    {
        $this->user_saving_service->approveUserSaving($id, $request->type);

        return $this->sendResponse('success', 'User saving approved sucessfully');
    }


    public function getAllUserSavingsByOrganisation($id)
    {
        $savings = $this->user_saving_service->getAllUserSavingsByOrganisation($id);

        return $this->sendResponse($savings, 200);
    }


    public function getUserSavingsByStatusAndOrganisation($id, Request $request)
    {

        $savings = $this->user_saving_service->findUserSavingByStatus($request->status, $id);

        return $this->sendResponse($savings, 200);
    }

    public function getMembersSavingsByName(Request $request){
        $savings = $this->user_saving_service->getMembersSavingsByName($request);

        return $this->sendResponse($savings, 200);
    }


    public function filterSavings(Request $request)
    {
        $savings = $this->user_saving_service->filterSavings($request);

        return $this->sendResponse(UserSavingResource::collection($savings), 200);
    }

    public function download(Request $request)
    {

        $auth_user         = auth()->user();

        $organisation      = User::find($auth_user['id'])->organisation;

        //user whose savings are been downloaded
        $user              = User::find($request->user_id);

        $savings           = $this->user_saving_service->getUserSavingsForDownload($request);

        $total             = $this->user_saving_service->calculateTotalSaving($savings);

        $president         = $this->getOrganisationAdministrators(Roles::PRESIDENT);

        $treasurer         = $this->getOrganisationAdministrators(Roles::TREASURER);

        $fin_sec           = $this->getOrganisationAdministrators(Roles::FINANCIAL_SECRETARY);


        $data = [
            'title'               => $user->name.' Savings',
            'date'                => date('m/d/Y'),
            'organisation'        => $organisation,
            'user_savings'        => $savings,
            'total'               => $total,
            'president'           => $president,
            'organisation_telephone'   => $this->setOrganisationTelephone($organisation->telephone),
            'treasurer'           => $treasurer,
            'fin_secretary'       => $fin_sec
        ];
        $pdf = PDF::loadView('UserSaving.Usersaving', $data);

        return $pdf->download('User_Saving.pdf');
    }

    public function downloadOrganisationSavings(Request  $request)
    {

        $auth_user         = auth()->user();

        $organisation      = User::find($auth_user['id'])->organisation;

        $savings = $this->user_saving_service->getOrganisationSavingsForDownload($request->organisation_id);

        $total = $this->user_saving_service->calculateOrganisationTotalSavings($savings);

        $president         = $this->getOrganisationAdministrators(Roles::PRESIDENT);

        $treasurer         = $this->getOrganisationAdministrators(Roles::TREASURER);

        $fin_sec           = $this->getOrganisationAdministrators(Roles::FINANCIAL_SECRETARY);


        $data = [
            'title'               => 'Organisation Savings',
            'date'                => date('m/d/Y'),
            'organisation'        => $organisation,
            'user_savings'        => $savings,
            'total'               => $total,
            'president'           => $president,
            'organisation_telephone'   => $this->setOrganisationTelephone($organisation->telephone),
            'treasurer'           => $treasurer,
            'fin_secretary'       => $fin_sec
        ];
        $pdf = PDF::loadView('UserSaving.OrganisationSavings', $data);

        return $pdf->download('Organisation_Saving.pdf');
    }
}
