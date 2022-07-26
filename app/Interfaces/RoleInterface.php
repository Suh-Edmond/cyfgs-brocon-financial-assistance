<?php
namespace App\Interfaces;

interface RoleInterface {

    public function addUserRole($userId, $role);

    public function removeRole($userId, $role);

    public function getUserRoles($userId);

    public function getAllRoles();

}
