<?php

namespace App\Services;

use App\Models\User;
use App\Interfaces\RoleInterface;
use Spatie\Permission\Models\Role;

class RoleService implements RoleInterface {


    public function addUserRole($user_id, $role_id)
    {
        $user = User::findOrFail($user_id);
        $assignRole = Role::find($role_id);
        if($user != null){
            $user->assignRole($assignRole);
        }
    }

    public function removeRole($user_id, $role_id)
    {
        $user = User::findOrFail($user_id);
        $deleted = Role::find($role_id);
        return $user->removeRole($deleted);
    }

    public function getUserRoles($user_id)
    {
        $user = User::findOrFail($user_id);
        return $user->roles->toArray();
    }

    public function getAllRoles() {
        return Role::all();
    }


}
