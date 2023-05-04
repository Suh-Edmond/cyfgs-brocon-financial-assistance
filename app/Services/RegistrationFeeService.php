<?php


namespace App\Services;


use App\Constants\SessionStatus;
use App\Http\Resources\RegisterFeeResource;
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
                'frequency'     => $request->frequency
            ]);
        }else {
            Registration::create([
                'is_compulsory' => $request->is_compulsory,
                'amount'        => $request->amount,
                'status'        => SessionStatus::IN_ACTIVE,
                'frequency'     => $request->frequency
            ]);
        }
    }

    public function updateRegistrationFee($request, $id)
    {
        $updated = Registration::findOrFail($id);
        $updated->update([
            'is_compulsory' => $request->is_compulsory,
            'amount'        => $request->amount,
            'frequency'     => $request->frequency
        ]);
    }

    public function getAllRegistrationFee()
    {
        $reg_fees = DB::table('registrations')->orderBy('created_at', 'ASC')->get();
        return RegisterFeeResource::collection($reg_fees->toArray());
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
