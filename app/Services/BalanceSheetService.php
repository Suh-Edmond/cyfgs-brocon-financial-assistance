<?php

namespace App\Services;
use App\Constants\PaymentItemFrequency;
use App\Constants\PaymentItemType;
use App\Constants\Roles;
use App\Http\Resources\BalanceSheetColumns;
use App\Http\Resources\BalanceSheetResource;
use App\Http\Resources\MemberInfoResource;
use App\Http\Resources\MemberPaymentItemContributionResource;
use App\Http\Resources\MemberPaymentItemResource;
use App\Http\Resources\MemberYearlyPaymentResource;
use App\Http\Resources\SessionResource;
use App\Interfaces\BalanceSheetInterface;
use App\Traits\HelpTrait;
use Carbon\Carbon;


class BalanceSheetService implements BalanceSheetInterface {
    use HelpTrait;
    private SessionService $sessionService;
    private PaymentItemService $paymentItemService;
    private UserManagementService $userManagementService;
    private UserContributionService $userContributionService;
    private RegistrationService $registrationService;
    private RegistrationFeeService $registrationFeeService;
    private UserSavingService $userSavingService;

    public function __construct(SessionService $sessionService, PaymentItemService $paymentItemService,
                                UserManagementService $userManagementService, UserContributionService $contributionService,
                                RegistrationService $registrationService,RegistrationFeeService $registrationFeeService,
                                UserSavingService $userSavingService)
    {
        $this->sessionService = $sessionService;
        $this->paymentItemService = $paymentItemService;
        $this->userManagementService = $userManagementService;
        $this->userContributionService = $contributionService;
        $this->registrationService = $registrationService;
        $this->registrationFeeService = $registrationFeeService;
        $this->userSavingService = $userSavingService;
    }

    public function generateBalanceSheet($request)
    {
        $admins            = $this->getOrganisationAdministrators();

        $session = $this->sessionService->getSessionById($request->session_id);

        $registration = $this->registrationFeeService->getCurrentRegistrationFee();

        $members = $this->userManagementService->getUsers($request->organisation_id);

        $membersYearlyPayments = array();

        $payment_items = $this->paymentItemService->getPaymentItemsForBalanceSheet($session->id);

        $total_year_contributions = 0;

        $total_year_balance = 0;

        $total_year_expected_amount = 0;

        $column_names = array();


        foreach ($members as $key => $member){
            $memberPayments = array();

            $total_member_year_contribution = 0;

            $registration_amount = $this->registrationService->getMemberRegistration($session->id, $member->id);

            $savings = $this->userSavingService->getTotalYearlyMemberSavings($session->id, $member->id)->balance_saving;

            $balance_saving = $savings ?? 0;

            $total_member_year_contribution += ($registration_amount);

            $memberPayments[] = new MemberPaymentItemContributionResource($registration, $registration->id, "Registration", $registration_amount, $registration->amount, ($registration->amount - $registration_amount), "MR", $registration->is_compulsory,
                PaymentItemType::ALL_MEMBERS, $registration->frequency, $this->getPaymentDurations($registration, "REGISTRATION"), $registration->created_at, "" );

            $memberPayments[] = new MemberPaymentItemContributionResource($registration, $member->id, "Savings", $balance_saving, 0, 0, "MS", false, "SAVINGS", "None", [], Carbon::now(), "");

            foreach ($payment_items as $k => $payment_item){

                $amount_per_item = $this->userContributionService->getApprovedContributionByUserAndPaymentItem($payment_item->id, $member->id)->sum('user_contributions.amount_deposited');

                    $memberPayments[] = new MemberPaymentItemContributionResource($payment_item, $payment_item->id, $payment_item->name, $amount_per_item, $payment_item->amount, ($payment_item->amount - $amount_per_item),
                        $payment_item->paymentCategory->code . '.' . $k, $payment_item->compulsory, $payment_item->type, $payment_item->frequency, $this->getPaymentDurations($payment_item, "PAYMENTS"), $payment_item->created_at, $payment_item->reference);

                    $total_member_year_contribution += $amount_per_item;
            }
            $total_year_contributions += $total_member_year_contribution;

            $perMemberExpectedAmount = $this->computeTotalExpectedAmountByMemberAndPaymentItem($payment_items, $member);

            $total_member_year_balance = $perMemberExpectedAmount - $total_member_year_contribution;

            $total_year_balance += $total_member_year_balance;

            $total_year_expected_amount += $perMemberExpectedAmount;

            $membersYearlyPayments[] =  (new MemberYearlyPaymentResource($member, $memberPayments, $total_member_year_contribution, $total_member_year_balance, new MemberInfoResource($member, $member->id, $member->name, $member->email), $perMemberExpectedAmount));
        }
        $column_names = $this->getColumnNameForBalanceSheet($membersYearlyPayments);

        return new BalanceSheetResource(null, $membersYearlyPayments, $total_year_expected_amount,
            $total_year_contributions, $total_year_balance, new SessionResource($session), $column_names, $admins[Roles::PRESIDENT], $admins[Roles::TREASURER], $admins[Roles::FINANCIAL_SECRETARY]);
    }

    public function downloadBalanceSheet($request)
    {
        return ($this->generateBalanceSheet($request));
    }

    private function computeTotalExpectedAmountByMemberAndPaymentItem($payment_items, $member)
    {
        $total = 0;
        foreach ($payment_items as $payment_item){
            $total += $this->computeExpectedAmountByMemberAndPaymentItem($payment_item, $member);
        }
        return $total;
    }

    private function getColumnNameForBalanceSheet($membersYearlyPayments)
    {
        $columns = array();
        $contributions = collect(json_decode(json_encode($membersYearlyPayments)))->map(function ($payment){
            return $payment->contributions;
        })->toArray();
        $allContributions = array();

        for ($i = 0; $i < count($contributions); $i++) {
            $allContributions =  array_merge($allContributions, $contributions[$i]);
        }
        $groupedContributions = (collect($allContributions)->groupBy('name'));
        $contributionsKeys = $groupedContributions->keys();
        foreach ($contributionsKeys as $key){
            $total = ($groupedContributions[$key]->sum('amount_deposited'));
            $payment_item = $groupedContributions[$key][0];
            $expected_data = $this->computeTotalExpectedPaymentItemAmount($payment_item);
            $columns[] = new BalanceSheetColumns($payment_item, $payment_item->code, $payment_item->name, $payment_item->id,
                $payment_item->amount, $payment_item->compulsory, $payment_item->type, $payment_item->frequency,
                $payment_item->payment_durations, $expected_data[0], $expected_data[1], $total);
        }

        return $columns;
    }

    private function getPaymentDurations($payment_item, $flag)
    {
        $payment_item_durations = array();
        if ($payment_item->frequency == PaymentItemFrequency::QUARTERLY && $flag != "REGISTRATION"){
            $payment_item_durations = $this->getPaymentItemQuartersBySession($payment_item->frequency, $payment_item->created_at);
        }
        if ($payment_item->frequency == PaymentItemFrequency::MONTHLY && $flag != "REGISTRATION"){
            $payment_item_durations = $this->getPaymentItemMonthsBySession($payment_item->frequency, $payment_item->created_at);
        }

        return $payment_item_durations;
    }
}
