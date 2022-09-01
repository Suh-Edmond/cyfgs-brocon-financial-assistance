<?php

namespace App\Services;

use App\Models\User;
use App\Interfaces\RoleInterface;
use App\Models\CustomRole;
use App\Traits\ResponseTrait;

class RoleService implements RoleInterface {

    use ResponseTrait;

    public function addUserRole($user_id, $role)
    {
        $user = User::findOrFail($user_id);
        $assignRole = CustomRole::findByName($role, 'api');
        $user->assignRole($assignRole);

    }

    public function removeRole($user_id, $role)
    {
        $user = User::findOrFail($user_id);
        $user_role = CustomRole::findByName($role, 'api');
        $user->removeRole($user_role);

    }

    public function getUserRoles($user_id)
    {
        $user = User::findOrFail($user_id);
        $roles = [];
        foreach($user->roles as $role){
            array_push($roles, CustomRole::findByName($role->name, 'api'));
        }
        return $roles;
    }


    public function getAllRoles() {
        return CustomRole::all();
    }


    public function findRole($role_name){
        $role = CustomRole::findByName($role_name, 'api');
        if(is_null($role)){
            return $this->sendError('Role not found', 404);
        }

        return $role;
    }


}
