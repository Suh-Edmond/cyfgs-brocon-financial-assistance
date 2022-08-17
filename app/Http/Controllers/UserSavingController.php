<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserSavingRequest;
use App\Http\Resources\UserSavingResource;
use App\Services\UserServingService;

class UserSavingController extends Controller
{

    private $user_saving_service;

    public function __construct(UserServingService $user_saving_service)
    {
        $this->user_saving_service = $user_saving_service;
    }


    public function getUserSavings($user_id)
    {
        $user_savings = $this->user_saving_service->getUserSavings($user_id);

        return $this->sendResponse(UserSavingResource::collection($user_savings), 200);
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


    public function updateUserSaving(UserSavingRequest $request, $user_id, $id)
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

        return $this->sendResponse(UserSavingResource::collection($savings), 200);
    }
}
