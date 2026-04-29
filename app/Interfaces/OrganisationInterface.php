<?php

namespace App\Interfaces;

interface OrganisationInterface {

    public function updateOrganisationInfo($request);
    public function getOrganisation($id);
    public function getOrganisationInfo();
    public function updatedOrganisation($request, $id);
    public function deleteOgranisation($id);
    public function getOrganisations();

    public function updateTelephoneNumber($request);
    public function createOrganisationAccount($request);

}
