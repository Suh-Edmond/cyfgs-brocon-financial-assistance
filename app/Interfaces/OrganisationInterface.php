<?php

namespace App\Interfaces;

interface OrganisationInterface {

    public function createOrganisation();

    public function getOrganisation($code);

    public function updateOrganisation($code);

}
