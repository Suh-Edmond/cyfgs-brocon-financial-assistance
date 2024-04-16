<?php
namespace App\Interfaces;

interface RoleInterface {

    public function addUserRole($user_id, $role, $updated_by);

    public function removeRole($user_id, $role);

    public function getUserRoles($user_id);

    public function getAllRoles();

    public function findRole($role_name);

    public function updateRole($request);

}
