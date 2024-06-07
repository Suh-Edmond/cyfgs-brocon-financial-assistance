<?php

namespace App\Services;
use App\Http\Resources\BalanceSheetResource;
use App\Http\Resources\MemberInfoResource;
use App\Http\Resources\MemberPaymentItemContributionResource;
use App\Http\Resources\MemberPaymentItemResource;
use App\Http\Resources\MemberYearlyPaymentResource;
use App\Http\Resources\SessionResource;
use App\Interfaces\BalanceSheetInterface;
use App\Traits\HelpTrait;


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
        $session = $this->sessionService->getSessionById($request->session_id);
        $registration = $this->registrationFeeService->getCurrentRegistrationFee();
        $members = $this->userManagementService->getUsers($request->organisation_id);
        $membersYearlyPayments = array();
        $payment_items = $this->paymentItemService->getPaymentItemsForBalanceSheet($session->id);
        $total_year_contributions = 0;
        $total_year_balance = 0;
        $total_year_expected_amount = 0;
        foreach ($members as $key => $member){
            $memberPayments = array();
            $total_member_year_contribution = 0;
            $registration_amount = $this->registrationService->getMemberRegistration($session->id, $member->id);
            $savings = $this->userSavingService->getTotalYearlyMemberSavings($session->id, $member->id)->balance_saving;
            $balance_saving = $savings ?? 0;
            $total_member_year_contribution += ($registration_amount);
            $memberPayments[] = new MemberPaymentItemContributionResource($registration, $registration->id, "Registration", $registration_amount, $registration->amount, ($registration->amount - $registration_amount), "MR");
            $memberPayments[] = new MemberPaymentItemContributionResource($registration, $member->id, "Savings", $balance_saving, 0, 0, "MS");
            foreach ($payment_items as $k => $payment_item){
                $amount_per_item = $this->userContributionService->getContributionByUserAndPaymentItem($payment_item->id, $member->id)->sum('user_contributions.amount_deposited');
                    $memberPayments[] = new MemberPaymentItemContributionResource($payment_item, $payment_item->id, $payment_item->name, $amount_per_item, $payment_item->amount, ($payment_item->amount - $amount_per_item),
                        $payment_item->paymentCategory->code . '.' . $k);
                $total_member_year_contribution += $amount_per_item;
            }
            $total_year_contributions += $total_member_year_contribution;
            $perMemberExpectedAmount = $this->computeTotalExpectedContribution($payment_items);
            $total_member_year_balance =$perMemberExpectedAmount - $total_member_year_contribution;
            $total_year_balance += $total_member_year_balance;
            $total_year_expected_amount += $perMemberExpectedAmount;
            $membersYearlyPayments[$member->name] =  (new MemberYearlyPaymentResource($member, $memberPayments, $total_member_year_contribution, $total_member_year_balance, new MemberInfoResource($member, $member->id, $member->name, $member->email), $perMemberExpectedAmount));
        }
        return new BalanceSheetResource(null, $membersYearlyPayments, $total_year_expected_amount, $total_year_contributions, $total_year_balance, new SessionResource($session));
    }

    public function downloadBalanceSheet($request)
    {
        return ($this->generateBalanceSheet($request));
    }

    private function computeTotalExpectedContribution($payment_items)
    {
        $total = 0;
        foreach ($payment_items as $payment_item){
            $total += $payment_item->amount;
        }

        return $total;
    }
}
