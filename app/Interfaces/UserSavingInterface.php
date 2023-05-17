<?php
namespace App\Interfaces;


use Illuminate\Http\Request;

interface UserSavingInterface {

    public function createUserSaving($request);

    public function updateUserSaving($request, $id, $user_id);

    public function getUserSavings($user_id);

    public function getUserSaving($id, $user_id);

    public function deleteUserSaving($id, $user_id);

    public function approveUserSaving($id, $type);

    public function getAllUserSavingsByOrganisation($id, $session_id);

    public function findUserSavingByStatus($status, $id);

    public function  getMembersSavingsByName(Request  $request);
}

