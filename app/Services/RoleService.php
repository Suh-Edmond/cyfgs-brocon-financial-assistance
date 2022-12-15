<?php

namespace App\Services;

use App\Http\Resources\RoleResource;
use App\Models\User;
use App\Interfaces\RoleInterface;
use App\Models\CustomRole;
use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;

class RoleService implements RoleInterface {

    use ResponseTrait, HelpTrait;

    public function addUserRole($user_id, $role): bool
    {
        $user = User::findOrFail($user_id);
        $assignRole = CustomRole::findByName($role, 'api');

        $role_exist = $this->checkIfAUserAlreadyHasTheRole($user, $role);
        if($role_exist){
            $this->saveUserRole($user, $assignRole);
        }
        return $role_exist;
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

        $user_roles = DB::table('model_has_roles')->where('model_id', $user->id)->get();
        foreach ($user_roles as $role) {
            $assignedRole = DB::table('roles')->where('id', $role->role_id)->first();
            array_push($roles, new RoleResource($assignedRole, $role->updated_by));
        }
        return $roles;
    }


    public function getAllRoles() {
        return CustomRole::all();
    }


    public function findRole($role_name){
        $role = CustomRole::findByName($role_name, 'api');
        if(is_null($role)){
            return $this->sendError('Role not found', 'The role to be assigned does not exist', 404);
        }

        return $role;
    }

    private function checkIfAUserAlreadyHasTheRole($user, $role): bool
    {
        $max_num_users_with_same_role = 1;

        $users = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->select('users.*')
            ->where('roles.name', $role)
            ->count();

        return $users <= $max_num_users_with_same_role;
    }
}
