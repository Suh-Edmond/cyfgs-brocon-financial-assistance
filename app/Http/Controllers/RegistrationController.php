<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrationFeeRequest;
use App\Http\Resources\RegisterFeeResource;
use App\Services\RegistrationFeeService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    use ResponseTrait;
    private RegistrationFeeService $registration_fee_service;

    public function __construct(RegistrationFeeService  $registrationService)
    {
        $this->registration_fee_service = $registrationService;
    }

    public function createRegFee(RegistrationFeeRequest $request)
    {
        $this->registration_fee_service->createRegistrationFee($request);
        return $this->sendResponse('success', 'Registration fee created successfully');
    }

    public function updateRegFee(RegistrationFeeRequest $request, $id)
    {
        $this->registration_fee_service->updateRegistrationFee($request, $id);
        return $this->sendResponse('success', 'Registration fee created successfully');
    }

    public function getAllRegistrationFee(Request $request)
    {
        $data = $this->registration_fee_service->getAllRegistrationFee($request);
        return $this->sendResponse( $data, 200);
    }

    public function getCurrentRegistrationFee()
    {
        $data =$this->registration_fee_service->getCurrentRegistrationFee();
        return $this->sendResponse(new RegisterFeeResource($data), 200);
    }

    public function deleteRegistrationFee($id)
    {
        $this->registration_fee_service->deleteRegistrationFee($id);
        return $this->sendResponse('success', 'Registration fee deleted successfully');
    }

    public function setNewFee($id)
    {
        $this->registration_fee_service->setNewFee($id);
        return $this->sendResponse('success', 'Registration fee set successfully');
    }
}
