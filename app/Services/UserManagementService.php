<?php

namespace App\Services;

use App\Constants\Constants;
use App\Constants\PaymentItemType;
use App\Constants\PaymentStatus;
use App\Constants\RegistrationStatus;
use App\Constants\Roles;
use App\Constants\SessionStatus;
use App\Exceptions\BusinessValidationException;
use App\Exceptions\EmailException;
use App\Exceptions\UnAuthorizedException;
use App\Http\Resources\MemberInviteNotification;
use App\Http\Resources\PasswordResetResponse;
use App\Http\Resources\TokenResource;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Imports\UsersImport;
use App\Interfaces\UserManagementInterface;
use App\Mail\InvitationMail;
use App\Mail\PasswordResetConfirmationMailable;
use App\Mail\PasswordResetMailable;
use App\Models\CustomRole;
use App\Models\MemberInvitation;
use App\Models\PasswordReset;
use App\Models\PaymentItem;
use App\Models\User;
use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Exception;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

use Maatwebsite\Excel\Facades\Excel;

class UserManagementService implements UserManagementInterface
{
    use ResponseTrait, HelpTrait;
    private RoleService $role_service;
    private SessionService  $session_service;

    public function __construct(RoleService $role_service, SessionService  $sessionService)
    {
        $this->role_service = $role_service;
        $this->session_service = $sessionService;
    }

    public function AddUserUserToOrganisation($request, $id)
    {
        $created =  User::create([
            'name'            => $request->name,
            'email'           => $request->email,
            'telephone'       => str_replace(" ", "", $request->telephone),
            'gender'          => $request->gender,
            'address'         => $request->address,
            'occupation'      => $request->occupation,
            'organisation_id' => $id,
            'updated_by'      => $request->user()->name,
            'status'          => SessionStatus::ACTIVE
        ]);

        $role = CustomRole::findByName(Roles::MEMBER, 'api');
        $this->saveUserRole($created, $role,  $request->user()->name);
    }

    public function sendInvitation($request)
    {
        $invitation_token = $this->generateSecurityToken(20);
        $auth_user = $request->user();
        $redirectLink = env('MEMBER_INVITATION_REDIRECT_LINK').$request->role."&key=".$invitation_token;
        $organisation_logo = $auth_user->organisation->logo;
        $year = Carbon::now()->year;
        try {
            Mail::to($request->user_email)->send(new InvitationMail($request->user_name, $request->user_email, $redirectLink,
                $organisation_logo, $auth_user->name, $auth_user->organisation->name, $request->role, $year));
            $assignedRole = $this->getAssignedRole($request->role);
            MemberInvitation::create([
                'user_id'               => $request->user_id,
                'expire_at'             => Carbon::now()->addHours(24),
                'has_seen_notification' => false,
                'role_id'               => $assignedRole->id,
                'invitation_token'      => $invitation_token
            ]);
        }catch (Exception $exception){
            throw new BusinessValidationException($exception->getMessage(), 404);
        }
    }

    public function getUsers($organisation_id)
    {
        return User::where('users.status', SessionStatus::ACTIVE)
               ->select('users.*')
               ->distinct()
               ->orderBy('name')->get();
    }

    private function getAllUsersNotRegistered($session_id){
        return  User::whereDoesntHave('registrations', function ($e) use ($session_id){
            return $e->where('session_id', $session_id);
        })->get()->toArray();
    }
    public function getUsersRegistrationStatus($session_id, $paymentStatus)
    {
        return User::whereHas('registrations', function ($e) use ($session_id, $paymentStatus){
                  return $e->where('session_id', $session_id)->where('approve', $paymentStatus);
        })->get()->toArray();
    }

    public function getTotalUsersByRegStatus($organisation_id, $session_id)
    {

        $approvedUsers = count($this->getUsersRegistrationStatus($session_id, PaymentStatus::APPROVED));
        $pendingUsers =  count($this->getUsersRegistrationStatus($session_id, PaymentStatus::PENDING));
        $declinedUsers = count($this->getUsersRegistrationStatus($session_id, PaymentStatus::DECLINED));
        $notRegistered = count($this->getAllUsersNotRegistered($session_id));
        $totalUsers = $approvedUsers + $pendingUsers + $declinedUsers + $notRegistered;

        return ["approvedUsers" => ($approvedUsers), "pendingUsers" => ($pendingUsers), "declinedUsers" => ($declinedUsers), "notRegistered" =>($notRegistered), "allUsers" => $totalUsers];
    }

