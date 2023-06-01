<?php

namespace App\Services;

use App\Constants\PaymentStatus;
use App\Http\Resources\ActivityReportResource;
use App\Http\Resources\DetailResource;
use App\Http\Resources\IncomeTotalResource;
use App\Http\Resources\QuarterlyExpenditureResource;
use App\Http\Resources\QuarterlyExpenditureResourceCollection;
use App\Http\Resources\QuarterlyIncomeResource;
use App\Http\Resources\QuarterlyIncomeResourceCollection;
use App\Interfaces\ReportGenerationInterface;
use App\Traits\HelpTrait;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportGenerationService implements ReportGenerationInterface
{

    use HelpTrait;
    private SessionService $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

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

    public function generateQuarterlyReport($request)
    {
        $incomes = $this->fetchQuarterlyIncomes($request->quarter);
        $expenditures = $this->fetchQuarterlyExpenditures($request->quarter);

        return [$incomes, $expenditures];
    }

    private function fetchQuarterlyIncomes($quarter)
    {
        $sponsorships =  new QuarterlyIncomeResourceCollection($this->getSponsorshipIncomePerQuarterly($quarter), "Sponsorships");
        $income_activities = new QuarterlyIncomeResourceCollection($this->getQuarterlyIncomeActivities($quarter), "Income Activities");
        $registrations =  new QuarterlyIncomeResourceCollection($this->getMemberRegistrationPerQuarter($quarter), "Member's Registrations");
        $savings = new QuarterlyIncomeResourceCollection($this->getMemberSavingPerQuarter($quarter), "Member's Savings");
        $contributions = new QuarterlyIncomeResourceCollection($this->getContributionsPerQuarterly($quarter), "Member's Contributions");

        $total_income = $this->computeTotalIncomePerQuarter([($income_activities), ($sponsorships), ($registrations), ($savings), ($contributions)]);

        return [($income_activities), ($sponsorships), ($registrations), ($savings), ($contributions), ($total_income)];
    }
    private function fetchQuarterlyExpenditures($quarter): QuarterlyExpenditureResourceCollection
    {
        $expenditures = $this->getQuarterlyExpenditures($quarter);
        $expenditure_collection = new QuarterlyExpenditureResourceCollection($expenditures, "Expenditures", $this->computeTotalExpenditurePerQuarter($expenditures));
        return ($expenditure_collection);
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

    private function getExpenditureActivities($payment_activity): Collection
    {
        return DB::table('expenditure_details')
            ->join('expenditure_items', 'expenditure_items.id', '=', 'expenditure_details.expenditure_item_id')
            ->join('payment_items', 'payment_items.id', '=', 'expenditure_items.payment_item_id')
            ->where('payment_items.id', $payment_activity)
            ->where('expenditure_details.approve', PaymentStatus::APPROVED)
            ->select('expenditure_details.name', 'expenditure_details.amount_given', 'expenditure_details.amount_spent')
            ->orderBy('expenditure_details.name', 'DESC')->get();
    }

    private function getSponsorshipIncomePerQuarterly($quarter_num): array
    {
        $current_year = $this->sessionService->getCurrentSession();
        $start_quarter = $this->getStartQuarter($current_year->year, $quarter_num)[0];
        $end_quarter = $this->getStartQuarter($current_year->year, $quarter_num)[1];
        $sponsorships = [];
        $data =  DB::table('activity_supports')
                    ->join('payment_items', 'payment_items.id', '=', 'activity_supports.payment_item_id')
                    ->join('sessions', 'sessions.id' , '=', 'activity_supports.session_id')
                    ->where('activity_supports.approve', PaymentStatus::APPROVED)
                    ->whereBetween('activity_supports.created_at', [$start_quarter, $end_quarter])
                    ->select('activity_supports.*', 'payment_items.id as payment_item_id',
                        'payment_items.name as payment_item_name', 'sessions.year')
                    ->get()->groupBy('payment_item_name');
        foreach ($data as $sponsorship){
            $total = $this->computeTotalContribution($sponsorship);
            array_push($sponsorships, new QuarterlyIncomeResource($sponsorship[0]->payment_item_name, $sponsorship, $total));
        }

        return $sponsorships;
    }

    private function getQuarterlyIncomeActivities($quarter_num): array
    {
        $current_year = $this->sessionService->getCurrentSession();
        $start_quarter = $this->getStartQuarter($current_year->year, $quarter_num)[0];
        $end_quarter = $this->getStartQuarter($current_year->year, $quarter_num)[1];
        $income_activities = [];

        $data =  DB::table('income_activities')
            ->join('payment_items', 'payment_items.id', '=', 'income_activities.payment_item_id')
            ->join('sessions', 'sessions.id' , '=', 'income_activities.session_id')
            ->where('income_activities.approve', PaymentStatus::APPROVED)
            ->whereBetween('income_activities.created_at', [$start_quarter, $end_quarter])
            ->select('income_activities.*', 'payment_items.id as payment_item_id',
                'payment_items.name as payment_item_name', 'sessions.year')
            ->get()->groupBy('payment_item_name');
        foreach ($data as $income){
            $total = $this->computeTotalAmountByPaymentCategory($income);
            array_push($income_activities, new QuarterlyIncomeResource($income[0]->payment_item_name, $income, $total));
        }

        return $income_activities;
    }

    private function getMemberRegistrationPerQuarter($quarter_num)
    {
        $current_year = $this->sessionService->getCurrentSession();
        $start_quarter = $this->getStartQuarter($current_year->year, $quarter_num)[0];
        $end_quarter = $this->getStartQuarter($current_year->year, $quarter_num)[1];

        $data =  DB::table('member_registrations')
            ->join('registrations', 'registrations.id' , '=', 'member_registrations.registration_id')
            ->join('users', 'users.id', '=', 'member_registrations.user_id')
            ->join('sessions', 'sessions.id' , '=', 'member_registrations.session_id')
            ->where('member_registrations.approve', PaymentStatus::APPROVED)
            ->whereBetween('member_registrations.created_at', [$start_quarter, $end_quarter])
            ->select('member_registrations.*', 'users.name as user_name',
                'registrations.*', 'sessions.year')
            ->get();
        $total = $this->computeTotalAmountByPaymentCategory($data);

        return [new QuarterlyIncomeResource("Member's Registration", $data, $total)];
    }

    private function getMemberSavingPerQuarter($quarter_num)
    {
        $current_year = $this->sessionService->getCurrentSession();
        $start_quarter = $this->getStartQuarter($current_year->year, $quarter_num)[0];
        $end_quarter = $this->getStartQuarter($current_year->year, $quarter_num)[1];

        $data =  DB::table('user_savings')
            ->join('users', 'users.id', '=', 'user_savings.user_id')
            ->join('sessions', 'sessions.id' , '=', 'user_savings.session_id')
            ->where('user_savings.approve', PaymentStatus::APPROVED)
            ->whereBetween('user_savings.created_at', [$start_quarter, $end_quarter])
            ->select('user_savings.*', 'users.name as user_name', 'sessions.year')
            ->get();
        $total = $this->computeTotalContribution($data);

        return [new QuarterlyIncomeResource("Member's Savings", $data, $total)];
    }


    private function getContributionsPerQuarterly($quarter_num){
        $current_year = $this->sessionService->getCurrentSession();
        $start_quarter = $this->getStartQuarter($current_year->year, $quarter_num)[0];
        $end_quarter = $this->getStartQuarter($current_year->year, $quarter_num)[1];
        $contributions = [];

        $data =  DB::table('user_contributions')
            ->join('payment_items', 'payment_items.id', '=', 'user_contributions.payment_item_id')
            ->join('sessions', 'sessions.id' , '=', 'user_contributions.session_id')
            ->join('users', 'users.id', '=', 'user_contributions.user_id')
            ->where('user_contributions.approve', PaymentStatus::APPROVED)
            ->whereBetween('user_contributions.created_at', [$start_quarter, $end_quarter])
            ->select('user_contributions.*', 'payment_items.id as payment_item_id',
                'payment_items.name as payment_item_name', 'sessions.year', 'users.name as user_name')
            ->orderBy('users.name')
            ->get()->groupBy('payment_item_name');
        foreach ($data as $contribution){
            $total = $this->computeTotalContribution($contribution);
            array_push($contributions, new QuarterlyIncomeResource($contribution[0]->payment_item_name, $contribution, $total));
        }

        return $contributions;
    }

    private function  computeTotalIncomePerQuarter($incomes){
        $total_quarter_income = 0;
        foreach ($incomes as $income){
            foreach ($income as $value){
                 $total_quarter_income += json_decode(json_encode($value))->total;
            }
        }
        return new IncomeTotalResource($total_quarter_income);
    }
    private function getQuarterlyExpenditures($quarter_num) {
        $current_year = $this->sessionService->getCurrentSession();
        $start_quarter = $this->getStartQuarter($current_year->year, $quarter_num)[0];
        $end_quarter = $this->getStartQuarter($current_year->year, $quarter_num)[1];
        $contributions = [];

        $data =  DB::table('expenditure_details')
            ->join('expenditure_items', 'expenditure_items.id', '=', 'expenditure_details.expenditure_item_id')
            ->join('payment_items', 'payment_items.id', '=', 'expenditure_items.payment_item_id')
            ->join('sessions', 'sessions.id' , '=', 'expenditure_items.session_id')
            ->join('expenditure_categories', 'expenditure_categories.id' , '=', 'expenditure_items.expenditure_category_id')
            ->where('expenditure_items.approve', PaymentStatus::APPROVED)
            ->whereBetween('expenditure_items.created_at', [$start_quarter, $end_quarter])
            ->select('expenditure_details.*', 'expenditure_items.name as expenditure_item_name', 'expenditure_categories.name as expenditure_category_name')
            ->orderBy('expenditure_item_name')
            ->get()->groupBy('expenditure_item_name');
         foreach ($data as $contribution){
            $total = $this->calculateTotalAmountSpent($contribution);
            array_push($contributions, new QuarterlyExpenditureResource($contribution[0]->expenditure_item_name, $contribution, $total));
        }

        return $contributions;
    }

    private function computeTotalExpenditurePerQuarter($quarterly_expenditures){
        $data = json_decode(json_encode($quarterly_expenditures));
        $total_expenditure = 0;
         foreach ($data as $expenditure){
            $total_expenditure += $expenditure->total;
        }
        return $total_expenditure;
    }
}
