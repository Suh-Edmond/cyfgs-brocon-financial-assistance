<?php
namespace App\Interfaces;

interface UserContributionInterface {

    public function createUserContribution($request);

    public function updateUserContribution($request, $id);

    public function getContributionsByItem($payment_item);

    public function getUserContributionsByUser($user_id);

    public function getContributionByUserAndItem($payment_item_id, $user_id);

    public function deleteUserContribution($id);

    public function approveUserContribution($id);

    public function filterContribution($status, $payment_item, $year, $month);

    public function getContribution($id);

    public function getTotalAmountPaidByUserAndItem($user_id, $payment_item_id);

    public function filterContributionByYear($payment_item_id, $year);
}
