<?php


namespace App\Services;


use App\Interfaces\RegistrationInterface;
use App\Models\User;
use App\Models\MemberRegistration;
use Illuminate\Support\Facades\DB;

class RegistrationService implements RegistrationInterface
{

    public function addRegistration($request)
    {
        $user = User::findOrFail($request->user_id);
        MemberRegistration::create([
            'user_id'     => $user->id,
            'year'        => $request->year,
            'amount'      => $request->amount,
            'updated_by'  => $request->user()->name
        ]);
    }

    public function updatedRegistration($request)
    {
        $savedReg = MemberRegistration::findOrFail($request->user_id);
        $savedReg->update([
            'year'   => $request->year,
            'amount' => $request->amount
        ]);
    }

    public function getRegistrations($request)
    {
        $registrations = MemberRegistration::join('users', ['users.id' => 'member_registrations.user_id'])
                            ->where('member_registrations.year', $request->year);
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
        $reg = MemberRegistration::where('user_id', $request->user_id)->where('year', $request->year)->firstorFail();
        $reg->approve = $request->status;
        $reg->save();
    }

}
