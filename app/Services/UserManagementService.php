<?php

namespace App\Services;

use App\Constants\Roles;
use App\Http\Resources\UserTokenResource;
use App\Interfaces\UserManagementInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserManagementService implements UserManagementInterface {

    public function createUser($request)
    {
        User::create([
            'name'            => $request->name,
            'email'           => $request->email,
            'telephone'       => $request->telephone,
            'password'        => Hash::make($request->password),
            'gender'          => $request->gender,
            'address'         => $request->address,
            'occupation'      => $request->occupation,
            'organisation_id' => $request->organisation_id
        ]);
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
            'telephone'       => $request->telephone,
            'address'         => $request->address,
            'occupation'      => $request->occupation,
        ]);
    }

    public function deleteUser($user_id)
    {
        User::findOrFail($user_id)->delete();
    }

    public function loginUser($request)
    {
        $user = User::where('telephone', $request->telephone)->first();

        if(! $user || ! Hash::check($request->password, $user->password)){
            return response()->json(['message' => 'incorrect credentials', 'status' => '404'], 404);
        }

        $token = $user->createToken('access-token', [Roles::USER])->plainTextToken;

        return new UserTokenResource($user);
    }
}
