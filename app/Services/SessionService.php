<?php


namespace App\Services;


use App\Constants\SessionStatus;
use App\Http\Resources\SessionResource;
use App\Interfaces\SessionInterface;
use App\Models\Session;

class SessionService implements SessionInterface
{


    public function getAllSessions()
    {
        return SessionResource::collection(Session::all());
    }
    public function getCurrentSession()
    {
        $session = Session::where('status', SessionStatus::ACTIVE)->get()[0];
        return new  SessionResource($session);
    }

    public function createSession($request)
    {
        $previousSession = Session::where('status', SessionStatus::ACTIVE)->get();
        if(is_null($previousSession)){
            Session::create([
                'year'          => $request->year,
                'status'        => SessionStatus::ACTIVE,
                'updated_by'    => $request->user()->name
            ]);
        }else{
            $previousSession->update([
                'status'    => SessionStatus::IN_ACTIVE
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
