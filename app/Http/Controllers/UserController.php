<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;
use App\Interfaces\UserManagementInterface;

class UserController extends Controller
{
    private $user_management_service;

    public function __construct(UserManagementInterface $user_management_service)
    {
        $this->user_management_service = $user_management_service;
    }


    public function createUser(CreateUserRequest $request)
    {
        $this->user_management_service->createUser($request);

        return response()->json(['message' => 'success', 'status'=> '201'], 201);
    }

    public function logInUser(LoginRequest $request)
    {
        $userToken = $this->user_management_service->loginUser($request);

        return response()->json(['data' => $userToken, 'status' => '200'], 200);
    }

    public function getUsers()
    {
        $organisation_id = Auth::user()->organisation->id;
        $users = $this->user_management_service->getUsers($organisation_id);

        return response()->json(['data' => UserResource::collection($users)], 200);
    }



    public function getUser($user_id)
    {
       $user = $this->user_management_service->getUser($user_id);

       return response()->json(['data' => new UserResource($user)], 200);
    }


    public function updateUser(UpdateUserRequest $request, $id)
    {
       $this->user_management_service->updateUser($id, $request);

       return response()->json(['message' => 'success', 'status' => '204'], 204);
    }



    public function deleteUser($id)
    {
        $this->user_management_service->deleteUser($id);

        return response()->json(['message' => 'success', 'status' => '204'], 204);
    }

}
