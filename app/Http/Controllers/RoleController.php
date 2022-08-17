<?php

namespace App\Http\Controllers;


use App\Http\Requests\AddUserRoleRequest;
use App\Http\Resources\RoleResource;
use App\Interfaces\RoleInterface;
use App\Services\RoleService;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    private $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function addUserRole(AddUserRoleRequest $request)
    {
        (int)Str::uuid($request->role_id)->toString();
        $this->roleService->addUserRole($request->user_id, $request->role_id);

        return $this->sendResponse('success', 'Role successfully added', 201);
    }


    public function getAllRoles()
    {

        $roles = $this->roleService->getAllRoles();

        return $this->sendResponse(RoleResource::collection($roles), 'success');
    }



    public function getUserRoles($user_id)
    {
        $user_roles = $this->roleService->getUserRoles($user_id);

        return $this->sendResponse(RoleResource::collection($user_roles), 'success');
    }


    public function removeUserRole($role_id, $user_id)
    {
        $user = $this->roleService->removeRole($user_id, $role_id);
        return $this->sendResponse('success', 'Role remove successfully', 204);
    }
}
