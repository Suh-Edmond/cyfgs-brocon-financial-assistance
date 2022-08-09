<?php

namespace App\Http\Controllers;


use App\Http\Requests\AddUserRoleRequest;
use App\Http\Resources\RoleResource;
use App\Interfaces\RoleInterface;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    private $roleService;

    public function __construct(RoleInterface $roleService)
    {
        $this->roleService = $roleService;
    }

    public function addUserRole(AddUserRoleRequest $request)
    {
        (int)Str::uuid($request->role_id)->toString();
        $this->roleService->addUserRole($request->user_id, $request->role_id);

        return response()->json(['message' => "success"], 201);
    }


    public function getAllRoles()
    {

        $roles = $this->roleService->getAllRoles();

        return response()->json(["data" => RoleResource::collection($roles)], 200);
    }



    public function getUserRoles($user_id)
    {
        $user_roles = $this->roleService->getUserRoles($user_id);

        return response()->json(["data" => RoleResource::collection($user_roles)], 200);
    }


    public function removeUserRole($user_id, $role_id)
    {
        $this->roleService->removeRole($user_id, $role_id);

        return response()->json(['message' => 'success'], 204);
    }
}
