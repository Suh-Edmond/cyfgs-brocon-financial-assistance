<?php

namespace App\Http\Controllers;

use App\Constants\RegistrationStatus;
use App\Constants\Roles;
use App\Http\Requests\CheckUserRequest;
use App\Http\Requests\CreateAccountRequest;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\SetPasswordRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Services\RoleService;
use App\Services\UserManagementService;
use App\Traits\HelpTrait;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class UserController extends Controller
{
    use HelpTrait;

    private UserManagementService $user_management_service;
    private RoleService $role_service;

    public function __construct(UserManagementService $user_management_service, RoleService $role_service)
    {
        $this->user_management_service = $user_management_service;
        $this->role_service            = $role_service;
    }

    public function createAccount(CreateAccountRequest $request)
    {
        $new_user = $this->user_management_service->createAccount($request);
        $this->role_service->addUserRole($new_user->id, Roles::MEMBER, $new_user->name);
        $this->role_service->addUserRole($new_user->id, Roles::PRESIDENT, $new_user->name);

        return $this->sendResponse("success", "Account created successfully");
    }


    public function addUser(CreateUserRequest $request, $id)
    {
        $this->user_management_service->AddUserUserToOrganisation($request, $id);

        return $this->sendResponse('success', 'User added successfully');
    }

    public function logInUser(LoginRequest $request)
    {
        return $this->user_management_service->loginUser($request);
    }


    public function setPassword(SetPasswordRequest $request)
    {
        $data = $this->user_management_service->setPassword($request);

        return $this->sendResponse($data, 'success');
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $this->user_management_service->updatePassword($request);

        return $this->sendResponse(200, 'success');;
    }


    public function checkUserExist(CheckUserRequest $request)
    {
        $data = $this->user_management_service->checkUserExist($request);

        return $this->sendResponse($data, 200);
    }

    public function getUsers($id)
    {
        $users = $this->user_management_service->getUsers($id);
        return $this->sendResponse(UserResource::collection($users), 'success');
    }

    public function getTotalUsersByRegStatus(Request $request)
    {
        $users = $this->user_management_service->getTotalUsersByRegStatus($request->organisation_id);
        return $this->sendResponse($users, 200);
    }

    public function getRegMemberByMonths(Request $request)
    {
        $users = $this->user_management_service->getRegMemberByMonths($request->organisation_id);

        return $this->sendResponse($users, 200);
    }

    public function getUser($user_id)
    {
        $user = $this->user_management_service->getUser($user_id);

        return $this->sendResponse(new UserResource($user, "", false), 200);
    }


    public function updateUser(UpdateUserRequest $request, $id)
    {
        $this->user_management_service->updateUser($id, $request);

        return $this->sendResponse('success', 'Account updated sucessfully');
    }



    public function deleteUser($id)
    {
        $this->user_management_service->deleteUser($id);

        return $this->sendResponse('success', 'User successfully been removed');
    }


    public function importUsers(Request $request, $id)
    {
        $this->user_management_service->importUsers($id, $request);

        return $this->sendResponse('success', 'Users successfully imported');
    }

    public function downloadUsers(Request $request)
    {

        $organisation      =$request->user()->organisation;
        $organisation_logo = (preg_replace('~^"?(.*?)"?$~', '$1', env('FILE_DOWNLOAD_URL_PATH').$organisation->logo));
        $users             = $this->user_management_service->filterUsers($request);
        $admins            = $this->getOrganisationAdministrators();
        $president         = $admins[0];
        $treasurer         = $admins[2];
        $fin_sec           = $admins[1];

        $data = [
            'title'                      => $request->has_register == RegistrationStatus::REGISTERED ? 'Registered Organisation Members':'Non Registered Organisation Members',
            'date'                   => date('m/d/Y'),
            'organisation'           => $organisation,
            'organisation_telephone' => $this->setOrganisationTelephone($organisation->telephone),
            'users'                  => $users,
            'president'              => $president,
            'treasurer'              => $treasurer,
            'fin_secretary'          => $fin_sec,
            'organisation_logo'      => $organisation_logo
        ];


        $pdf = PDF::loadView('User.Users', $data);

        return $pdf->download('Organisation_Users.pdf');
    }


    public function filterUsers(Request $request)
    {
        $users = $this->user_management_service->filterUsers($request);

        return $this->sendResponse(UserResource::collection($users), 'success');
    }

    public function updateProfile(UpdateProfileRequest $request){
        $user = $this->user_management_service->updateProfile($request);

        return $this->sendResponse($user, 'success');
    }
}
