<?php

namespace App\Interfaces;

interface UserManagementInterface {

    public function createUser($request);

    public function getUsers($organisation_id);

    public function getUser($user_id);

    public function updateUser($user_id, $request);

    public function deleteUser($user_id);

    public function loginUser($request);


}
