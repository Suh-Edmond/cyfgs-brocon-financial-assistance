<?php

namespace App\Services;

use App\Constants\PaymentStatus;
use App\Constants\RegistrationStatus;
use App\Constants\Roles;
use App\Exceptions\BusinessValidationException;
use App\Http\Resources\UserResource;
use App\Imports\UsersImport;
use App\Interfaces\UserManagementInterface;
use App\Mail\PasswordResetMail;
use App\Models\CustomRole;
use App\Models\PasswordReset;
use App\Models\User;
use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class UserManagementService implements UserManagementInterface
{
    use ResponseTrait, HelpTrait;

    private RoleService $role_service;

    public function __construct(RoleService $role_service)
    {
        $this->role_service = $role_service;
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
            'updated_by'      => $request->user()->name
        ]);

        $role = CustomRole::findByName(Roles::MEMBER, 'api');
        $this->saveUserRole($created, $role,  $request->user()->name);
    }

    public function getUsers($organisation_id)
    {
          return User::join('organisations', 'organisations.id', '=', 'users.organisation_id')
                 ->leftJoin('member_registrations', 'users.id', '=', 'member_registrations.user_id')
                 ->where('organisations.id', $organisation_id)
                 ->select('users.*', 'member_registrations.approve', 'member_registrations.session_id')
                 ->distinct()
                 ->orderBy('name')->get();

    }

    public function getTotalUsersByRegStatus($organisation_id)
    {

        $users = $this->getUsers($organisation_id);
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
        return [$total_approved_record, $total_pending,$total_declined, $total_unregistered];
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
        ]);
        $updated = $user->refresh();
        $token = $this->generateToken($updated);
        return new UserResource($updated,$token, true);
    }


    public function deleteUser($user_id)
    {
        return User::findOrFail($user_id)->delete();
    }

    public function loginUser($request)
    {
        $telephone = str_replace(" ", "", $request->telephone);
        $user = User::where('telephone', $telephone)->firstOrFail();

        if (!Hash::check($request->password, $user->password)) {
            return $this->sendError('Unauthorized', 'Bad Credentials', 401);
        } else {
            $token = $this->generateToken($user);
            $hasLoginBefore = $this->checkIfUserHasLogin($user);

            return new UserResource($user, $token, $hasLoginBefore);
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
        $user->save();

        $token = $this->generateToken($user);
        $hasLoginBefore = $this->checkIfUserHasLogin($user);

        return new UserResource($user, $token, $hasLoginBefore);
    }

    public function updatePassword($request)
    {
        $user = User::findOrFail($request->user_id);
        if(!Hash::check($request->old_password, $user->password)){
            throw new BusinessValidationException("Old Password not match");
        }
        $user->password = Hash::make($request->password);
        $user->save();
    }


    public function checkUserExist($request)
    {
        return User::where('telephone', str_replace(" ", "", $request->credential))->orWhere('email', $request->credential)->firstOrFail();
    }

    public function importUsers($organisation_id, $request)
    {
        $memberRole = CustomRole::findByName(Roles::MEMBER, 'api');
        Excel::import(new UsersImport($organisation_id, $this->role_service, $request->user()->name, $memberRole), $request->file('file'));
    }

    public function filterUsers($request)
    {
        $filter_users = User::leftJoin('member_registrations', 'member_registrations.user_id', '=', 'users.id');
        if($request->has_register == RegistrationStatus::REGISTERED){
            $filter_users = $filter_users->where('member_registrations.approve', PaymentStatus::APPROVED);
        }
        if(!is_null($request->gender) && $request->gender != "ALL"){
           $filter_users = $filter_users->where('users.gender', $request->gender);
        }
        if(!is_null($request->session_id)) {
            $filter_users = $filter_users->where('member_registrations.session_id', $request->session_id);
        }
        $filter_users = $filter_users->select('users.*','member_registrations.approve');
        $filter_users = $filter_users->orderBy('users.name', 'DESC')->get();

        return $filter_users;
    }


    public function setPasswordResetToken($request)
    {
        $request->validate([
            'email'  => 'required|email'
        ]);
        $user = User::where('email', $request->email)->firstOrFail();
        $token = md5(mt_rand());
        $redirectLink = env('PASSWORD_RESET_UI_REDIRECT_LINK')."?token=".$token;
        $organisation_logo = env('FILE_DOWNLOAD_URL_PATH').$user->organisation->logo;
        try {
            Mail::to($user['email'])->send(new PasswordResetMail($user, $redirectLink, $organisation_logo));
        }catch (\Exception $exception){

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

    }

    public function setNewPassword($request)
    {

    }


    private function generateToken($user)
    {
        $token = "";
        if (!is_null($user)) {
            $token = $user->createToken('access-token', $user->roles->toArray())->plainTextToken;
        }

        return $token;
    }

    private function checkIfUserHasLogin($user)
    {
        $hasLoginBefore = false;
        if (!is_null($user->password)) {
            $hasLoginBefore = true;
        }

        return $hasLoginBefore;
    }


}
