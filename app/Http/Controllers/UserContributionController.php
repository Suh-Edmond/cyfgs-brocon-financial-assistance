<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserContributionRequest;
use App\Http\Requests\UpdateUserContributionRequest;
use App\Http\Resources\UserContributionResource;
use App\Interfaces\UserContributionInterface;

class UserContributionController extends Controller
{

    private $user_contribution_interface;

    public function __construct(UserContributionInterface $user_contributio_interface)
    {
        $this->user_contribution_interface = $user_contributio_interface;
    }


    public function createUserContribution(CreateUserContributionRequest $request)
    {
        $this->user_contribution_interface->createUserContribution($request);

        return response()->json(['message' => 'success', 'status' => 'ok'], 201);
    }



    public function updateUserContribution(UpdateUserContributionRequest $request,  $id)
    {
       $this->user_contribution_interface->updateUserContribution($request, $id);

       return response()->json(['message' => 'success', 'status' => 'ok'], 202);
    }


    public function getUserContributionsByItem($id)
    {
        $contributions = $this->user_contribution_interface->getUserContributionsByItem($id);

        return response()->json(['data' => UserContributionResource::collection($contributions), 'status' => 'ok'], 200);
    }


    public function getContributionByUser($id)
    {
        $contributions = $this->user_contribution_interface->getUserContributionsByUser($id);

        return response()->json(['data' => UserContributionResource::collection($contributions), 'status' => 'ok'], 200);
    }


    public function getContributionByUserAndItem($item_id, $user_id)
    {
        $contributions = $this->user_contribution_interface->getContributionByUserAndItem($item_id, $user_id);

        return response()->json(['data' => UserContributionResource::collection($contributions), 'status' => 'ok'], 200);
    }


    public function deleteUserContributon($id)
    {
        $this->user_contribution_interface->deleteUserContribution($id);

        return response()->json(['message' => 'success', 'status' => 'ok'], 204);
    }


    public function approveUserContribution($id)
    {
        $this->user_contribution_interface->approveUserContribution($id);

        return response()->json(['message' => 'success', 'status' => 'ok'], 204);
    }


    public function filterContribution($payment_item_id, $status)
    {
        $contributions = $this->user_contribution_interface->filterContribution($payment_item_id, $status);

        return response()->json(['data' => UserContributionResource::collection($contributions), 'status' => 'ok'], 200);
    }


    public function getContribution($id)
    {
        $contribution = $this->user_contribution_interface->getContribution($id);

        return response()->json(['data' => new UserContributionResource($contribution), 'status' => 'ok'], 200);
    }
}
