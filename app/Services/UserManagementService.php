<?php

namespace App\Services;

use App\Constants\PaymentStatus;
use App\Constants\RegistrationStatus;
use App\Constants\Roles;
use App\Exceptions\BusinessValidationException;
use App\Http\Resources\UserResource;
use App\Imports\UsersImport;
use App\Interfaces\UserManagementInterface;
use App\Models\CustomRole;
use App\Models\User;
use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Hash;
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
            'telephone'       => $request->telephone,
            'gender'          => $request->gender,
            'address'         => $request->address,
            'occupation'      => $request->occupation,
            'organisation_id' => $id,
            'updated_by'      => $request->user()->name
        ]);

        $role = CustomRole::findByName(Roles::MEMBER, 'api');
        $this->saveUserRole($created, $role);
    }

    public function getUsers($organisation_id)
    {
          return User::join('organisations', 'organisations.id', '=', 'users.organisation_id')
                 ->leftJoin('member_registrations', 'users.id', '=', 'member_registrations.user_id')
                 ->where('organisations.id', $organisation_id)
                 ->select('users.*', 'member_registrations.approve')
                 ->orderBy('created_at', 'DESC')->get();

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
            'name'            => $request->name,
            'email'           => $request->email,
            'address'         => $request->address,
            'occupation'      => $request->occupation,
            'gender'          => $request->gender,
            'updated_by'      => $request->user()->name,
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
        $user = User::where('telephone', $request->telephone)->orwhere('email', $request->email)->firstOrFail();

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
            'telephone'  => $request->telephone,
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
        return User::where('telephone', $request->credential)->orWhere('email', $request->credential)->firstOrFail();
    }

    public function importUsers($organisation_id, $request)
    {
        Excel::import(new UsersImport($organisation_id, $this->role_service), $request->file('file'));
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
