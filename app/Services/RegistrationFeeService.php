<?php


namespace App\Services;


use App\Constants\SessionStatus;
use App\Http\Resources\RegisterFeeResource;
use App\Http\Resources\RegistrationFeeResourceCollection;
use App\Interfaces\RegistrationFeeInterface;
use App\Models\Registration;
use Illuminate\Support\Facades\DB;

class RegistrationFeeService implements RegistrationFeeInterface
{

    public function createRegistrationFee($request)
    {
        $exist_fees = Registration::all()->toArray();
        if(count($exist_fees) == 0){
            Registration::create([
                'is_compulsory' => $request->is_compulsory,
                'amount'        => $request->amount,
                'status'        => SessionStatus::ACTIVE,
                'frequency'     => $request->frequency,
                'updated_by'    => $request->user()->name
            ]);
        }else {
            Registration::create([
                'is_compulsory' => $request->is_compulsory,
                'amount'        => $request->amount,
                'status'        => SessionStatus::IN_ACTIVE,
                'frequency'     => $request->frequency,
                'updated_by'    => $request->user()->name
            ]);
        }
    }

    public function updateRegistrationFee($request, $id)
    {
        $activeRegFee = Registration::where('status', SessionStatus::ACTIVE)->first();
        if(SessionStatus::ACTIVE == $request->status){
            $activeRegFee->update([
                'status' => SessionStatus::IN_ACTIVE
            ]);
        }
        $updated = Registration::findOrFail($id);
        $updated->update([
            'is_compulsory' => $request->is_compulsory,
            'amount'        => $request->amount,
            'frequency'     => $request->frequency,
            'status'        => $request->status
        ]);
    }

    public function getAllRegistrationFee($request)
    {
        $reg_fees = Registration::orderBy('updated_at', 'DESC')->get();

        return RegisterFeeResource::collection($reg_fees);
    }

    public function getCurrentRegistrationFee()
    {
        return Registration::where('status', SessionStatus::ACTIVE)->firstOrFail();
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
