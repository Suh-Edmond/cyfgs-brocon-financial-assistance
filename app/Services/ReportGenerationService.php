<?php

namespace App\Services;

use App\Http\Resources\ActivityReportResource;
use App\Http\Resources\DetailResource;
use App\Http\Resources\IncomeResource;
use App\Http\Resources\QuarterlyExpenditureResource;
use App\Http\Resources\QuarterlyExpenditureResourceCollection;
use App\Http\Resources\QuarterlyIncomeResource;
use App\Interfaces\ReportGenerationInterface;
use App\Traits\HelpTrait;

class ReportGenerationService implements ReportGenerationInterface
{

    use HelpTrait;
    private SessionService $sessionService;
    private PaymentCategoryService $paymentCategoryService;
    private PaymentItemService $paymentItemService;
    private UserContributionService $userContributionService;
    private ExpenditureCategoryService $expenditureCategoryService;
    private ExpenditureItemService $expenditureItemService;
    private ExpenditureDetailService $expenditureDetailService;
    private ActivitySupportService $activitySupportService;
    private IncomeActivityService $incomeActivityService;
    private RegistrationService $registrationService;
    private UserSavingService $userSavingService;

    public function __construct(SessionService $sessionService, PaymentCategoryService $paymentCategoryService,
                                PaymentItemService $paymentItemService, UserContributionService $contributionService,
                                ExpenditureCategoryService $expenditureCategoryService, ExpenditureItemService $expenditureItemService,
                                ExpenditureDetailService $expenditureDetailService, ActivitySupportService $activitySupportService,
                                IncomeActivityService $incomeActivityService, RegistrationService $registrationService,
                                UserSavingService $userSavingService)
    {
        $this->sessionService = $sessionService;
        $this->paymentCategoryService = $paymentCategoryService;
        $this->paymentItemService = $paymentItemService;
        $this->userContributionService = $contributionService;
        $this->expenditureCategoryService = $expenditureCategoryService;
        $this->expenditureItemService = $expenditureItemService;
        $this->expenditureDetailService = $expenditureDetailService;
        $this->activitySupportService = $activitySupportService;
        $this->incomeActivityService = $incomeActivityService;
        $this->registrationService = $registrationService;
        $this->userSavingService = $userSavingService;
    }

    public function generateReportPerActivity($id): array
    {
        $total_income = 0;
        $total_amount_given = 0;
        $total_amount_spent = 0;
        $total_balance =0;
        $balance = 0;
        $income_list = [];
        $expenditures = [];
        $members_contributions = $this->userContributionService->getApproveMembersContributionPerActivity($id)->toArray();
        $total_members_contributions = count($members_contributions) > 0  ? $members_contributions[0]->amount: 0;
        $incomes = $this->incomeActivityService->getIncomePerActivity($id)->toArray();
        $sponsorship = $this->activitySupportService->getSponsorshipPerActivity($id)->toArray();
        array_push($income_list, new ActivityReportResource("Members Contributions", $total_members_contributions));
        $data = array_merge($incomes, $sponsorship);
        foreach ($data as $contribution){
            $total_income += $contribution->amount;
            array_push($income_list, new ActivityReportResource($contribution->name, $contribution->amount));
        }
        $total_income += $total_members_contributions;

        $expenses = $this->expenditureDetailService->getExpenditureActivities($id);

        foreach ($expenses as $expense){
            $total_amount_spent += $expense->amount_spent;
            $total_amount_given += $expense->amount_given;
            $balance += ($expense->amount_given- $expense->amount_spent);
            array_push($expenditures, new DetailResource($expense->name, $expense->amount_given, $expense->amount_spent, ($expense->amount_given- $expense->amount_spent)));
        }
        $total_balance += $balance > 0 ? ($total_income - $total_amount_spent)  + $balance: ($total_income - $total_amount_spent);
        $admins            = $this->getOrganisationAdministrators();
        $president         = $admins[0];
        $treasurer         = count($admins) == 3 ? $admins[2]: null;
        $fin_sec           = count($admins) == 3 ? $admins[1] : null;

        return [$income_list, $expenditures, ["total_income" => $total_income], ["total_amount_given" => $total_amount_given],
            ["total_amount_spent" => $total_amount_spent], ["balance" => $balance], ["total_balance" => $total_balance],
            ["president" => $president], ["fin_sec" => $fin_sec], ["treasurer" => $treasurer]];
    }