    public function getRegMemberByMonths($organisation_id, $session_id)
    {
        $data = [];
        $payment_statuses = [PaymentStatus::APPROVED, PaymentStatus::PENDING, PaymentStatus::DECLINED];
        for ($month = 1; $month <= 12; $month++){
            $users_by_status = [];
            foreach ($payment_statuses as $payment_status){
                $users = User::join('member_registrations', 'users.id', '=', 'member_registrations.user_id')
                    ->where('member_registrations.session_id', $session_id)
                    ->where('member_registrations.approve', $payment_status)
                    ->whereMonth('member_registrations.created_at', $month)
                    ->select('users.*', 'member_registrations.approve', 'member_registrations.session_id')
                    ->distinct()->get()->toArray();

                $users_by_status[] = count($users);
            }
            $data[] = $users_by_status;
        }
        return $data;
    }

    public function getUser($user_id)
    {

        $user = User::leftJoin('member_registrations', 'users.id', '=', 'member_registrations.user_id')
                   ->where('users.id', $user_id)
                   ->select('users.*', 'member_registrations.approve')
                   ->get();
        return ($user[0]);
    }

    public function updateUser($user_id, $request)
    {
        $updated = User::findOrFail($user_id);
        $updated->update([
            'picture'         => $request->picture
        ]);
    }


    public function updateMemberProfile($request) {
        $user = User::findOrFail($request->user_id);
        $user->update([
            'name'          => $request->name,
            'email'         => $request->email,
            'address'       => $request->address,
            'occupation'    => $request->occupation,
            'gender'        => $request->gender,
            'status'        => $request->status,
        ]);
        return $user;
    }

    public function updateProfile($request) {
        $user = User::findOrFail($request->user_id);
        $user->update([
            'name'          => $request->name,
            'email'         => $request->email,
            'address'       => $request->address,
            'occupation'    => $request->occupation,
            'gender'        => $request->gender,
            'status'        => $request->status,
        ]);
        $updated = $user->refresh();
        $currentSession = $this->session_service->getCurrentSession();
        $token = $this->generateToken($user);

        return new TokenResource(new UserResource($updated,$token, true), $currentSession);
    }

    public function deleteUser($user_id)
    {
        return User::findOrFail($user_id)->delete();
    }

    public function loginUser($request)
    {

        $user = User::where('email', $request->email)->firstOrFail();
        if (!Hash::check($request->password, $user->password)) {
            throw new UnAuthorizedException('Bad Credentials', 403);
        } else {
            $this->validateIfUserCanLogin($user);
            $token = $this->generateToken($user);
            $hasLoginBefore = $this->checkIfUserHasLogin($user);
            $currentSession = $this->session_service->getCurrentSession();
            return new TokenResource(new UserResource($user, $token, $hasLoginBefore), $currentSession);
        }

    }

    public function createAccount($request)
    {
        return User::create([
            'name'       => $request->name,
            'telephone'  => str_replace(" ", "", $request->telephone),
            'password'   => Hash::make($request->password),
            'email'      => $request->email,
        ]);
    }

    public function setPassword($request)
    {

        $user = $this->checkUserExist($request);
        $user->password = Hash::make($request->password);
        $user->email_verified_at = Carbon::now()->toDateTimeString();
        $user->save();

        $this->role_service->addUserRole($user->id, $request->role, 'Default');

        $token = $this->generateToken($user);
        $hasLoginBefore = $this->checkIfUserHasLogin($user);
        $currentSession = $this->session_service->getCurrentSession();

        return new TokenResource(new UserResource($user, $token, $hasLoginBefore), $currentSession);
    }

    public function updatePassword($request)
    {
        $user = User::findOrFail($request->user_id);
        if(!Hash::check($request->old_password, $user->password)){
            throw new UnAuthorizedException("Old Password not match", 403);
        }
        $user->password = Hash::make($request->password);
        $user->save();
    }

