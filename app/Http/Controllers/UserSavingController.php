<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserSavingRequest;
use App\Http\Requests\UpdateUserSavingRequest;
use App\Http\Resources\UserSavingResource;
use App\Services\UserSavingService;
use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class UserSavingController extends Controller
{

    use ResponseTrait, HelpTrait;

    private UserSavingService $user_saving_service;

    public function __construct(UserSavingService $user_saving_service)
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


    public function getAllUserSavingsByOrganisation(Request $request)
    {
        $savings = $this->user_saving_service->getAllUserSavingsByOrganisation($request);

        return $this->sendResponse($savings, 200);
    }


    public function getUserSavingsByStatusAndOrganisation($id, Request $request)
    {

        $savings = $this->user_saving_service->findUserSavingByStatus($request->status, $id);

        return $this->sendResponse($savings, 200);
    }

    public function getMembersSavingsByOrganisation(Request $request){
        $savings = $this->user_saving_service->getMembersSavingsByOrganisation($request);

        return $this->sendResponse($savings, 200);
    }


    public function filterSavings(Request $request)
    {
        $savings = $this->user_saving_service->filterSavings($request);

        return $this->sendResponse(UserSavingResource::collection($savings), 200);
    }

    public function download(Request $request)
    {
        $organisation      = $request->user()->organisation;

        $savings           = $this->user_saving_service->getUserSavingsForDownload($request);

        $admins            = $this->getOrganisationAdministrators();
        $president         = $admins[0];
        $treasurer         = $admins[2];
        $fin_sec           = $admins[1];


        $data = [
            'title'               => $savings[0]->name.' Savings',
            'date'                => date('m/d/Y'),
            'organisation'        => $organisation,
            'user_savings'        => $savings,
            'total'               => $savings[0]->total_amount_deposited,
            'president'           => $president,
            'organisation_telephone'   => $this->setOrganisationTelephone($organisation->telephone),
            'treasurer'           => $treasurer,
            'fin_secretary'       => $fin_sec,
            'organisation_logo'   => env('FILE_DOWNLOAD_URL_PATH').$organisation->logo
        ];
        $pdf = PDF::loadView('UserSaving.Usersaving', $data);

        return $pdf->download('User_Saving.pdf');
    }

    public function downloadOrganisationSavings(Request  $request)
    {
        $organisation      = $request->user()->organisation;

        $savings = $this->user_saving_service->getOrganisationSavingsForDownload($request);

        $total = $this->user_saving_service->calculateOrganisationTotalSavings($savings);

        $admins            = $this->getOrganisationAdministrators();
        $president         = $admins[0];
        $treasurer         = $admins[2];
        $fin_sec           = $admins[1];

        $data = [
            'title'               => 'Organisation Savings',
            'date'                => date('m/d/Y'),
            'organisation'        => $organisation,
            'user_savings'        => $savings,
            'total'               => $total,
            'president'           => $president,
            'organisation_telephone'   => $this->setOrganisationTelephone($organisation->telephone),
            'treasurer'           => $treasurer,
            'fin_secretary'       => $fin_sec,
            'organisation_logo'   => env('FILE_DOWNLOAD_URL_PATH').$organisation->logo
        ];
        $pdf = PDF::loadView('UserSaving.OrganisationSavings', $data);

        return $pdf->download('Organisation_Saving.pdf');
    }

    public function getSavingsStatistics(Request $request)
    {
        $data = $this->user_saving_service->getSavingsStatistics($request);
        return $this->sendResponse($data, 200);
    }

}
