<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrganisationRequest;
use App\Http\Requests\UpdateOrganisationRequest;
use App\Http\Resources\OrganisationResource;
use App\Services\OrganisationService;

class OrganisationController extends Controller
{
    private OrganisationService $organisation_service;

    public function __construct(OrganisationService $organisation_service)
    {
        $this->organisation_service = $organisation_service;
    }



    public function createOrganisation(CreateOrganisationRequest $request)
    {
        $data = $this->organisation_service->createOrganisation($request);

        return $this->sendResponse($data, 'Organisation created successfully' );
    }


    public function getOrganisation($id)
    {
        $organisation = $this->organisation_service->getOrganisation($id);

        return $this->sendResponse(new OrganisationResource($organisation), 'success');
    }




    public function getOrganisationInfo()
    {
        $organisation = $this->organisation_service->getOrganisationInfo();

        return $this->sendResponse(new OrganisationResource($organisation), 'success');
    }


    public function getOrganisations()
    {
        $organisations = $this->organisation_service->getOrganisations();

        return $this->sendResponse(OrganisationResource::collection($organisations), 'success');
    }


    public function updateOrganisation(UpdateOrganisationRequest $request, $id)
    {
        $data = $this->organisation_service->updatedOrganisation($request, $id);

        return $this->sendResponse( $data, 'successfully updated organisation');
    }


    public function deleteOrganisation($id)
    {
        $this->organisation_service->deleteOgranisation($id);

        return $this->sendResponse('success','successfully deleted organisation');
    }
}
