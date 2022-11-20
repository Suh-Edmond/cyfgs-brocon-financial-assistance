<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Imports\UsersImport;
use App\Interfaces\UserManagementInterface;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class UserManagementService implements UserManagementInterface
{
    private $role_service;

    public function __construct(RoleService $role_service)
    {
        $this->role_service = $role_service;
    }

    use ResponseTrait;
    public function AddUserUserToOrganisation($request, $id)
    {
        $saved = User::create([
            'name'            => $request->name,
            'email'           => $request->email,
            'telephone'       => $request->telephone,
            'gender'          => $request->gender,
            'address'         => $request->address,
            'occupation'      => $request->occupation,
            'organisation_id' => $id
        ]);

        return $saved;
    }

    public function getUsers($organisation_id)
    {
        $users =  User::where('organisation_id', $organisation_id)->get();

        return $users;
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
            'telephone'       => $request->telephone,
            'address'         => $request->address,
            'occupation'      => $request->occupation,
            'gender'          => $request->gender,
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
        $saved =  User::create([
            'name'       => $request->name,
            'telephone'  => $request->telephone,
            'password'   => Hash::make($request->password),
            'email'      => $request->email
        ]);

        return $saved;
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
        $user = User::where('telephone', $request->credential)->orWhere('email', $request->credential)->firstOrFail();
        return $user;
    }

    public function importUsers($organisation_id, $request)
    {
        Excel::import(new UsersImport($organisation_id, $this->role_service), $request->file('file'));
    }

    private function generateToken($user)
    {
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
