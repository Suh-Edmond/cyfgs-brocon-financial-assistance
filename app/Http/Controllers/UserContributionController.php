<?php

namespace App\Http\Controllers;

use App\Constants\Roles;
use App\Http\Requests\BulkPaymentRequest;
use App\Http\Requests\CreateUserContributionRequest;
use App\Http\Requests\UpdateUserContributionRequest;
use App\Http\Resources\UserContributionResource;
use App\Models\User;
use App\Services\UserContributionService;
use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class UserContributionController extends Controller
{

    use ResponseTrait, HelpTrait;

    private UserContributionService $user_contribution_interface;

    public function __construct(UserContributionService $user_contribution_interface)
    {
        $this->user_contribution_interface = $user_contribution_interface;
    }


    public function createUserContribution(CreateUserContributionRequest $request)
    {
        $this->user_contribution_interface->createUserContribution($request);

        return $this->sendResponse('success', 'Contribution saved successfully');
    }



    public function updateUserContribution(UpdateUserContributionRequest $request,  $id)
    {
       $this->user_contribution_interface->updateUserContribution($request, $id);

       return $this->sendResponse('success', 'Contribution updated successfully');
    }


    public function getUsersContributionsByItem($id, $user_id)
    {
        $contributions = $this->user_contribution_interface->getContributionByUserAndItem($id, $user_id);

        return $this->sendResponse(UserContributionResource::collection($contributions), 200);
    }


    public function getContributionByUser($id)
    {
        $contributions = $this->user_contribution_interface->getUserContributionsByUser($id);

        return $this->sendResponse($contributions, 200);
    }


    public function getTotalAmountPaidByUserForTheItem($user_id, $id)
    {
        $contributions = $this->user_contribution_interface->getTotalAmountPaidByUserForTheItem($user_id, $id);

        return $this->sendResponse($contributions, 200);
    }


    public function deleteUserContributon($id)
    {
        $this->user_contribution_interface->deleteUserContribution($id);

        return $this->sendResponse( 'success', 'Contribution deleted sucessfully');
    }


    public function approveUserContribution($id, Request $request)
    {
        $this->user_contribution_interface->approveUserContribution($id, $request->type);

        return $this->sendResponse('success', 'Contribution approve successfully');
    }


    public function filterContributions(Request $request)
    {
        $contributions = $this->user_contribution_interface->filterContributions($request);

        return $this->sendResponse(UserContributionResource::collection($contributions), 200);
    }




    public function getContribution($id)
    {
        $contribution = $this->user_contribution_interface->getContribution($id);

        return $this->sendResponse(new UserContributionResource($contribution), 200);
    }

    public function getContributionsByPaymentItem($id) {
        $contributions = $this->user_contribution_interface->getContributionsByItem($id);

        return $this->sendResponse($contributions, 200);
    }

    public function downloadFilteredContributions(Request $request) {
        $contributions     = $this->filterContributions($request);
        $contributions     = json_decode(json_encode($contributions))->original->data;
        $this->downloadContribution($contributions);
    }


    public function bulkPayment(BulkPaymentRequest $request){
        $this->validate($request, [
            'row.*.user_id'         => 'required|string',
            'row.*.payment_item_id' => 'required|string',
            'row.*.comment'         => 'string|required',
            'row.*.year'            => 'required|string',
            'row.*.amount_deposited'  => 'required|numeric',
            'row.*.type'            => 'required|string',
            'row.*.is_compulsory'   => 'required|string'
        ]);
        $this->user_contribution_interface->bulkPayment($request->all());
        return $this->sendResponse("success", 200);
    }


    public function getMemberOweContributions(Request $request)
    {
        $data = $this->user_contribution_interface->getMemberDebt($request->user_id, $request->year);
        return $this->sendResponse($data, 200);
    }


    public function getAllMemberContributions(Request $request)
    {
        $data = $this->user_contribution_interface->getMemberContributedItems($request->user_id, $request->year);
        return $this->sendResponse($data, 200);
    }

    public function downloadUserContributions(Request $request) {
        $contributions      = $this->getUsersContributionsByItem($request->payment_item_id, $request->user_id);
        $contributions      = json_decode(json_encode($contributions))->original->data;
        $this->downloadContribution($contributions);
    }


    public function downloadContribution($contributions)
    {

        $auth_user         = auth()->user();
        $organisation      = User::find($auth_user['id'])->organisation;

        $president         = $this->getOrganisationAdministrators(Roles::PRESIDENT);
        $treasurer         = $this->getOrganisationAdministrators(Roles::TREASURER);
        $fin_sec           = $this->getOrganisationAdministrators(Roles::FINANCIAL_SECRETARY);
        $total             = $this->computeTotalContribution($contributions);

        $data = [
            'title'             => 'User Contribution for '.$contributions[0]->payment_item_name,
            'date'              => date('m/d/Y'),
            'organisation'      => $organisation,
            'contributions'     => $contributions,
            'organisation_telephone'   => $this->setOrganisationTelephone($organisation->telephone),
            'president'         => $president,
            'treasurer'         => $treasurer,
            'fin_secretary'     => $fin_sec,
            'total'             => $total
        ];

        $pdf = PDF::loadView('Contribution.UserContribution', $data);

        return $pdf->download('User_Contributions.pdf');
    }
}
