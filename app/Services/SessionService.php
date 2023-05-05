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
        $sessions = DB::table('sessions')->orderBy('created_at', 'DESC')->orderBy('updated_at', 'DESC')->get();
        return SessionResource::collection($sessions->toArray());
    }
    public function getCurrentSession()
    {
        $session = Session::where('status', SessionStatus::ACTIVE)->get();
        return new  SessionResource($session[0]);
    }

    public function createSession($request)
    {
        $previousSession =  Session::where('status', SessionStatus::ACTIVE)->first();
        if(is_null($previousSession)){
            Session::create([
                'year'          => $request->year,
                'status'        => $request->status,
                'updated_by'    => $request->user()->name
            ]);
        }else{
            if(SessionStatus::ACTIVE == $request->status){
                $previousSession->status = SessionStatus::IN_ACTIVE;
                $previousSession->save();
                Session::create([
                    'year'          => $request->year,
                    'status'        => $request->status,
                    'updated_by'    => $request->user()->name
                ]);
            }else {
                Session::create([
                    'year'          => $request->year,
                    'status'        => $request->status,
                    'updated_by'    => $request->user()->name
                ]);
            }
        }
    }

    public function updateSession($request, $id)
    {
        $currentSession =  Session::where('status', SessionStatus::ACTIVE)->first();
        $updatedSession = Session::findOrFail($id);

        if(SessionStatus::ACTIVE == $request->status && $request->year != $currentSession->year){
            $currentSession->status = SessionStatus::IN_ACTIVE;
            $currentSession->save();

            $updatedSession->update([
                'status' => $request->status
            ]);
        }else {
            $updatedSession->update([
                'status' => $request->status
            ]);
        }




    }

    public function deleteSession($id)
    {
        return Session::findOrFail($id)->delete();
    }
}
