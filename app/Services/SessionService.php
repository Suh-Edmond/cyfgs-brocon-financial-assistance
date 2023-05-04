<?php


namespace App\Services;


use App\Constants\SessionStatus;
use App\Http\Resources\SessionResource;
use App\Interfaces\SessionInterface;
use App\Models\Session;
use Illuminate\Support\Facades\DB;

class SessionService implements SessionInterface
{


    public function getAllSessions()
    {
        $sessions = DB::table('sessions')->orderBy('created_at', 'DESC')->get();
        return SessionResource::collection($sessions->toArray());
    }
    public function getCurrentSession()
    {
        $session = Session::where('status', SessionStatus::ACTIVE)->get()[0];
        return new  SessionResource($session);
    }

    public function createSession($request)
    {
        $previousSession =  Session::where('status', SessionStatus::ACTIVE)->first();
        if(is_null($previousSession)){
            Session::create([
                'year'          => $request->year,
                'status'        => SessionStatus::ACTIVE,
                'updated_by'    => $request->user()->name
            ]);
        }else{
            $previousSession->status = SessionStatus::IN_ACTIVE;
            $previousSession->save();
            Session::create([
                'year'          => $request->year,
                'status'        => SessionStatus::ACTIVE,
                'updated_by'    => $request->user()->name
            ]);
        }
    }

    public function updateSession($request)
    {
        $currentSession = Session::findOrFail($request->id);
        $currentSession->update([
            'status' => $request->status
        ]);
    }

    public function deleteSession($id)
    {
        return Session::findOrFail($id)->delete();
    }
}
