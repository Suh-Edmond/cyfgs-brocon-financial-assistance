<?php
namespace App\Interfaces;


interface UserSavingInterface {

    public function createUserSaving($request);

    public function updateUserSaving($request, $id, $user_id);

    public function getUserSavings($user_id);

    public function getUserSaving($id, $user_id);

    public function deleteUserSaving($id, $user_id);

    public function approveUserSaving($id, $type);

    public function getAllUserSavingsByOrganisation($request);

    public function findUserSavingByStatus($status, $id);

    public function  getMembersSavingsByOrganisation($request);

    public function getSavingsStatistics($request);
}

