<?php

namespace App\Interfaces;

interface UserManagementInterface {

    public function AddUserUserToOrganisation($request, $id);

    public function getUsers($organisation_id);

    public function getTotalUsersByRegStatus($organisation_id);

    public function getRegMemberByMonths($organisation_id);

    public function getUser($user_id);

    public function updateUser($user_id, $request);

    public function deleteUser($user_id);

    public function loginUser($request);

    public function createAccount($request);

    public function setPassword($request);

    public function checkUserExist($request);

    public function importUsers($organisation_id, $request);

    public function filterUsers($request);

    public function updateProfile($request);

    public function updatePassword($request);

    public function setPasswordResetToken($request);

    public function validateResetToken($request);

    public function resetPassword($request);

    public function getUserByPaymentItem($id, $request);

}
