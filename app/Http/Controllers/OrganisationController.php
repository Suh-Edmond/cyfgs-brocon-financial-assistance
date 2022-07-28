<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrganisationRequest;
use App\Http\Requests\UpdateOrganisationRequest;
use App\Services\OrganisationService;
use App\Http\Resources\OrganisationResource;

class OrganisationController extends Controller
{
    private $organisation_service;

    public function __construct(OrganisationService $organisation_service)
    {
        $this->organisation_service = $organisation_service;
    }



    public function createOrganisation(CreateOrganisationRequest $request)
    {
        $this->organisation_service->createOrganisation($request);

        return response()->json(['message' => 'success', 'status' => '201'], 201);
    }


    public function getOrganisation($id)
    {
        $organisation = $this->organisation_service->getOrganisation($id);

        return response()->json(['data' => new OrganisationResource($organisation)], 200);
    }




    public function getOrganisationInfo()
    {
        $organisation = $this->organisation_service->getOrganisationInfo();

        return response()->json(['data' => new OrganisationResource($organisation)], 200);
    }


    public function getOrganisations()
    {
        $organisations = $this->organisation_service->getOrganisations();

        return response()->json(['data' => OrganisationResource::collection($organisations)], 200);
    }


    public function updateOgransation(UpdateOrganisationRequest $request, $id)
    {
        $this->organisation_service->updatedOrganisation($request, $id);

        return response()->json(['message' => 'success', 'status' => '204'], 204);
    }


    public function deleteOrganisation($id)
    {
        $this->organisation_service->deleteOgranisation($id);

        return response()->json(['message' => 'success', 'status' => '204'], 204);
    }
}
