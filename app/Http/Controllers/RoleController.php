<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddUserRoleRequest;
use App\Http\Resources\RoleResource;
use App\Services\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    private $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function addUserRole(AddUserRoleRequest $request)
    {

        $this->roleService->addUserRole($request->user_id, $request->role);

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

        return $this->sendResponse($user_roles, 'success');
    }


    public function removeUserRole(Request $request, $user_id)
    {
        $this->roleService->removeRole($user_id, $request->role);
        return $this->sendResponse('success', 'Role remove successfully', 204);
    }
}