    public function checkUserExist($request)
    {
        $user = User::where('email', $request->credential)->firstOrFail();
        $member_invitation = MemberInvitation::where('user_id', $user->id)->where('invitation_token', $request->invitation_token)->first();
        if(isset($member_invitation)){
            if(Carbon::now()->greaterThan($member_invitation->expire_at)){
                throw new BusinessValidationException("Member's invitation link has expired. Please request for a new one", 400);
            }
        }else {
            throw new UnAuthorizedException("Invalid Invitation Link", 403);
        }

        return $user;
    }

    public function importUsers($organisation_id, $request)
    {
        $memberRole = CustomRole::findByName(Roles::MEMBER, 'api');
        $updated_by = ($request->user()->name);
        return Excel::import(new UsersImport($organisation_id, $updated_by, $memberRole->id), $request->file('file'));
    }

    public function filterUsers($request)
    {

        $filter_users = User::where('organisation_id', $request->organisation_id);

        if(isset($request->has_register) && $request->has_register == RegistrationStatus::REGISTERED) {
            $filter_users = $filter_users->whereHas('registrations', function ($query) use ($request){
                $query->where('session_id', $request->year)->where('approve', PaymentStatus::APPROVED);
            });

        }
        if(isset($request->has_register) && $request->has_register == RegistrationStatus::NOT_REGISTERED){

            $filter_users = User::whereDoesntHave('registrations', function ($e) use ($request){
                return $e->where('session_id', $request->year);
            });

        }
        if(isset($request->has_register) && $request->has_register == PaymentStatus::PENDING){

            $filter_users = $filter_users->whereHas('registrations', function ($query) use ($request){
                $query->where('session_id', $request->year)
                    ->where('approve', PaymentStatus::PENDING);
            });

        }
        if(isset($request->has_register) && $request->has_register == PaymentStatus::DECLINED ){

            $filter_users = $filter_users->whereHas('registrations', function ($query) use ($request){
                $query->where('session_id', $request->year)
                    ->where('approve', PaymentStatus::DECLINED);
            });

        }
        if(isset($request->has_register) && $request->has_register == SessionStatus::ACTIVE ){

            $filter_users = $filter_users->where('status', SessionStatus::ACTIVE);

        }
        if(isset($request->has_register) && $request->has_register == SessionStatus::IN_ACTIVE){

            $filter_users = $filter_users->where('status', SessionStatus::IN_ACTIVE);

        }
        if(isset($request->gender) && $request->gender != Constants::ALL){

           $filter_users = $filter_users->where('gender', $request->gender);

        }
        if(isset($request->filter)){

            $filter_users = $filter_users->where('name','LIKE', '%'.$request->filter.'%');

        }
        $filter_users = $filter_users->distinct();

        $perPage = $request->input('per_page', 10);

        $currentPage = $request->input('page', 1);

        $filter_users =  $filter_users->orderBy('users.name')->paginate($perPage, ['*'], 'page', $currentPage);

        $total = $filter_users->total();

        $last_page = !is_null($request->per_page) ? $filter_users->lastPage(): 0;

        $per_page = !is_null($request->per_page) ? (int)$filter_users->perPage() : 0;

        $current_page = !is_null($request->per_page) ? $filter_users->currentPage() : 0;

        return new UserCollection($filter_users, $total, $last_page, $per_page, $current_page);
    }

    public function setPasswordResetToken($request)
    {
        $request->validate([
            'email'  => 'required|email'
        ]);
        $user = User::where('email', $request->email)->firstOrFail();
        $this->validateIfUserCanLogin($user);
        $token = $this->generateSecurityToken(7);
        $redirectLink = env('PASSWORD_RESET_UI_REDIRECT_LINK').$this->generateSecurityToken(20);
        $organisation_logo = $user->organisation->logo;
        $year = Carbon::now()->year;
        try {
            Mail::to($user['email'])->send(new PasswordResetMailable($user, $redirectLink, $organisation_logo, $year, $token));
            PasswordReset::create([
                'email' => $user->email,
                'token' => $token,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'user_id' => $user->id,
                'expire_at' => Carbon::now()->addMinutes(10)
            ]);
        }catch (Exception $exception){
            throw new EmailException("Could not send reset email link", 550);
        }
    }

