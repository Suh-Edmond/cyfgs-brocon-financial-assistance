<?php

namespace App\Services;

use App\Constants\Roles;
use App\Http\Resources\UserResource;
use App\Imports\UsersImport;
use App\Interfaces\UserManagementInterface;
use App\Models\CustomRole;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class UserManagementService implements UserManagementInterface
{
    use ResponseTrait;
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

        $role = CustomRole::findByName(Roles::USER, 'api');
        $this->saveUserRole($created, $role);
    }

    public function getUsers($organisation_id)
    {
        return User::where('organisation_id', $organisation_id)->get();
    }

    public function getUser($user_id)
    {
        return User::findOrFail($user_id);
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
            'updated_by'      => $request->user()->name
        ]);
    }

    public function deleteUser($user_id)
    {
        User::findOrFail($user_id)->delete();
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


    public function checkUserExist($request)
    {
        return User::where('telephone', $request->credential)->orWhere('email', $request->credential)->firstOrFail();
    }

    public function importUsers($organisation_id, $request)
    {
        Excel::import(new UsersImport($organisation_id, $this->role_service), $request->file('file'));
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
