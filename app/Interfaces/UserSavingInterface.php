<?php
namespace App\Interfaces;


interface UserSavingInterface {

    public function createUserSaving($request);

    public function updateUserSaving($request, $id, $user_id);

    public function getUserSavings($user_id);

    public function getUserSaving($id, $user_id);

    public function deleteUserSaving($id, $user_id);

    public function approveUserSaving($id);

    public function calculateUserSaving($id, $user_id);

    public function getAllUserSavingsByOrganisation($id);
}

