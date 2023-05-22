<?php


namespace App\Services;


use App\Constants\PaymentStatus;
use App\Interfaces\RegistrationInterface;
use App\Models\User;
use App\Models\MemberRegistration;

class RegistrationService implements RegistrationInterface
{
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
        $exist_user = MemberRegistration::join('users', ['users.id' => 'member_registrations.user_id'])
                    ->join('sessions', ['sessions.id' => 'member_registrations.session_id'])
                    ->where('session_id', $current_session->id)
                    ->where('member_registrations.user_id', $user->id)
                    ->first();
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
        $registrations = MemberRegistration::join('users', ['users.id' => 'member_registrations.user_id'])->where('member_registrations.session_id', $current_session->id);
        if(!is_null($request->status) && $request->status != "ALL"){
            $registrations = $registrations->where('member_registrations.approve', $request->status);
        }
        $registrations = $registrations->orderBy('users.name', 'DESC')->get();

        return $registrations;
    }

    public function deleteRegistration($id)
    {
        return MemberRegistration::findOrFail($id)->delete();
    }

    public function approveRegisteredMember($request)
    {
        $current_session = $this->sessionService->getCurrentSession();
        $reg = MemberRegistration::where('user_id', $request->user_id)->where('session_id', $current_session->id)->firstOrFail();
        $reg->approve = $request->status;
        $reg->save();
    }

}
