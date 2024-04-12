<?php

namespace App\Services;

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
use App\Mail\MemberInvitationMail;
use App\Mail\PasswordResetMail;
use App\Models\CustomRole;
use App\Models\MemberInvitation;
use App\Models\PasswordReset;
use App\Models\PaymentItem;
use App\Models\User;
use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
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
        $auth_user = $request->user();
        $redirectLink = env('MEMBER_INVITATION_REDIRECT_LINK').$request->role;
        $organisation_logo = env('FILE_DOWNLOAD_URL_PATH').$auth_user->organisation->logo;
        try {
            Mail::to($request->user_email)->send(new MemberInvitationMail($request->user_name, $request->user_email, $redirectLink,
                $organisation_logo, $auth_user->name, $auth_user->organisation->name, $request->role));
            $assignedRole = $this->getAssignedRole($request->role);
            MemberInvitation::create([
                'user_id'               => $request->user_id,
                'expire_at'             => Carbon::now()->addDays(7),
                'has_seen_notification' => false,
                'role_id'               => $assignedRole->id
            ]);
        }catch (Exception $exception){
            throw new BusinessValidationException("Could not send member's invitation email", 404);
        }
    }

    public function getUsers($organisation_id)
    {
        return User::join('organisations', 'organisations.id', '=', 'users.organisation_id')
               ->where('organisations.id', $organisation_id)
               ->where('users.status', SessionStatus::ACTIVE)
               ->select('users.*')
               ->distinct()
               ->orderBy('name')->get();
    }

    public function getUsersRegistrationStatus($organisation_id)
    {
        return User::join('organisations', 'organisations.id', '=', 'users.organisation_id')
            ->leftJoin('member_registrations', 'users.id', '=', 'member_registrations.user_id')
            ->where('organisations.id', $organisation_id)
            ->where('users.status', SessionStatus::ACTIVE)
            ->select('users.*', 'member_registrations.approve')
            ->distinct()
            ->orderBy('name')->get();
    }

    public function getTotalUsersByRegStatus($organisation_id)
    {

        $users = $this->getUsersRegistrationStatus($organisation_id);
        $group_users = collect($users)->groupBy('approve')->toArray();
        $total_approved_record = 0;
        $total_pending = 0;
        $total_declined = 0;
        $total_unregistered = 0;
         if(count($group_users) > 0){
             $total_approved_record  = isset($group_users['APPROVED']) ? count($group_users['APPROVED']) : 0;
             $total_pending = isset($group_users['PENDING']) ? count($group_users['PENDING']) : 0;
             $total_declined = isset($group_users['DECLINED']) ? count($group_users['DECLINED']): 0;
             $total_unregistered = isset($group_users['']) ? count($group_users['']): 0;
         }
        return [$total_approved_record, $total_pending, $total_declined, $total_unregistered];
    }

    public function getRegMemberByMonths($organisation_id)
    {
        $data = [];
        $payment_statuses = [PaymentStatus::APPROVED, PaymentStatus::PENDING, PaymentStatus::DECLINED];
        for ($month = 1; $month <= 12; $month++){
            $users_by_status = [];
            foreach ($payment_statuses as $payment_status){
                $users = User::join('organisations', 'organisations.id', '=', 'users.organisation_id')
                    ->join('member_registrations', 'users.id', '=', 'member_registrations.user_id')
                    ->where('organisations.id', $organisation_id)
                    ->where('member_registrations.approve', $payment_status)
                    ->whereMonth('member_registrations.created_at', $month)
                    ->select('users.*', 'member_registrations.approve', 'member_registrations.session_id')
                    ->distinct()
                    ->orderBy('name')->get()->toArray();
                array_push($users_by_status, count($users));
            }
            array_push($data, $users_by_status);
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

        $telephone = $request->telephone;
        $user = User::where('telephone', $telephone)->firstOrFail();
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
        $user->email_verified_at = Carbon::now()->setTimezone('Africa/Douala')->toDateTimeString();
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
        $user = User::where('telephone', str_replace(" ", "", $request->credential))->orWhere('email', $request->credential)->firstOrFail();
        $member_invitation = MemberInvitation::where('user_id', $user->id)->first();
        if(isset($member_invitation)){
            if(Carbon::now()->greaterThan($member_invitation->expire_at)){
                throw new UnAuthorizedException("Member's invitation link has expired. Please request for a nwe one", 403);
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
        $filter_users = User::join('organisations', 'organisations.id', '=', 'users.organisation_id')
            ->leftJoin('member_registrations', 'users.id', '=', 'member_registrations.user_id')
            ->where('organisations.id', $request->organisation_id);

        if(isset($request->year)) {
            $filter_users = $filter_users->where('member_registrations.session_id', $request->year);
        }
        if(isset($request->has_register) && $request->has_register == RegistrationStatus::REGISTERED && $request->has_register != "ALL"){
            $filter_users = $filter_users->where('member_registrations.approve', PaymentStatus::APPROVED);
        }
        if(isset($request->has_register) && $request->has_register == RegistrationStatus::NOT_REGISTERED && $request->has_register != "ALL"){
            $filter_users = $filter_users->whereNull('member_registrations.approve');
        }
        if(isset($request->has_register) && $request->has_register == PaymentStatus::PENDING && $request->has_register != "ALL"){
            $filter_users = $filter_users->where('member_registrations.approve', PaymentStatus::PENDING);
        }
        if(isset($request->has_register) && $request->has_register == PaymentStatus::DECLINED && $request->has_register != "ALL"){
            $filter_users = $filter_users->where('member_registrations.approve', PaymentStatus::DECLINED);
        }
        if(isset($request->has_register) && $request->has_register == SessionStatus::ACTIVE && $request->has_register != "ALL"){
            $filter_users = $filter_users->where('users.status', SessionStatus::ACTIVE);
        }
        if(isset($request->has_register) && $request->has_register == SessionStatus::IN_ACTIVE && $request->has_register != "ALL"){
            $filter_users = $filter_users->where('users.status', SessionStatus::IN_ACTIVE);
        }
        if(isset($request->gender) && $request->gender != "ALL"){
           $filter_users = $filter_users->where('users.gender', $request->gender);
        }
        if(isset($request->filter)){
            $filter_users = $filter_users->where('users.name','LIKE', '%'.$request->filter.'%');
        }
        $filter_users = $filter_users->select('users.*','member_registrations.approve','member_registrations.session_id')->distinct();

        $filter_users = !is_null($request->per_page) ? $filter_users->orderBy('users.name')->paginate($request->per_page): $filter_users->orderBy('users.name')->get();
        $total = !is_null($request->per_page) ? $filter_users->total() : count($filter_users);
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
        $token = md5(mt_rand());
        $redirectLink = env('PASSWORD_RESET_UI_REDIRECT_LINK')."?token=".$token;
        $organisation_logo = env('FILE_DOWNLOAD_URL_PATH').$user->organisation->logo;
        try {
            Mail::to($user['email'])->send(new PasswordResetMail($user, $redirectLink, $organisation_logo));
        }catch (Exception $exception){
            throw new EmailException("Could not send reset email link", 550);
        }
        PasswordReset::create([
            'email' => $user->email,
            'token' => $token,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'user_id' => $user->id,
            'expire_at' => Carbon::now()->addHours(4)
        ]);
    }

    public function validateResetToken($request)
    {
        $request->validate([
            'token' => 'required|string'
        ]);
        $resetData = PasswordReset::where('token',$request->token)->first();
        if(isset($resetData)){
            if(Carbon::now()->greaterThan($resetData->expire_at)){
                throw new UnAuthorizedException("Password Reset token has Expired", 403);
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
