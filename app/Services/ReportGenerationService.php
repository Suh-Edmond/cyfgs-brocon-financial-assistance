<?php

namespace App\Services;

use App\Constants\PaymentStatus;
use App\Http\Resources\ActivityReportResource;
use App\Http\Resources\DetailResource;
use App\Interfaces\ReportGenerationInterface;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportGenerationService implements ReportGenerationInterface
{

    public function generateReportPerActivity($id)
    {
        $total = 0;
        $data = [];
        $expenditures = [];
        $members_contributions = $this->getApproveMembersContributionPerActivity($id)->toArray();
        $total_members_contributions = count($members_contributions) > 0  ? $members_contributions[0]->amount: 0;
        $incomes = $this->getIncomePerActivity($id)->toArray();
        $sponsorship = $this->getSponsorshipPerActivity($id)->toArray();
        array_push($data, new ActivityReportResource("Members Contributions", $total_members_contributions));
        $result = array_merge($incomes, $sponsorship);
        foreach ($result as $contribution){
            $total += $contribution->amount;
            array_push($data, new ActivityReportResource($contribution->name, $contribution->amount));
        }

        $expenses = $this->getExpenditureActivities($id);
        foreach ($expenses as $expense){
            array_push($expenditures, new DetailResource($expense->name, $expense->amount_given, $expense->amount_spent, ($expense->amount_given- $expense->amount_spent)));
        }
        return [$data, $expenditures];
    }

    public function generateQuarterlyReport()
    {
        $data = $this->getSponsorshipPerQuarterly();

        dd($data->toArray());
    }

    private function getApproveMembersContributionPerActivity($id)
    {
        return DB::table('user_contributions')
            ->join('users', 'users.id', '=' ,'user_contributions.user_id')
            ->join('payment_items', 'payment_items.id', '=', 'user_contributions.payment_item_id')
            ->where('user_contributions.payment_item_id', $id)
            ->where('user_contributions.approve', PaymentStatus::APPROVED)
            ->selectRaw('SUM(user_contributions.amount_deposited) as amount, payment_items.name')
            ->groupBy('user_contributions.payment_item_id')
            ->get();
    }

    private function getSponsorshipPerActivity($id)
    {
        return DB::table('activity_supports')
            ->join('payment_items', 'payment_items.id', '=', 'activity_supports.payment_item_id')
            ->where('activity_supports.payment_item_id', $id)
            ->where('activity_supports.approve', PaymentStatus::APPROVED)
            ->select('activity_supports.supporter as name', 'activity_supports.amount_deposited as amount')
            ->orderBy('activity_supports.supporter', 'DESC')
            ->get();
    }

    private function getIncomePerActivity($id)
    {
        return DB::table('income_activities')
            ->join('payment_items', 'payment_items.id', '=', 'income_activities.payment_item_id')
            ->where('income_activities.payment_item_id', $id)
            ->where('income_activities.approve', PaymentStatus::APPROVED)
            ->select('income_activities.name', 'income_activities.amount')
            ->orderBy('income_activities.name', 'DESC')
            ->get();
    }

    private function getExpenditureActivities($payment_activity)
    {
        return DB::table('expenditure_details')
            ->join('expenditure_items', 'expenditure_items.id', '=', 'expenditure_details.expenditure_item_id')
            ->join('payment_items', 'payment_items.id', '=', 'expenditure_items.payment_item_id')
            ->where('payment_items.id', $payment_activity)
            ->where('expenditure_details.approve', PaymentStatus::APPROVED)
            ->select('expenditure_details.name', 'expenditure_details.amount_given', 'expenditure_details.amount_spent')
            ->orderBy('expenditure_details.name', 'DESC')->get();
    }

    private function getStartOfQuarterly()
    {
        return Carbon::now()->startOfQuarter()->toDateTimeString();
    }

    private function getEndOfQuarterly()
    {
        return Carbon::now()->endOfQuarter()->toDateTimeString();
    }

    private function getSponsorshipPerQuarterly()
    {
        $start_of_quarterly = $this->getStartOfQuarterly();
        $end_of_quarterly   = $this->getEndOfQuarterly();

        return DB::table('activity_supports')
            ->join('payment_items', 'payment_items.id', '=', 'activity_supports.payment_item_id')
            ->where('activity_supports.approve', PaymentStatus::APPROVED)
            ->whereBetween('activity_supports.created_at', [$start_of_quarterly, $end_of_quarterly])
//            ->select('activity_supports.supporter as name', 'activity_supports.amount_deposited as amount')
            ->groupBy('activity_supports.payment_item_id')
            ->get();
    }
}
