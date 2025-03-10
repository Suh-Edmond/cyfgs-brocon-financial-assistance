<?php

namespace App\Services;

use App\Constants\Constants;
use App\Constants\Roles;
use App\Http\Requests\GenerateQuarterlyRequest;
use App\Http\Resources\ActivityReportResource;
use App\Http\Resources\DetailResource;
use App\Http\Resources\IncomeResource;
use App\Http\Resources\QuarterlyExpenditureResource;
use App\Http\Resources\QuarterlyExpenditureResourceCollection;
use App\Http\Resources\QuarterlyIncomeResource;
use App\Interfaces\ReportGenerationInterface;
use App\Traits\HelpTrait;
use Carbon\Carbon;

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

        $income_list[] = new ActivityReportResource("Members Contributions", $total_members_contributions);

        $data = array_merge($incomes, $sponsorship);

        foreach ($data as $contribution){
            $total_income += $contribution['amount'];
            $income_list[] = new ActivityReportResource($contribution['name'], $contribution['amount']);
        }
        $total_income += $total_members_contributions;

        $expenses = $this->expenditureDetailService->getExpenditureActivities($id);

        foreach ($expenses[0] as $expense){

            $total_amount_spent += $expense['amount_spent'];

            $total_amount_given += $expense['amount_given'];

            $balance += ($expense['amount_given']- $expense['amount_spent']);

            $expenditures[] = new DetailResource($expense['name'], $expense['amount_given'], $expense['amount_spent'], ($expense['amount_given'] - $expense['amount_spent']));
        }

        $total_balance += $balance > 0 ? ($total_income - $total_amount_spent)  + $balance: ($total_income - $total_amount_spent);

        $admins            = $this->getOrganisationAdministrators();

        return [$income_list, $expenditures, ["total_income" => $total_income], ["total_amount_given" => $total_amount_given],

            ["total_amount_spent" => $total_amount_spent], ["balance" => $balance], ["total_balance" => $total_balance],

            ["president" => $admins[Roles::PRESIDENT]], ["fin_sec" => $admins[Roles::FINANCIAL_SECRETARY]], ["treasurer" => $admins[Roles::TREASURER]]];
    }

    public function generateQuarterlyReport(GenerateQuarterlyRequest $request): array
    {

        $current_year = $this->sessionService->getCurrentSession();

        $incomes = $this->fetchQuarterlyIncomes($request, $current_year, self::$INCOME_ONLY);

        $expenditures = $this->fetchQuarterlyExpenditures($request->quarter,$current_year, $request->organisation_id, self::$INCOME_ONLY);

        $balance_bf = $request->quarter == 6 || $request->quarter == 1? 0: $this->computeBalanceBroughtForwardByQuarter($request, $current_year, self::$BALANCE_BROUGHT_FORWARD);

        $income_elements = $incomes[0];

        $total_income = $incomes[1] + $balance_bf;

        $expenditures_elements = $expenditures[0];

        $total_expenditures = $expenditures[1];

        $admins            = $this->getOrganisationAdministrators();

        return [$income_elements, $expenditures_elements, ["total_income" => $total_income], ["total_expenditure" => $total_expenditures],
            ["balance_brought_forward" => $balance_bf], ["president" => $admins[Roles::PRESIDENT]], ["treasurer" => $admins[Roles::TREASURER]], ["fin_sec" => $admins[Roles::FINANCIAL_SECRETARY]]];
    }

    public function downloadQuarterlyReport($request): array
    {
        return $this->generateQuarterlyReport($request);
    }

    public function computeBalanceBroughtForwardByQuarter($request, $current_year, $type){


        $quarter = (int) $request->quarter > 1 && $type == self::$BALANCE_BROUGHT_FORWARD ? ((int) $request->quarter - 1) : 0;


        $income = $this->fetchQuarterlyIncomes($request, $current_year, $type);

        $expenditure = $this->fetchQuarterlyExpenditures($quarter, $current_year, $request->organisation_id, $type);

        return $income[1] - $expenditure[1];
    }

    public function computeBalanceBroughtForwardByYear($request)
    {
        $balance_brought_forward = 0;
        $previous_year_label = (int)$request->year_label - 1;
        $previous_year = $this->sessionService->getSessionByLabel($previous_year_label);
        if(!isset($previous_year)){
            return $balance_brought_forward;
        }
        $income = $this->fetchYearIncomes($previous_year->id, $previous_year_label, $request->user()->organisation_id)[1];

        $expenditure = $this->fetchYearlyExpenditures($previous_year->id, $request->user()->organisation_id)[1];

        return $income - $expenditure;
    }


    public function generateYearlyReport($request): array
    {

        $income = $this->fetchYearIncomes($request->year_id, $request->year_label, $request->user()->organisation_id);

        $expenditures = $this->fetchYearlyExpenditures($request->year_id, $request->user()->organisation_id);

        $bal_brought_forward = $this->computeBalanceBroughtForwardByYear($request);
        $admins            = $this->getOrganisationAdministrators();
        $income_list = $income[0];
        $total_income = $income[1] + $bal_brought_forward;
        $expenditure_list = $expenditures[0];
        $total_expenditure = $expenditures[1];

        return [$income_list, $expenditure_list, ["total_income" => $total_income], ["total_expenditure" => $total_expenditure],["bal_brought_forward" => $bal_brought_forward],
            ["president" => $admins[Roles::PRESIDENT]], ["treasurer" => $admins[Roles::TREASURER]], ["fin_sec" => $admins[Roles::FINANCIAL_SECRETARY]]];
    }

    public function downloadYearlyReport($request): array
    {
        return $this->generateYearlyReport($request);
    }
    private function fetchQuarterlyExpenditures($quarter,$current_year, $organisation_id, $type): array
    {
        $start_quarter = $this->getStartQuarter($current_year->year, $quarter,$type)[0];

        $end_quarter = $this->getStartQuarter($current_year->year, $quarter, $type)[1];

        $expenses = [];

        $exp_total = 0;

        $expenditure_categories = $this->expenditureCategoryService->getAllExpenditureCategories($organisation_id);

        foreach ($expenditure_categories as $key => $expenditure_category){
            $expenditures_by_cat = array();

            $expenditure_items = $this->expenditureItemService->getExpensesByCategoryAndQuarter($expenditure_category->id, $start_quarter, $end_quarter);

            $total = 0;

            foreach ($expenditure_items as $expenditure_item) {
                $details = $this->expenditureDetailService->findExpenditureDetailsByItemAndQuarter($expenditure_item->id, $start_quarter, $end_quarter);

                $sub_total = collect($details)->sum('amount_spent');

                $expenditure = json_encode(new QuarterlyExpenditureResource(($key + 1), $expenditure_item->name, ($details->toArray()), $sub_total));

                $expenditures_by_cat[] = json_decode($expenditure);

                $total += $sub_total;

             }
            $expenses[] = json_decode(json_encode(new QuarterlyExpenditureResourceCollection($expenditure_category->code, $expenditure_category->name, $expenditures_by_cat, $total)));

            $exp_total += $total;
        }

        return [$expenses, $exp_total];
    }


    private function fetchQuarterlyIncomes(GenerateQuarterlyRequest $request, $current_year, $type): array
    {

        $payment_categories = $this->paymentCategoryService->getPaymentCategoriesByOrganisationAndYear($request->organisation_id, $current_year->year);

        $incomes = array();

        $total_reg_saving = 0;

        $total_income = 0;

        $quarterly_incomes = array();

        $member_reg = $this->registrationService->getMemberRegistrationPerQuarter($request, Constants::MR, $current_year->id, $current_year, $type);


        $savings = $this->userSavingService->getMemberSavingPerQuarter($request, Constants::MS, $current_year->id, $type, $current_year);

        array_push($incomes, $member_reg, $savings);

        $total_reg_saving += $member_reg->total + $savings->total;

        foreach ($payment_categories as $key => $payment_category){

            $session_payment_items = $this->paymentItemService->getPaymentActivitiesByCategoryAndSessionAndQuarter($payment_category->id, $request, $current_year, $type);

            $payment_activities = array();

            $payment_category_total = 0;

            for ($i = 0; $i < count($session_payment_items); $i++) {
                $payment_incomes = array();
                $supports = $this->activitySupportService->getSponsorshipIncomePerQuarterly($current_year, $request, $type, $session_payment_items[$i]);

                $activities = $this->incomeActivityService->getQuarterlyIncomeActivities($current_year, $session_payment_items[$i], $request, $type);

                $user_contribution = $this->userContributionService->getContributionsByItemAndSession($session_payment_items[$i]['id'], $request, $current_year, $type);

                $payment_incomes = array_merge($user_contribution, $payment_incomes, $supports, $activities);

                $total = $this->calculateTotal($payment_incomes);

                $payment_activity = json_encode(new QuarterlyIncomeResource($i + 1, $session_payment_items[$i]['name'], $payment_incomes, $total));

                $payment_activities[] = json_decode($payment_activity);

                $payment_category_total += $total;


            }

            if(count($session_payment_items) !== 0){

                $payment_category_income = json_encode(new IncomeResource($payment_category->code, $payment_activities,  $payment_category->name, $payment_category_total));
                $incomes[] = json_decode($payment_category_income);

                $total_income = $payment_category_total + $total_reg_saving;
            }

        }

        array_push($quarterly_incomes, $incomes, $total_income);

        return $quarterly_incomes;
    }

    private function fetchYearIncomes($year_id, $year_label, $organisation_id): array
    {

        $payment_categories = $this->paymentCategoryService->getPaymentCategoriesByOrganisationAndYear($organisation_id, $year_label);
        $incomes = array();
        $total_reg_saving = 0;
        $year_incomes = array();

        $member_reg = $this->registrationService->getMemberRegistrationPerYear($year_id, Constants::MR);

        $savings = $this->userSavingService->getMemberSavingPerYear($year_id, Constants::MS);
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

                $payment_activities[] = json_decode($payment_activity);

                $payment_category_total += $total;
            }
            $payment_category_income = json_encode(new IncomeResource($payment_category->code, $payment_activities,  $payment_category->name, $payment_category_total));
            $incomes[] = json_decode($payment_category_income);
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
            foreach ($expenditure_items as $k => $expenditure_item) {
                $details = $this->expenditureDetailService->findExpenditureDetailsByItemAndYear($expenditure_item->id, $year_id);
                $sub_total = collect($details)->sum('amount_spent');
                $expenditure = json_encode(new QuarterlyExpenditureResource(($k + 1), $expenditure_item->name, ($details->toArray()), $sub_total));
                $expenditures_by_cat[] = json_decode($expenditure);
                $total += $sub_total;
            }
            $expenses[] = json_decode(json_encode(new QuarterlyExpenditureResourceCollection($expenditure_category->code, $expenditure_category->name,
                $expenditures_by_cat, $total)));
            $exp_total += $total;
        }
        return [$expenses, $exp_total];
    }
}
