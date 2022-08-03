<?php
namespace App\Interfaces;


interface UserSavingInterface {

    public function createUserSaving($request);

    public function updateUserSaving($request, $id, $user_id);

    public function getUserSavings($expenditure_item_id);

    public function getUserSaving($id, $expenditure_item_id);

    public function deleteUserSaving($id, $expenditure_item_id);

    public function approveUserSaving($id);

    public function calculateExpenditureBalance($id, $expenditure_item_id);
}

