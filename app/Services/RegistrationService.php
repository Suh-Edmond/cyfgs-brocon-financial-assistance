<?php


namespace App\Services;


use App\Constants\Constants;
use App\Constants\PaymentStatus;
use App\Http\Resources\QuarterlyIncomeResource;
use App\Interfaces\RegistrationInterface;
use App\Models\User;
use App\Models\MemberRegistration;
use App\Traits\HelpTrait;

class RegistrationService implements RegistrationInterface
{
    use HelpTrait;
    private SessionService $sessionService;
    private RegistrationFeeService  $registrationFeeService;

    public function __construct(SessionService $sessionService, RegistrationFeeService $registrationFeeService)
    {
        $this->sessionService = $sessionService;
        $this->registrationFeeService = $registrationFeeService;
    }


    public function addRegistration($request)
    {
        $current_session = $this->sessionService->getCurrentSession();
        $user = User::findOrFail($request->user_id);
        $exist_user = MemberRegistration::where('session_id', $current_session->id)->where('user_id', $user->id)->first();
        $reg_fee = $this->registrationFeeService->getCurrentRegistrationFee();
        if(is_null($exist_user)){
            MemberRegistration::create([
                'user_id'           => $user->id,
                'session_id'        => $current_session->id,
                'updated_by'        => $request->user()->name,
                'month_name'        => $request->month_name,
                'registration_id'   => $reg_fee->id
            ]);
        }else {
            $exist_user->approve = PaymentStatus::PENDING;
            $exist_user->save();
        }
    }

    public function updatedRegistration($request)
    {
        $savedReg = MemberRegistration::findOrFail($request->user_id);
        $savedReg->update([
            'year'   => $request->year,
        ]);
    }

    public function getRegistrations($request)
    {
        $current_session = $this->sessionService->getCurrentSession();
        $registrations = MemberRegistration::where('session_id', $current_session->id);
        if(!is_null($request->status) && $request->status != "ALL"){
            $registrations = $registrations->where('member_registrations.approve', $request->status);
        }
        return $registrations->orderBy('users.name', 'DESC')->get();
    }

    public function deleteRegistration($id)
    {
        return MemberRegistration::findOrFail($id)->delete();
    }

    public function approveRegisteredMember($request)
    {
        $reg = MemberRegistration::where('user_id', $request->user_id)->where('session_id', $request->session_id)->firstOrFail();
        $reg->approve = $request->status;
        $reg->save();
    }

    public function getMemberRegistrationPerQuarter($request, $code, $session_id, $current_year, $type)
    {
        $quarter_range = $this->getStartQuarter($current_year->year,  $request->quarter, $type);
        $start_quarter = $quarter_range[0];
        $end_quarter = $quarter_range[1];
        $registrations = MemberRegistration::where('session_id', $session_id)
                        ->where('approve', PaymentStatus::APPROVED)
                        ->whereBetween('created_at', [$start_quarter, $end_quarter])
                        ->get();
        $totalReg = collect($registrations)->map(function ($e) {
            return $e->registration->amount;
        })->sum();

        return new QuarterlyIncomeResource($code, Constants::MEMBERS_REGISTRATION, [], $totalReg);
    }

    public function getMemberRegistrationPerYear($year, $code)
    {
        $registrations = MemberRegistration::where('session_id', $year)
            ->where('approve', PaymentStatus::APPROVED)
            ->get();

        $totalReg = collect($registrations)->map(function ($e) {
            return $e->registration->amount;
        })->sum();

        return new QuarterlyIncomeResource($code, Constants::MEMBERS_REGISTRATION, [], $totalReg);
    }

    public function getMemberRegistration($session_id, $user_id){
        $registrations = MemberRegistration::where('session_id', $session_id)
            ->where('approve', PaymentStatus::APPROVED)
            ->where('user_id', $user_id)
            ->get();

        return collect($registrations)->map(function ($e) {
            return $e->registration->amount;
        })->sum();
    }

}
