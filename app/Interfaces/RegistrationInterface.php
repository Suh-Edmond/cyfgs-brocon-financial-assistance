<?php

namespace App\Interfaces;

interface RegistrationInterface {

    public function addRegistration($request);

    public function updatedRegistration($request);

    public function getRegistrations($request);

    public function deleteRegistration($id);

    public function approveRegisteredMember($request);
}
