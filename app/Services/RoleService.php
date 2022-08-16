<?php

namespace App\Services;

use App\Models\User;
use App\Interfaces\RoleInterface;
use App\Traits\ResponseTrait;
use Spatie\Permission\Models\Role;

class RoleService implements RoleInterface {

    use ResponseTrait;

    public function addUserRole($user_id, $role_id)
    {
        $user = User::findOrFail($user_id);
        $assignRole = Role::find($role_id);
        $user->assignRole($assignRole);

    }

    public function removeRole($user_id, $role_id)
    {
        $user = User::findOrFail($user_id);
        $user_role = Role::find($role_id);
        $delete_role =array_filter($user->roles->toArray(), function($role, $role_id) {
            return $role->id == $role_id;
        });
        // $user->removeRole($user_role);

        return $delete_role;
    }

    public function getUserRoles($user_id)
    {
        $user = User::findOrFail($user_id);
        return $user->roles;
    }


    public function getAllRoles() {
        return Role::all();
    }


}
