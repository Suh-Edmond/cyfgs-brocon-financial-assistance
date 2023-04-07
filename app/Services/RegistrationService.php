<?php


namespace App\Services;


use App\Constants\PaymentStatus;
use App\Interfaces\RegistrationInterface;
use App\Models\PaymentItem;
use App\Models\User;
use App\Models\MemberRegistration;

class RegistrationService implements RegistrationInterface
{

    public function addRegistration($request)
    {
        $user = User::findOrFail($request->user_id);
        $payment_item = PaymentItem::findOrFail($request->payment_item_id);
        $exist_user = MemberRegistration::where('user_id', $user->id)->where('year', $request->year)->get()->toArray();
        if(count($exist_user)){
            MemberRegistration::create([
                'user_id'           => $user->id,
                'year'              => $request->year,
                'payment_item_id'   => $payment_item->id,
                'updated_by'        => $request->user()->name
            ]);
        }else {
            $exist_user[0]->approve = PaymentStatus::PENDING;
            $exist_user[0]->save();
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
