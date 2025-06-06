<?php

namespace App\Http\Controllers;

use App\Constants\PaymentStatus;
use App\Constants\RegistrationStatus;
use App\Constants\Roles;
use App\Http\Requests\CheckUserRequest;
use App\Http\Requests\CreateAccountRequest;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\PasswordResetRequest;
use App\Http\Requests\SendInvitationRequest;
use App\Http\Requests\SetPasswordRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
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
        $this->role_service->addUserRole($new_user->id, Roles::ADMIN, $new_user->name);

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

        return $this->sendResponse(200, 'success');
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
        $users = $this->user_management_service->getTotalUsersByRegStatus($request->organisation_id, $request->session_id);
        return $this->sendResponse($users, 200);
    }

    public function getRegMemberByMonths(Request $request)
    {
        $usersByMonth = $this->user_management_service->getRegMemberByMonths($request->organisation_id, $request->session_id);

        $usersByRegStatus = $this->user_management_service->getTotalUsersByRegStatus($request->organisation_id, $request->session_id);

        return $this->sendResponse(["usersByMonth" => $usersByMonth, "usersByRegStatus" => $usersByRegStatus], 200);
    }

    public function getUser($user_id)
    {
        $user = $this->user_management_service->getUser($user_id);

        return $this->sendResponse(new UserResource($user, "", false), 200);
    }


    public function updateUser(UpdateUserRequest $request, $id)
    {
        $this->user_management_service->updateUser($id, $request);

        return $this->sendResponse("success", 'Account updated sucessfully');
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

        $organisation      = $request->user()->organisation;
        $users             = $this->user_management_service->filterUsers($request);
        $admins            = $this->getOrganisationAdministrators();
        $president         = $admins[Roles::PRESIDENT];
        $treasurer         = $admins[Roles::TREASURER];
        $fin_sec           = $admins[Roles::FINANCIAL_SECRETARY];

        $data = [
            'title'                  => $this->setTitle($request),
            'date'                   => date('d/m/Y'),
            'organisation'           => $organisation,
            'organisation_telephone' => $this->setOrganisationTelephone($organisation->telephone),
            'users'                  => $users,
            'president'              => $president,
            'treasurer'              => $treasurer,
            'fin_secretary'          => $fin_sec,
            'organisation_logo'      => $organisation->logo,
        ];

        $pdf = PDF::loadView('User.Users', $data)->setPaper('a4', 'portrait');
        $pdf->output();
        $domPdf = $pdf->getDomPDF();
        $canvas = $domPdf->getCanvas();
        $canvas->page_text(10, $canvas->get_height() - 20, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, [0, 0, 0]);
        return $pdf->download('Organisation_Users.pdf');
    }


    public function filterUsers(Request $request)
    {
        $users = $this->user_management_service->filterUsers($request);

        return $this->sendResponse(($users), 'success');
    }

    public function updateProfile(UpdateProfileRequest $request){
        $user = $this->user_management_service->updateProfile($request);

        return $this->sendResponse($user, 'success');
    }

    public function updateMemberProfile(UpdateProfileRequest $request){
        $user = $this->user_management_service->updateMemberProfile($request);

        return $this->sendResponse($user, 'success');
    }

    public function setPasswordResetToken(Request $request){
        $this->user_management_service->setPasswordResetToken($request);

        return $this->sendResponse("Password Reset Token sent successfully", 'success');
    }

    public function validateResetToken(Request $request)
    {
        $data = $this->user_management_service->validateResetToken($request);

        return $this->sendResponse($data, 'success');
    }

    public function resetPassword(PasswordResetRequest $request)
    {
        $data = $this->user_management_service->resetPassword($request);

        return $this->sendResponse($data, 'success');
    }

    public function getUserByPaymentItem($id, Request $request)
    {
        $data = $this->user_management_service->getUserByPaymentItem($id, $request);
        return $this->sendResponse($data, 'success');
    }

    public function sendInvitation(SendInvitationRequest $request){
        $this->user_management_service->sendInvitation($request);

        return $this->sendResponse("Invitation sent successfully", 'success');
    }

    public function getInvitationNotifications(){
        $data = $this->user_management_service->getAdminNotifications();

        return $this->sendResponse($data, 'success');
    }

    public function markNotificationRead($id)
    {
        $this->user_management_service->markNotificationRead($id);

        return $this->sendResponse("Notification mark as read","success");
    }

    public function markAllNotificationsAsRead(Request  $request){
        $this->user_management_service->markAllNotificationsAsRead($request);

        return $this->sendResponse("All notification mark as read","success");
    }
    private function setTitle(Request $request): string
    {
        $title = "";
        if(isset($request->has_register)){
            switch ($request->has_register){
                case RegistrationStatus::REGISTERED:
                    $title = 'Registered Organisation Members';
                break;
                case RegistrationStatus::NOT_REGISTERED:
                    $title = 'Non Registered Organisation Members';
                break;
                case PaymentStatus::PENDING:
                    $title = "Pending Registration";
                break;
                case PaymentStatus::DECLINED:
                    $title = "Declined Registrations";
            }
        }else {
            $title = "Organisation Members";
        }

        return $title;
    }

}
