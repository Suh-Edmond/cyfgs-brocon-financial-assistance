<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckUserRequest;
use App\Http\Requests\CreateAccountRequest;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Services\UserManagementService;

class UserController extends Controller
{
    private $user_management_service;

    public function __construct(UserManagementService $user_management_service)
    {
        $this->user_management_service = $user_management_service;
    }

    public function createAccount(CreateAccountRequest $request)
    {
        $this->user_management_service->createAccount($request);

        return $this->sendResponse("success", "Account created successfully");
    }


    public function addUser(CreateUserRequest $request, $id)
    {
        $this->user_management_service->AddUserUserToOrganisation($request, $id);

        return $this->sendResponse('success', 'User added successfully', 201);
    }

    public function logInUser(LoginRequest $request)
    {
        $data = $this->user_management_service->loginUser($request);

        return $this->sendResponse($data, 'success');
    }


    public function updatePassword(UpdatePasswordRequest $request)
    {
        $data = $this->user_management_service->setPassword($request);

        return $this->sendResponse($data, 'success', 200);
    }


    public function checkUserExist(CheckUserRequest $request)
    {
        $data = $this->user_management_service->checkUserExist($request);

        return $this->sendResponse($data, 'success', 200);
    }

    public function getUsers($id)
    {
        $users = $this->user_management_service->getUsers($id);

        return $this->sendResponse(UserResource::collection($users), 'success');
    }


    public function getUser($user_id)
    {
       $user = $this->user_management_service->getUser($user_id);

       return $this->sendResponse(new UserResource($user, "", false), 200);
    }


    public function updateUser(UpdateUserRequest $request, $id)
    {
       $this->user_management_service->updateUser($id, $request);

       return $this->sendResponse('success', 'Account updated sucessfully', 204);
    }



    public function deleteUser($id)
    {
        $this->user_management_service->deleteUser($id);

        return $this->sendResponse('success', 'User successfully been removed', 204);
    }

}
