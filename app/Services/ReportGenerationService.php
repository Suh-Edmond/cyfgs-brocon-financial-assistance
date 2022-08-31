<?php

namespace App\Services;

use App\Interfaces\ReportGenerationInterface;
use App\Models\IncomeActivity;
use App\Models\PaymentItem;
use App\Models\UserContribution;
use App\Models\UserSaving;
use PaymentItems;

class ReportGenerationService implements ReportGenerationInterface
{

    public function calculateIncomesForEachMonth($month, $organisation_id)
    {
    }

    public function calculateIncomesForThreeMonths($month_range, $organisation_id)
    {
    }

    public function calculateIncomesForSixMonths($month_range, $organisation_id)
    {
    }

    public function calculateIncomesForYear($year, $organisation_id)
    {
    }

    public function getIncomeActivitiesForThreeMonths($month_range, $organisation_id)
    {
    }

    public function getUserContributionsForThreeMonths($month_range, $organisation_id)
    {
    }

    public function getUserSavingsForThreeMonths($month_range, $organisation_id)
    {
    }

    public function getIncomeActivitiesForEachMonth($month, $organisation_id)
    {
        $income_activities = IncomeActivity::select('income_activities.*')
            ->join('organisations', ['organisations.id' => 'income_activities.organisation_id'])
            ->where('income_activities.organisation_id', $organisation_id)
            ->whereMonth('income_activities.created_at', $month)
            ->where('income_activities.approve', 1)
            ->get();

        return $income_activities;
    }

    public function getUserContributionsForEachMonth($month, $organisation_id)
    {
        $contributions = UserContribution::select('user_contributions.*')
            ->join('users', ['users.id'  => 'user_contributions.user_id'])
            ->join('payment_items', ['payment_items.id' => 'user_contributions.payment_item_id'])
            ->join('organisations', ['organisations.id' => 'users.organisation_id'])
            ->where('organisations.id', $organisation_id)
            ->whereMonth('user_contributions.created_at', $month)
            ->get();

        return $contributions;
    }

    public function getUserSavingsForEachMonth($month, $organisation_id)
    {
        $income_activities = UserSaving::select('user_savings.*')
            ->join('users',         ['users.id'          => 'user_savings.user_id'])
            ->join('organisations', ['organisations.id'  => 'users.organisation_id'])
            ->where('organisations.id', $organisation_id)
            ->whereMonth('user_savings.created_at', $month)
            ->get();

        return $income_activities;
    }

    public function groupUserContributionsByPaymentItem($contributions, $month)
    {
        $user_contributions = [];
        $payment_items       = $this->getPaymentItemsByMonth($month);
        foreach ($payment_items as $payment_item) {
        $user_contributions = array_filter($contributions->toArray(), function ($item) use ($payment_item) {
                                return $item->payment_item_id === $payment_item->id ? true : false;
                            });
        }
    }


    public function getPaymentItemsByMonth($month)
    {
        $items = PaymentItem::select('*')
            ->where('payment_items.created_at', $month)
            ->get();

        return $items;
    }
}
