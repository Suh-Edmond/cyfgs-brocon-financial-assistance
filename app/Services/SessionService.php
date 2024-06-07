<?php


namespace App\Services;


use App\Constants\SessionStatus;
use App\Http\Resources\SessionResource;
use App\Http\Resources\SessionResourceCollection;
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
        $session = Session::where('status', SessionStatus::ACTIVE)->first();
        return new SessionResource($session);
    }

    public function createSession($request)
    {
        $previousSession =  Session::where('status', SessionStatus::ACTIVE)->first();
        if(isset($previousSession)) {
            $previousSession->status = SessionStatus::IN_ACTIVE;
            $previousSession->save();
        }
        Session::create([
            'year'          => $request->year,
            'status'        => SessionStatus::ACTIVE,
            'updated_by'    => $request->user()->name
        ]);
    }

    public function updateSession($request, $id)
    {
        $currentSession =  Session::where('status', SessionStatus::ACTIVE)->first();
        $updatedSession = Session::findOrFail($id);

        if(SessionStatus::ACTIVE == $request->status && $request->year != $currentSession->year){
            $currentSession->status = SessionStatus::IN_ACTIVE;
            $currentSession->save();

        }
        $updatedSession->update([
            'status' => $request->status
        ]);


    }

    public function deleteSession($id)
    {
        return Session::findOrFail($id)->delete();
    }

    public function getSessionByLabel($label)
    {
        return Session::where('year', $label)->first();
    }

    public function getPaginatedSessions($request)
    {
        $sessions = DB::table('sessions')->orderBy('year')->paginate($request->per_page);

        return new SessionResourceCollection($sessions, $sessions->total(), $sessions->lastPage(), (int)$sessions->perPage(), $sessions->currentPage());
    }

    public function getSessionById($id)
    {
        return Session::findOrFail($id);
    }
}
