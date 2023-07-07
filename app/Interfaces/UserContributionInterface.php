<?php
namespace App\Interfaces;

interface UserContributionInterface {

    public function createUserContribution($request);

    public function updateUserContribution($request, $id);

    public function getContributionsByItem($payment_item);

    public function getUserContributionsByUser($user_id);

    public function getContributionByUserAndItem($payment_item_id, $user_id);

    public function deleteUserContribution($id);

    public function approveUserContribution($request);

    public function filterContributions($request);

    public function getContribution($id);

    public function getTotalAmountPaidByUserForTheItem($user_id, $payment_item_id);

    public function filterContributionByYear($payment_item_id, $year);

    public function bulkPayment($request);

    public function getMemberDebt($user_id, $year);

    public function getMemberContributedItems($user_id, $year);

    public function getYearlyContributions($request);

    public function getContributionStatistics($request);
}
