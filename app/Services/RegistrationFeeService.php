<?php


namespace App\Services;


use App\Constants\SessionStatus;
use App\Interfaces\RegistrationFeeInterface;
use App\Models\Registration;

class RegistrationFeeService implements RegistrationFeeInterface
{

    public function createRegistrationFee($request)
    {
        $exist_fees = Registration::all()->toArray();
        if(count($exist_fees) == 0){
            Registration::create([
                'motive'    => $request->motive,
                'is_compulsory' => $request->is_compulsory,
                'amount'        => $request->amount,
                'status'        => SessionStatus::ACTIVE
            ]);
        }else {
            Registration::create([
                'motive'    => $request->motive,
                'is_compulsory' => $request->is_compulsory,
                'amount'        => $request->amount,
                'status'        => SessionStatus::IN_ACTIVE
            ]);
        }
    }

    public function updateRegistrationFee($request, $id)
    {
        $updated = Registration::findOrFail($id);
        $updated->update([
            'motive'    => $request->motive,
            'is_compulsory' => $request->is_compulsory,
            'amount'        => $request->amount
        ]);
    }

    public function getAllRegistrationFee()
    {
        return Registration::all();
    }

    public function getCurrentRegistrationFee()
    {
        return Registration::where('status', SessionStatus::ACTIVE)->get();
    }

    public function deleteRegistrationFee($id)
    {
        Registration::findOrFail($id)->delete();
    }

    public function setNewFee($id)
    {
        $exist = Registration::where('status', SessionStatus::ACTIVE);
        $exist->update('status', SessionStatus::IN_ACTIVE);
        Registration::findOrFail($id)->update('status', SessionStatus::ACTIVE);
    }
}
