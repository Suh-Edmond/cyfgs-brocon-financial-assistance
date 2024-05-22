<?php

namespace App\Http\Controllers;

use App\Constants\PaymentItemFrequency;
use App\Constants\Roles;
use App\Http\Requests\ApproveContributionRequest;
use App\Http\Requests\BulkPaymentRequest;
use App\Http\Requests\CreateUserContributionRequest;
use App\Http\Requests\UpdateUserContributionRequest;
use App\Http\Resources\UserContributionResource;
use App\Models\PaymentItem;
use App\Models\User;
use App\Services\UserContributionService;
use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class UserContributionController extends Controller
{

    use ResponseTrait, HelpTrait;

    private UserContributionService $userContributionService;

    public function __construct(UserContributionService $userContributionService)
    {
        $this->userContributionService = $userContributionService;
    }


    public function createUserContribution(CreateUserContributionRequest $request)
    {
        $this->userContributionService->createUserContribution($request);

        return $this->sendResponse('success', 'Contribution saved successfully');
    }



    public function updateUserContribution(UpdateUserContributionRequest $request,  $id)
    {
       $this->userContributionService->updateUserContribution($request, $id);

       return $this->sendResponse('success', 'Contribution updated successfully');
    }


    public function getUsersContributionsByItem($id, $user_id, Request $request)
    {
        $contributions = $this->userContributionService->getContributionByUserAndItem($id, $user_id, $request);
        return $this->sendResponse(($contributions), 200);
    }


    public function getContributionByUser($id)
    {
        $contributions = $this->userContributionService->getUserContributionsByUser($id);

        return $this->sendResponse($contributions, 200);
    }


    public function getTotalAmountPaidByUserForTheItem($user_id, $id, Request $request)
    {
        $contributions = $this->userContributionService->getTotalAmountPaidByUserForTheItem($user_id, $id, $request->month, $request->quarter, $request->frequency);

        return $this->sendResponse($contributions, 200);
    }


    public function deleteUserContribution($id)
    {
        $this->userContributionService->deleteUserContribution($id);

        return $this->sendResponse( 'success', 'Contribution deleted Successfully');
    }


    public function approveUserContribution(ApproveContributionRequest $request)
    {
        $this->userContributionService->approveUserContribution($request);

        return $this->sendResponse('success', 'Contribution approve successfully');
    }


    public function filterContributions(Request $request)
    {
        $contributions = $this->userContributionService->filterContributions($request);

        return $this->sendResponse(($contributions), 200);
    }


    public function getContribution($id)
    {
        $contribution = $this->userContributionService->getContribution($id);

        return $this->sendResponse(new UserContributionResource($contribution), 200);
    }

    public function getContributionsByPaymentItem($id) {
        $contributions = $this->userContributionService->getContributionsByItem($id);

        return $this->sendResponse($contributions, 200);
    }

    public function downloadFilteredContributions(Request $request) {
        $contributions     = $this->filterContributions($request);
        $contributions     = json_decode(json_encode($contributions))->original->data;
        $organisation      = $request->user()->organisation;
        $paymentItem        = PaymentItem::find($request->payment_item_id);
        $admins            = $this->getOrganisationAdministrators();
        $president         = $admins[Roles::PRESIDENT];
        $treasurer         = $admins[Roles::TREASURER];
        $fin_sec           = $admins[Roles::FINANCIAL_SECRETARY];
//        dd($contributions);
        $data = [
            'title'             => "Member's Contribution for ".$paymentItem->name,
            'date'              => date('m/d/Y'),
            'organisation'      => $organisation,
            'contributions'     => $contributions->data,
            'organisation_telephone'   => $this->setOrganisationTelephone($organisation->telephone),
            'president'         => $president,
            'treasurer'         => $treasurer,
            'fin_secretary'     => $fin_sec,
            'total'             => $contributions->total_amount,
            'balance'           => $contributions->total_balance,
            'organisation_logo' => $organisation->logo,
            'total_deposited_users' => $this->computeTotalAmountByUser($contributions->data),
            'total_balance_users' => $this->computeBalanceByUser($contributions->data),
            'total_amount_payable' => $contributions->total_amount_payable,
            'paymentItem' => $paymentItem,
            'payment_durations' => $contributions->payment_durations
        ];

        $pdf = PDF::loadView('Contribution.UsersContribution', $data);
        $pdf->output();
        $domPdf = $pdf->getDomPDF();
        $canvas = $domPdf->getCanvas();
        $canvas->page_text(10, $canvas->get_height() - 20, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, [0, 0, 0]);

        return $pdf->download('UsersContributions.pdf');
    }


    public function bulkPayment(BulkPaymentRequest $request){
         $this->userContributionService->bulkPayment(json_decode(json_encode($request->all())), $request->user()->name);

        return $this->sendResponse("success", 200);
    }


    public function getMemberOweContributions(Request $request)
    {
        $data = $this->userContributionService->getMemberDebt($request);
        return $this->sendResponse($data, 200);
    }

    public function downloadMemberDebts(Request $request)
    {
        $organisation      = $request->user()->organisation;
        $debts             = json_decode(json_encode($this->userContributionService->getMemberDebt($request)));
        $admins            = $this->getOrganisationAdministrators();
        $president         = $admins[Roles::PRESIDENT];
        $treasurer         = $admins[Roles::TREASURER];
        $fin_sec           = $admins[Roles::FINANCIAL_SECRETARY];
        $member            = User::findOrFail($request->user_id);

        $data = [
            'title'             => $member->name . " Unpaid Payment Items",
            'date'              => date('m/d/Y'),
            'organisation'      => $organisation,
            'contributions'     => $debts,
            'organisation_telephone'   => $this->setOrganisationTelephone($organisation->telephone),
            'president'         => $president,
            'treasurer'         => $treasurer,
            'fin_secretary'     => $fin_sec,
            'total'             => $this->computeTotal($debts, "DEBTS"),
            'balance'           => 0,
            'organisation_logo' => $organisation->logo,
        ];

        $pdf = PDF::loadView('Contribution.MemberDebts', $data);

        $pdf->output();
        $domPdf = $pdf->getDomPDF();
        $canvas = $domPdf->getCanvas();
        $canvas->page_text(10, $canvas->get_height() - 20, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, [0, 0, 0]);

        return $pdf->download('MemberDebts.pdf');
    }

    public function downloadMemberPaidItems(Request $request) {
        $organisation      = $request->user()->organisation;
        $debts             = json_decode(json_encode($this->userContributionService->getMemberContributedItems($request->user_id, $request->session_id)));
        $admins            = $this->getOrganisationAdministrators();
        $president         = $admins[Roles::PRESIDENT];
        $treasurer         = $admins[Roles::TREASURER];
        $fin_sec           = $admins[Roles::FINANCIAL_SECRETARY];
        $member            = User::findOrFail($request->user_id);

        $data = [
            'title'             => $member->name . " Paid Payment Items",
            'date'              => date('m/d/Y'),
            'organisation'      => $organisation,
            'contributions'     => $debts,
            'organisation_telephone'   => $this->setOrganisationTelephone($organisation->telephone),
            'president'         => $president,
            'treasurer'         => $treasurer,
            'fin_secretary'     => $fin_sec,
            'total'             => $this->computeTotal($debts, "PAID"),
            'balance'           => 0,
            'organisation_logo' => $organisation->logo,
        ];

        $pdf = PDF::loadView('Contribution.MemberPaidItems', $data);

        $pdf->output();
        $domPdf = $pdf->getDomPDF();
        $canvas = $domPdf->getCanvas();
        $canvas->page_text(10, $canvas->get_height() - 20, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, [0, 0, 0]);

        return $pdf->download('MemberPaidItems.pdf');
    }
    public function getAllMemberContributions(Request $request)
    {
        $data = $this->userContributionService->getMemberContributedItems($request->user_id, $request->session_id);
        return $this->sendResponse($data, 200);
    }

    public function downloadUserContributions(Request $request) {
        $contributions      = $this->getUsersContributionsByItem($request->payment_item_id, $request->user_id, $request);
        $contributions      = json_decode(json_encode($contributions))->original->data;
        $organisation      = $request->user()->organisation;

        $admins            = $this->getOrganisationAdministrators();
        $president         = $admins[Roles::PRESIDENT];
        $treasurer         = $admins[Roles::TREASURER];
        $fin_sec           = $admins[Roles::FINANCIAL_SECRETARY];

        $data = [
            'title'             => $request->user_name." Contributions for ".$request->payment_item_name,
            'date'              => date('m/d/Y'),
            'organisation'      => $organisation,
            'contributions'     => $contributions->data,
            'organisation_telephone'   => $this->setOrganisationTelephone($organisation->telephone),
            'president'         => $president,
            'treasurer'         => $treasurer,
            'fin_secretary'     => $fin_sec,
            'total'             => $contributions->total_amount,
            'balance'           => $contributions->total_balance,
            'payment_item_name' => $request->payment_item_name,
            'payment_item_amount' => $request->payment_item_amount,
            'payment_item_frequency'   => $request->payment_item_frequency,
            'organisation_logo' => $organisation->logo,
            'unpaid_durations'    => $contributions->unpaid_durations
        ];

        $pdf = PDF::loadView('Contribution.MemberContribution', $data);

        $pdf->output();
        $domPdf = $pdf->getDomPDF();
        $canvas = $domPdf->getCanvas();
        $canvas->page_text(10, $canvas->get_height() - 20, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, [0, 0, 0]);

        return $pdf->download('MemberContributions.pdf');
    }


    public function getYearlyContributions(Request $request)
    {
        $data = $this->userContributionService->getYearlyContributions($request);

        return $this->sendResponse($data, 200);
    }

    public function getContributionStatistics(Request $request)
    {
        $data = $this->userContributionService->getContributionStatistics($request);
        return $this->sendResponse($data, 200);
    }

    private function computeTotal($contributions, $type)
    {
        return collect($contributions)->map(function ($contribution) use ($type) {
            return $type == "DEBTS"? $contribution->item_amount: $contribution->payment_item_amount;
        })->sum();
    }
}
