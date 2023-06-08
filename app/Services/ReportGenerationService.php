<?php

namespace App\Services;

use App\Constants\PaymentStatus;
use App\Http\Resources\ActivityReportResource;
use App\Http\Resources\DetailResource;
use App\Http\Resources\IncomeResource;
use App\Http\Resources\IncomeTotalResource;
use App\Http\Resources\MemberContributionResource;
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
    private PaymentCategoryService $paymentCategoryService;
    private PaymentItemService $paymentItemService;
    private UserContributionService $userContributionService;

    public function __construct(SessionService $sessionService, PaymentCategoryService $paymentCategoryService,
                                PaymentItemService $paymentItemService, UserContributionService $contributionService)
    {
        $this->sessionService = $sessionService;
        $this->paymentCategoryService = $paymentCategoryService;
        $this->paymentItemService = $paymentItemService;
        $this->userContributionService = $contributionService;
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
        $incomes = $this->fetchQuarterlyIncomes($request->quarter,$request->user()->organisation_id);
        return [$incomes, null];
    }

    public function downloadQuarterlyReport($request)
    {
         return [$this->fetchQuarterlyIncomes($request->quarter,$request->user()->organisation_id), null];
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

    private function getSponsorshipIncomePerQuarterly($quarter_num, $current_year): array
    {
         $start_quarter = $this->getStartQuarter($current_year->year, $quarter_num)[0];
         $end_quarter = $this->getStartQuarter($current_year->year, $quarter_num)[1];
         return  DB::table('activity_supports')
                    ->join('payment_items', 'payment_items.id', '=', 'activity_supports.payment_item_id')
                    ->join('sessions', 'sessions.id' , '=', 'activity_supports.session_id')
                    ->where('activity_supports.approve', PaymentStatus::APPROVED)
                    ->whereBetween('activity_supports.created_at', [$start_quarter, $end_quarter])
                    ->select('activity_supports.id', 'activity_supports.supporter as name', 'activity_supports.amount_deposited as amount', 'sessions.year')
                    ->orderBy('name')
                    ->get()
                    ->toArray();
    }

    private function getQuarterlyIncomeActivities($quarter_num, $current_year): array
    {
        $start_quarter = $this->getStartQuarter($current_year->year, $quarter_num)[0];
        $end_quarter = $this->getStartQuarter($current_year->year, $quarter_num)[1];

        return  DB::table('income_activities')
            ->join('payment_items', 'payment_items.id', '=', 'income_activities.payment_item_id')
            ->join('sessions', 'sessions.id' , '=', 'income_activities.session_id')
            ->where('income_activities.approve', PaymentStatus::APPROVED)
            ->whereBetween('income_activities.created_at', [$start_quarter, $end_quarter])
            ->select('income_activities.id', 'income_activities.name', 'income_activities.amount', 'sessions.year')
            ->orderBy('name')
            ->get()->toArray();
    }

    private function getMemberRegistrationPerQuarter($quarter_num, $current_year, $code)
    {
        $start_quarter = $this->getStartQuarter($current_year->year, $quarter_num)[0];
        $end_quarter = $this->getStartQuarter($current_year->year, $quarter_num)[1];

       $reg_amount = DB::table('member_registrations')
            ->join('registrations', 'registrations.id' , '=', 'member_registrations.registration_id')
            ->join('users', 'users.id', '=', 'member_registrations.user_id')
            ->join('sessions', 'sessions.id' , '=', 'member_registrations.session_id')
            ->where('member_registrations.approve', PaymentStatus::APPROVED)
            ->whereBetween('member_registrations.created_at', [$start_quarter, $end_quarter])
            ->selectRaw('SUM(registrations.amount) as amount')
            ->get()[0]->amount;
       return new IncomeResource($code, [], "Member's Registration", $reg_amount);
    }

    private function getMemberSavingPerQuarter($quarter_num, $current_year, $code)
    {
        $start_quarter = $this->getStartQuarter($current_year->year, $quarter_num)[0];
        $end_quarter = $this->getStartQuarter($current_year->year, $quarter_num)[1];

        $savings =  DB::table('user_savings')
            ->join('users', 'users.id', '=', 'user_savings.user_id')
            ->join('sessions', 'sessions.id' , '=', 'user_savings.session_id')
            ->where('user_savings.approve', PaymentStatus::APPROVED)
            ->whereBetween('user_savings.created_at', [$start_quarter, $end_quarter])
            ->selectRaw('SUM(user_savings.amount_deposited) as amount')
            ->get()[0]->amount;
        return new IncomeResource($code, [], "Member's Savings", $savings);
    }


    private function fetchQuarterlyIncomes($quarter_num, $organisation_id)
    {
        $current_year = $this->sessionService->getCurrentSession();
        $start_quarter = $this->getStartQuarter($current_year->year, $quarter_num)[0];
        $end_quarter = $this->getStartQuarter($current_year->year, $quarter_num)[1];
        $payment_categories = $this->paymentCategoryService->getPaymentCategories($organisation_id);
        $incomes = array();
        $total_reg_saving = 0;
        $member_reg = $this->getMemberRegistrationPerQuarter($quarter_num, $current_year, "I2");
        $savings = $this->getMemberSavingPerQuarter($quarter_num, $current_year, "I3");
        array_push($incomes, $member_reg, $savings);
        $total_reg_saving += $member_reg->total + $savings->total;
        foreach ($payment_categories as $key => $payment_category){
            $payment_items = $this->paymentItemService->getPaymentActivitiesByCategoryAndSession($payment_category->id, $current_year->id);
            $payment_activities = array();
            $payment_category_total = 0;
            foreach ($payment_items as $payment_item_key => $payment_item){
                $payment_incomes = array();
                $supports = $this->getSponsorshipIncomePerQuarterly($quarter_num, $current_year);
                $activities = $this->getQuarterlyIncomeActivities($quarter_num, $current_year);
                $user_contribution = $this->userContributionService->getContributionsByItemAndSession($payment_item->id, $start_quarter, $end_quarter);
                $payment_incomes = array_merge($user_contribution, $payment_incomes, $supports, $activities);
                $total = $this->calculateTotal($payment_incomes);

                $payment_activity = json_encode(new QuarterlyIncomeResource($payment_item_key + 1, $payment_item->name, $payment_incomes, $total));

                array_push($payment_activities, json_decode($payment_activity));

                $payment_category_total += $total;
            }
            $payment_category_total += $total_reg_saving;
            $payment_category_income = json_encode(new IncomeResource("I".($key+4), $payment_activities,  $payment_category->name, $payment_category_total));
            array_push($incomes, json_decode($payment_category_income));
        }
        return $incomes;
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