    public function generateQuarterlyReport($request): array
    {
        $current_year = $this->sessionService->getCurrentSession();
        $incomes = $this->fetchQuarterlyIncomes($request->organisation_id, $request->quarter, $current_year);
        $income_elements = $incomes[0];
        $total_income = $incomes[1];
        $expenditures = $this->fetchQuarterlyExpenditures($request->quarter,$current_year, $request->organisation_id);
        $expenditures_elements = $expenditures[0];
        $total_expenditures = $expenditures[1];
        $balance_bf = $this->computeBalanceBroughtForwardByQuarter($request, $current_year);
        $admins            = $this->getOrganisationAdministrators();
        $president         = $admins[0];
        $treasurer         = count($admins) == 3 ? $admins[2]: null;
        $fin_sec           = count($admins) == 3 ? $admins[1] : null;
        return [$income_elements, $expenditures_elements, ["total_income" => $total_income], ["total_expenditure" => $total_expenditures],
            ["balance_brought_forward" => $balance_bf], ["president" => $president], ["treasurer" => $treasurer], ["fin_sec" => $fin_sec]];
    }

    public function downloadQuarterlyReport($request): array
    {
        return $this->generateQuarterlyReport($request);
    }

    public function computeBalanceBroughtForwardByQuarter($request, $current_year){
        $quarter = (int) $request->quarter > 1 ? ((int) $request->quarter - 1) : ((int) $request->quarter);
        $income = $this->fetchQuarterlyIncomes($request->organisation_id, $quarter, $current_year);
        $expenditure = $this->fetchQuarterlyExpenditures($quarter, $current_year, $request->organisation_id);
        return $income[1] - $expenditure[1];
    }

    public function computeBalanceBroughtForwardByYear($request)
    {
        $previous_year = (int)$request->year_label - 1;
        $previous_year_id = $this->sessionService->getSessionByLabel($previous_year);
        $income = $this->fetchYearIncomes($previous_year_id, $request->year_label, $request->user()->organisation_id)[1];
        $expenditure = $this->fetchYearlyExpenditures($previous_year_id, $request->user()->organisation_id)[1];

        return $income - $expenditure;
    }


    public function generateYearlyReport($request): array
    {
        $bal_brought_forward = $this->computeBalanceBroughtForwardByYear($request);

        $income = $this->fetchYearIncomes($request->year_id, $request->year_label, $request->user()->organisation_id);

        $expenditures = $this->fetchYearlyExpenditures($request->year_id, $request->user()->organisation_id);

        $admins            = $this->getOrganisationAdministrators();
        $president         = $admins[0];
        $treasurer         = count($admins) == 3 ? $admins[2]: null;
        $fin_sec           = count($admins) == 3 ? $admins[1] : null;
        $income_list = $income[0];
        $total_income = $income[1];
        $expenditure_list = $expenditures[0];
        $total_expenditure = $expenditures[1];

        return [$income_list, $expenditure_list, ["total_income" => $total_income], ["total_expenditure" => $total_expenditure],["bal_brought_forward" => $bal_brought_forward],
            ["president" => $president], ["treasurer" => $treasurer], ["fin_sec" => $fin_sec]];
    }

    public function downloadYearlyReport($request): array
    {
        return $this->generateYearlyReport($request);
    }
    private function fetchQuarterlyExpenditures($quarter,$current_year, $organisation_id): array
    {
        $start_quarter = $this->getStartQuarter($current_year->year, $quarter)[0];
        $end_quarter = $this->getStartQuarter($current_year->year, $quarter)[1];
        $expenses = [];
        $exp_total = 0;
        $expenditure_categories = $this->expenditureCategoryService->getExpenditureCategoriesByOrganisationYear($organisation_id, $current_year->year);
        foreach ($expenditure_categories as $key => $expenditure_category){
            $expenditures_by_cat = array();
            $expenditure_items = $this->expenditureItemService->getExpensesByCategoryAndQuarter($expenditure_category->id, $start_quarter, $end_quarter);
            $total = 0;
            foreach ($expenditure_items as $expenditure_item) {
                $details = $this->expenditureDetailService->findExpenditureDetailsByItemAndQuarter($expenditure_item->id, $start_quarter, $end_quarter);
                $sub_total = collect($details)->sum('amount_spent');
                $expenditure = json_encode(new QuarterlyExpenditureResource(($key + 1), $expenditure_item->name, ($details->toArray()), $sub_total));
                array_push($expenditures_by_cat, json_decode($expenditure));
                $total += $sub_total;
             }
            array_push($expenses, json_decode(json_encode(new QuarterlyExpenditureResourceCollection($expenditure_category->code, $expenditure_category->name,
                $expenditures_by_cat, $total))));
            $exp_total += $total;
        }
        return [$expenses, $exp_total];
    }


