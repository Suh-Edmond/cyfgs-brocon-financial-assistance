<?php

namespace App\Http\Controllers;

use App\Constants\SessionStatus;
use App\Http\Requests\SessionRequest;
use App\Services\SessionService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SessionController extends Controller
{
    use ResponseTrait;
    private SessionService $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }
    public function getAllSessions()
    {
        $data = $this->sessionService->getAllSessions();
        return $this->sendResponse($data, 200);
    }

    public function getCurrentSession()
    {
        $data = $this->sessionService->getCurrentSession();
        return $this->sendResponse($data, 200);
    }

    public function createSession(SessionRequest $request)
    {
        $this->sessionService->createSession($request);
        return $this->sendResponse('success', 'Session set successfully');
    }

    public function updateSession(Request $request)
    {
        $request->validate([
            'status' => 'required', Rule::in([SessionStatus::ACTIVE, SessionStatus::IN_ACTIVE]),
            'id'     => 'required|string'
        ]);
        $this->sessionService->updateSession($request, $request->id);
        return $this->sendResponse('success', 'Session updated successfully');
    }

    public function deleteSession($id)
    {
        $this->sessionService->deleteSession($id);

        return $this->sendResponse('success', 'Session deleted successfully');
    }
}
