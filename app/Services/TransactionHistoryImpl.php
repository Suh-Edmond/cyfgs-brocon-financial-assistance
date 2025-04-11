<?php

namespace App\Services;

use App\Constants\TransactionDataGroup;
use App\Interfaces\TransactionHistoryInterface;
use App\Models\TransactionHistory;
use App\Traits\HelpTrait;

class TransactionHistoryImpl implements TransactionHistoryInterface
{
    use HelpTrait;
    private UserSavingService $userSavingService;
    private ActivitySupportService $activitySupportService;
    private ExpenditureDetailService $expenditureDetailService;
    private ExpenditureItemService $expenditureItemService;

    private RegistrationService $registrationService;

    private  UserContributionService $userContributionService;

    private IncomeActivityService $incomeActivityService;

    /**
     * @param UserSavingService $userSavingService
     * @param ActivitySupportService $activitySupportService
     * @param ExpenditureDetailService $expenditureDetailService
     * @param ExpenditureItemService $expenditureItemService
     * @param RegistrationService $registrationService
     * @param UserContributionService $userContributionService
     * @param IncomeActivityService $incomeActivityService
     */
    public function __construct(UserSavingService $userSavingService, ActivitySupportService $activitySupportService, ExpenditureDetailService $expenditureDetailService, ExpenditureItemService $expenditureItemService, RegistrationService $registrationService, UserContributionService $userContributionService, IncomeActivityService $incomeActivityService)
    {
        $this->userSavingService = $userSavingService;
        $this->activitySupportService = $activitySupportService;
        $this->expenditureDetailService = $expenditureDetailService;
        $this->expenditureItemService = $expenditureItemService;
        $this->registrationService = $registrationService;
        $this->userContributionService = $userContributionService;
        $this->incomeActivityService = $incomeActivityService;
    }


    public function createTransactionHistory($request)
    {
        $existHistory = TransactionHistory::where('reference_data', $request['reference_data'])->first();
        if(isset($existHistory)){
            $existHistory->old_amount_deposited = $request['old_amount_deposited'];
            $existHistory->new_amount_deposited = $request['new_amount_deposited'];
            $existHistory->reason               = $request['reason'];
            $existHistory->approve              = $request['approve'];
            $existHistory->updated_by           = $request['updated_by'];
        }else {
            $existHistory = TransactionHistory::create([
                'old_amount_deposited'     => $request['old_amount_deposited'],
                'new_amount_deposited'     => $request['new_amount_deposited'],
                'reason'                   => $request['reason'],
                'reference_data'           => $request['reference_data'],
                'updated_by'               => $request->user()->name,
                'approve'                  => $request['approve'],
                'code'                     => $this->generateCode(10),
            ]);
        }

       $updatedTransactionData = $this->getTransactionDataGroup($request['transaction_data_group'], $request['reference_data']);

       return $this->saveTransactionData($existHistory, $request['transaction_data_group'], $updatedTransactionData);
    }

    private function getTransactionDataGroup($transactionDataGroup, $id){
        $transaction = null;
        switch ($transactionDataGroup){
            case TransactionDataGroup::USER_SAVING:
                $transaction = $this->userSavingService->getTransactionData($id);
            break;
            case TransactionDataGroup::USER_REGISTRATION:
                $transaction = $this->registrationService->getTransactionData($id);
            break;
            case TransactionDataGroup::USER_CONTRIBUTIONS:
                $transaction = $this->userContributionService->getTransactionData($id);
            break;
            case TransactionDataGroup::SPONSORSHIP:
                $transaction = $this->activitySupportService->getTransactionData($id);
            break;
            case TransactionDataGroup::INCOME_ACTIVITY:
                $transaction = $this->incomeActivityService->getTransactionData($id);
            break;
            case TransactionDataGroup::EXPENDITURE_ITEMS:
                $transaction = $this->expenditureItemService->getTransactionData($id);
            break;
            case TransactionDataGroup::EXPENDITURE_ITEM_DETAILS:
                $transaction = $this->expenditureDetailService->getTransactionData($id);

        }

        return $transaction;
    }

    private function saveTransactionData($saveTransactionHistory, $transactionDataGroup, $updatedTransactionData){
        $savedData = null;
        switch ($transactionDataGroup){
            case TransactionDataGroup::EXPENDITURE_ITEM_DETAILS:
                $savedData = $this->expenditureDetailService->saveTransactionData($saveTransactionHistory, $updatedTransactionData);
            break;
            case TransactionDataGroup::EXPENDITURE_ITEMS:
                $savedData = $this->expenditureItemService->saveTransactionData($saveTransactionHistory, $updatedTransactionData);
            break;
            case TransactionDataGroup::INCOME_ACTIVITY:
                $savedData = $this->incomeActivityService->saveTransactionData($saveTransactionHistory, $updatedTransactionData);
            break;
            case TransactionDataGroup::SPONSORSHIP:
                $savedData = $this->activitySupportService->saveTransactionData($saveTransactionHistory, $updatedTransactionData);
            break;
            case TransactionDataGroup::USER_SAVING:
                $savedData = $this->userSavingService->saveTransactionData($saveTransactionHistory, $updatedTransactionData);
            break;
            case TransactionDataGroup::USER_CONTRIBUTIONS:
                $savedData = $this->userContributionService->saveTransactionData($saveTransactionHistory, $updatedTransactionData);
            break;
            case TransactionDataGroup::USER_REGISTRATION:
                $savedData = $this->registrationService->saveTransactionData($saveTransactionHistory, $updatedTransactionData);
            break;
        }

        return $savedData;
    }


}
