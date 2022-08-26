<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserSavingRequest;
use App\Http\Requests\UpdateUserSavingRequest;
use App\Http\Resources\UserSavingResource;
use App\Services\UsersavingService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use PDF;
class UserSavingController extends Controller
{

    use ResponseTrait;

    private $user_saving_service;

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

        return $this->sendResponse('success', 'User saving saved successfully', 201);
    }


    public function getUserSaving($user_id, $id)
    {
        $user_saving = $this->user_saving_service->getUserSaving($id, $user_id);

        return $this->sendResponse(new UserSavingResource($user_saving), 200);
    }


    public function updateUserSaving(UpdateUserSavingRequest $request, $user_id, $id)
    {
        $this->user_saving_service->updateUserSaving($request, $id, $user_id);

        return $this->sendResponse('success', 'User saving updated successfully', 204);
    }


    public function deleteUserSaving($user_id, $id)
    {
        $this->user_saving_service->deleteUserSaving($id, $user_id);

        return $this->sendResponse('success', 'User saving deleted sucessfully', 204);
    }


    public function approveUserSaving($id)
    {
        $this->user_saving_service->approveUserSaving($id);

        return $this->sendResponse('success', 'User saving approved sucessfully', 204);
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

    public function downloadUserSaving(Request $request)
    {

        $organisation      = auth()->user()->organisation;
        $savings           = $this->user_saving_service->findOrganisationUserSavings($request->organisation_id);
        $total             = $this->user_saving_service->calculateTotalSaving($savings);
        $administrators    = ResponseTrait::getOrganisationAdministrators($organisation->users);
        $president         = $administrators[0];
        $treasurer         = $administrators[1];
        $fin_sec           = $administrators[2];


        $data = [
            'title'               => 'User Savings for 2022',
            'date'                => date('m/d/Y'),
            'organisation'        => $organisation,
            'user_savings'        => $savings,
            'total'               => $total,
            'president'           => $president,
            'treasurer'           => $treasurer,
            'fin_secretary'       => $fin_sec
        ];
        // return ($user_savings);
        $pdf = PDF::loadView('UserSaving.Usersaving', $data);

        return $pdf->download('User_Saving.pdf');
    }
}
