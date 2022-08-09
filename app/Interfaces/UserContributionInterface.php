<?php
namespace App\Interfaces;

interface UserContributionInterface {

    public function createUserContribution($request);

    public function updateUserContribution($request, $id);

    public function getUserContributionsByItem($payment_item);

    public function getUserContributionsByUser($user_id);

    public function getUserContribution($payment_item_id, $user_id);

    public function deleteUserContribution($payment_item_id, $user_id);

    public function approveUserContribution($id);
}