    private function fetchQuarterlyIncomes($organisation_id, $quarter, $current_year): array
    {
        $start_quarter = $this->getStartQuarter($current_year->year, (int)$quarter)[0];
        $end_quarter = $this->getStartQuarter($current_year->year, (int)$quarter)[1];
        $payment_categories = $this->paymentCategoryService->getPaymentCategoriesByOrganisationAndYear($organisation_id, $current_year->year);
        $incomes = array();
        $total_reg_saving = 0;
        $total_income =null;
        $member_reg = $this->registrationService->getMemberRegistrationPerQuarter($quarter, $current_year, "MR");
        $savings = $this->userSavingService->getMemberSavingPerQuarter($quarter, $current_year, "MS");
        array_push($incomes, $member_reg, $savings);
        $total_reg_saving += $member_reg->total + $savings->total;
        $quarterly_incomes = array();
        foreach ($payment_categories as $key => $payment_category){
            $payment_items = $this->paymentItemService->getPaymentActivitiesByCategoryAndSession($payment_category->id, $current_year->id);
            $payment_activities = array();
            $payment_category_total = 0;
            foreach ($payment_items as $payment_item_key => $payment_item){
                $payment_incomes = array();
                $supports = $this->activitySupportService->getSponsorshipIncomePerQuarterly($quarter, $current_year, $payment_item);
                $activities = $this->incomeActivityService->getQuarterlyIncomeActivities($quarter, $current_year, $payment_item);
                $user_contribution = $this->userContributionService->getContributionsByItemAndSession($payment_item->id, $start_quarter, $end_quarter);
                $payment_incomes = array_merge($user_contribution, $payment_incomes, $supports, $activities);
                $total = $this->calculateTotal($payment_incomes);

                $payment_activity = json_encode(new QuarterlyIncomeResource($payment_item_key + 1, $payment_item->name, $payment_incomes, $total));

                array_push($payment_activities, json_decode($payment_activity));

                $payment_category_total += $total;

            }
            $payment_category_income = json_encode(new IncomeResource($payment_category->code, $payment_activities,  $payment_category->name, $payment_category_total));
            array_push($incomes, json_decode($payment_category_income));
            $total_income = $payment_category_total + $total_reg_saving;
            array_push($quarterly_incomes, $incomes, $total_income);
         }
        return $quarterly_incomes;
    }

    private function fetchYearIncomes($year_id, $year_label, $organisation_id): array
    {
        $payment_categories = $this->paymentCategoryService->getPaymentCategoriesByOrganisationAndYear($organisation_id, $year_label);
        $incomes = array();
        $total_reg_saving = 0;
        $year_incomes = array();
        $member_reg = $this->registrationService->getMemberRegistrationPerYear($year_id, "MR");
        $savings = $this->userSavingService->getMemberSavingPerYear($year_id, "MS");
        array_push($incomes, $member_reg, $savings);
        $total_reg_saving += $member_reg->total + $savings->total;
        foreach ($payment_categories as $key => $payment_category){
            $payment_items = $this->paymentItemService->getPaymentActivitiesByCategoryAndSession($payment_category->id, $year_id);
            $payment_activities = array();
            $payment_category_total = 0;
            foreach ($payment_items as $payment_item_key => $payment_item){
                $payment_incomes = array();
                $supports = $this->activitySupportService->getSponsorshipIncomePerYear($year_id, $payment_item);
                $activities = $this->incomeActivityService->getYearIncomeActivities($year_id, $payment_item);
                $user_contribution = $this->userContributionService->getContributionsByItemAndYear($payment_item->id, $year_id);
                $payment_incomes = array_merge($user_contribution, $payment_incomes, $supports, $activities);
                $total = $this->calculateTotal($payment_incomes);

                $payment_activity = json_encode(new QuarterlyIncomeResource($payment_item_key + 1, $payment_item->name, $payment_incomes, $total));

                array_push($payment_activities, json_decode($payment_activity));

                $payment_category_total += $total;
            }
            $payment_category_income = json_encode(new IncomeResource($payment_category->code, $payment_activities,  $payment_category->name, $payment_category_total));
            array_push($incomes, json_decode($payment_category_income));
            $total_income = $payment_category_total + $total_reg_saving;
            array_push($year_incomes, $incomes, $total_income);
        }
        return $year_incomes;
    }

    private function fetchYearlyExpenditures($year_id, $organisation_id): array
    {
        $expenses = [];
        $exp_total = 0;
        $expenditure_categories = $this->expenditureCategoryService->getExpenditureCategoriesByOrganisationYear($organisation_id, null);
        foreach ($expenditure_categories as $key => $expenditure_category){
            $expenditures_by_cat = array();
            $expenditure_items = $this->expenditureItemService->getExpensesByCategoryAndYear($expenditure_category->id, $year_id);
            $total = 0;
            foreach ($expenditure_items as $expenditure_item) {
                $details = $this->expenditureDetailService->findExpenditureDetailsByItemAndYear($expenditure_item->id, $year_id);
                $sub_total = collect($details)->sum('amount_spent');
                $expenditure = json_encode(new QuarterlyExpenditureResource(($key + 1), $expenditure_item->name, ($details->toArray()), $sub_total));
                array_push($expenditures_by_cat, json_decode($expenditure));
                $total += $sub_total;
            }
            array_push($expenses, json_decode(json_encode(new QuarterlyExpenditureResourceCollection($expenditure_category->code, $expenditure_category->name,
                $expenditures_by_cat, $total))));
            $exp_total += $total;
        }
        return [$expenses, $exp_total];
    }
}