    public function validateResetToken($request)
    {
        $request->validate([
            'token' => 'required|string'
        ]);
        $resetData = PasswordReset::where('token',$request->token)->first();
        if(isset($resetData)){
            if(Carbon::now()->greaterThan($resetData->expire_at)){
                throw new BusinessValidationException("Password Reset token has Expired", 400);
            }
        }else {
            throw new UnAuthorizedException("Invalid token", 403);
        }

        return new PasswordResetResponse($resetData->email, $resetData->user_id);
    }

    public function resetPassword($request)
    {
        $resetData = PasswordReset::where('token',$request->token)->first();
        if(isset($resetData)){
            if(Carbon::now()->greaterThan($resetData->expired_at)){
                throw new UnAuthorizedException("Password Reset token has Expired", 403);
            }
            $user = User::findOrFail($request->user_id);
            $this->validateIfUserCanLogin($user);
            $user->password = Hash::make($request->new_password);
            $user->save();

            $token = $this->generateToken($user);
            $currentSession = $this->session_service->getCurrentSession();
            $year = Carbon::now()->year;
            try {
                $organisation_logo = $user->organisation->logo;
                Mail::to($user['email'])->send(new PasswordResetConfirmationMailable($user, $organisation_logo, $year));
            }catch (Exception $exception){
                throw new EmailException("Could not send reset email link", 550);
            }
            return new TokenResource(new UserResource($user, $token, true), $currentSession);
        }else {
            throw new UnAuthorizedException("Invalid token", 403);
        }

    }

    public function getUserByPaymentItem($id, $request)
    {
        $paymentItem = PaymentItem::findOrFail($id);
        switch ($paymentItem->type){
            case PaymentItemType::ALL_MEMBERS:
                $paymentItemMembers = UserResource::collection($this->getUsers($request->organisation_id));
            break;
            default:
                $paymentItemMembers = $this->getReferenceResource($paymentItem->reference);
            break;
        }
        return $paymentItemMembers;
    }

    public function getAdminNotifications()
    {
        $notifications = DB::table('users')
            ->join('member_invitations', 'users.id', '=', 'member_invitations.user_id')
            ->join('roles', 'roles.id', '=', 'member_invitations.role_id')
            ->whereNotNull('users.email_verified_at')
            ->select('users.name', 'users.updated_at', 'roles.name as role_name', 'member_invitations.id', 'member_invitations.has_seen_notification')
            ->orderBy('member_invitations.created_at','DESC')
            ->get();

        return collect($notifications)->map(function ($notification){
            return new MemberInviteNotification($notification->id, $notification->name, $notification->role_name, $notification->updated_at, $notification->has_seen_notification);
        })->toArray();
    }

    public function markNotificationRead($id)
    {
        return MemberInvitation::findOrFail($id)->update([
            'has_seen_notification' => true
        ]);
    }
    public function markAllNotificationsAsRead($request)
    {
        foreach ($request->all() as $key => $value){
            MemberInvitation::findOrFail($value['id'])->update(['has_seen_notification' => true]);
        }
    }
    private function generateToken($user)
    {
        return !is_null($user) ? $user->createToken('access-token', $user->roles->toArray())->plainTextToken : "";
    }

    private function checkIfUserHasLogin($user)
    {
        return !is_null($user->password);
    }

    private function validateIfUserCanLogin($user)
    {
        if(empty(collect($user->roles)->whereIn('name', [Roles::TREASURER, Roles::FINANCIAL_SECRETARY,
            Roles::PRESIDENT, Roles::ADMIN])->toArray())){
            throw new UnAuthorizedException("User does not have any Administrator role", 403);
        }
        if($user->status === SessionStatus::IN_ACTIVE){
            throw new UnAuthorizedException("User's Account has been deactivated! Please contact the ADMIN or President", 401);
        }
    }

    private function getAssignedRole($role)
    {
        return CustomRole::findByName($role, 'api');
    }


}
