<?php

namespace App\Services;

use App\Exceptions\BusinessValidationException;
use App\Http\Resources\RoleResource;
use App\Models\User;
use App\Interfaces\RoleInterface;
use App\Models\CustomRole;
use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RoleService implements RoleInterface {

    use ResponseTrait, HelpTrait;

    public function addUserRole($user_id, $role, $updated_by): bool
    {
        $user = User::findOrFail($user_id);
        $assignRole = CustomRole::findByName($role, 'api');

        $member_has_role = $this->checkIfRoleCanBeAdded($assignRole);
        if($member_has_role){
            $this->saveUserRole($user, $assignRole, $updated_by);
        }else {
            throw new BusinessValidationException("Only one member of your organisation can have this role", 403);
        }
        return $member_has_role;
    }

    public function removeRole($user_id, $role)
    {
        $user = User::findOrFail($user_id);
        $user_role = CustomRole::findByName($role, 'api');
        $assigned_role = $this->getAssignedRole($user_role->id, $user->id);
        if(isset($assigned_role)){
            $role_end_term = (Carbon::create($assigned_role->created_at)->addYears($user_role->term));
            if(Carbon::now()->greaterThanOrEqualTo($role_end_term)){
                $user->removeRole($user_role);
            }else {
                throw new BusinessValidationException("This User has not reach the Expiration Term. Term: ". $user_role->term, 427);
            }
        }else {
            throw new BusinessValidationException("User does not have this role: ".$role);
        }

    }

    public function getUserRoles($user_id)
    {
        $user_roles = DB::table('model_has_roles')
            ->join('users', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_id', $user_id)
            ->select('roles.*')->get();
        return RoleResource::collection($user_roles);
    }


    public function getAllRoles() {
        return RoleResource::collection(CustomRole::all());
    }


    public function findRole($role_name){
        $role = CustomRole::findByName($role_name, 'api');
        if(is_null($role)){
            return $this->sendError('Role not found', 'The role to be assigned does not exist', 404);
        }

        return $role;
    }

    public function updateRole($request)
    {
        $role = $this->findRole($request->name);
        $role->update([
            'term' => $request->term,
            'number_of_members' => $request->number_of_members
        ]);
        return $role;
    }

    private function checkIfRoleCanBeAdded($assign_role): bool
    {
        $users = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->select('users.*')
            ->where('roles.name', $assign_role->name)
            ->count();
        return $assign_role->number_of_members > $users;
    }

    private function getAssignedRole($role_id, $model_id)
    {
        return DB::table('model_has_roles')
            ->join('users', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.role_id', $role_id)
            ->where('model_has_roles.model_id', $model_id)
            ->select('model_has_roles.*')->first();
    }
}
