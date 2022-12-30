<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserContributionRequest;
use App\Http\Requests\UpdateUserContributionRequest;
use App\Http\Resources\UserContributionResource;
use App\Models\User;
use App\Services\UserContributionService;
use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use PDF;

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


    public function getUsersContributionsByItem($id)
    {
        $contributions = $this->user_contribution_interface->getContributionsByItem($id);

        return $this->sendResponse($contributions, 200);
    }


    public function getContributionByUser($id)
    {
        $contributions = $this->user_contribution_interface->getUserContributionsByUser($id);

        return $this->sendResponse($contributions, 200);
    }


    public function getContributionByUserAndItem($user_id, $id)
    {
        $contributions = $this->user_contribution_interface->getContributionByUserAndItem($id, $user_id);

        return $this->sendResponse($contributions, 200);
    }


    public function deleteUserContributon($id)
    {
        $this->user_contribution_interface->deleteUserContribution($id);

        return $this->sendResponse( 'success', 'Contribution deleted sucessfully');
    }


    public function approveUserContribution($id)
    {
        $this->user_contribution_interface->approveUserContribution($id);

        return $this->sendResponse('success', 'Contribution approve successfully');
    }


    public function filterContribution(Request $request)
    {
        $contributions = $this->user_contribution_interface->filterContribution($request->status, $request->payment_item_id, $request->year, $request->month);

        return $this->sendResponse($contributions, 200);
    }


    public function getContribution($id)
    {
        $contribution = $this->user_contribution_interface->getContribution($id);

        return $this->sendResponse(new UserContributionResource($contribution), 200);
    }

    public function downloadContrition(Request $request)
    {
        $auth_user         = auth()->user();
        $organisation      = User::find($auth_user['id'])->organisation;
        $contributions  = $this->user_contribution_interface->getContributionsByItem($request->payment_item_id);
        $administrators = $this->getOrganisationAdministrators($organisation->users);
        $president      = $administrators[0];
        $treasurer      = $administrators[1];
        $fin_sec        = $administrators[2];
        $total          = $this->computeTotalContribution($contributions);

        $data = [
            'title'             => 'User Contribution for '.$contributions[0]->paymentItem->name,
            'date'              => date('m/d/Y'),
            'organisation'      => $organisation,
            'contributions'     => $contributions,
            'president'         => $president,
            'treasurer'         => $treasurer,
            'fin_secretary'     => $fin_sec,
            'total'             => $total
        ];

        $pdf = PDF::loadView('Contribution.UserContribution', $data);

        return $pdf->download('User_Contributions.pdf');
    }
}
