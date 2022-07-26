<?php

namespace App\Http\Controllers;

use App\Services\RoleService;
use Illuminate\Http\Request;
use App\Http\Requests\AddUserRoleRequest;

class RoleController extends Controller
{
    private $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function addUserRole(AddUserRoleRequest $request)
    {
        return $this->roleService->addUserRole($request->user_id, $request->role_id);
    }


    public function getAllRoles()
    {
        return $this->roleService->getAllRoles();
    }



    public function getUserRoles($user_id)
    {
        return $this->roleService->getUserRoles($user_id);
    }


    public function removeUserRole($user_id, $role_id)
    {
        $this->roleService->removeRole($user_id, $role_id);
    }
}
