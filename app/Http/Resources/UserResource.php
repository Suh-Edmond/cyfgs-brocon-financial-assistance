<?php

namespace App\Http\Resources;

use App\Constants\PaymentStatus;
use App\Constants\RegistrationStatus;
use App\Constants\SessionStatus;
use App\Models\Session;
use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    use ResponseTrait;
    use HelpTrait;

    private $token;
    private $hasLoginBefore;
    private $current_session;

    public function __construct($resource, $token = null, $hasLoginBefore = null)
    {
        parent::__construct($resource);
        $this->token = $token;
        $this->hasLoginBefore = $hasLoginBefore;
        $this->current_session = Session::where('status', SessionStatus::ACTIVE)->first();
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'email'          => $this->email,
            'telephone'      => $this->telephone,
            'address'        => $this->address,
            'occupation'     => $this->occupation,
            'gender'         => $this->gender,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
            'picture'        => $this->picture,
            'roles'          => $this->roles,
            'token'          => $this->hasLoginBefore ? $this->token: "",
            'has_register'   => !is_null($this->approve) && $this->approve == PaymentStatus::APPROVED && $this->current_session->id == $this->session_id ? RegistrationStatus::REGISTERED : RegistrationStatus::NOT_REGISTERED,
            'hasLoginBefore' => $this->hasLoginBefore,
            'has_paid'       => !is_null($this->approve) && $this->current_session->id == $this->session_id,
            'approve'        => !is_null($this->approve) && $this->current_session->id == $this->session_id? $this->approve : '',
            'year'           => !is_null(Session::find($this->session_id)) ? Session::find($this->session_id)->year : null,
            'session_id'     => $this->session_id,
            'status'         => $this->status,
            'organisation_id' => $this->organisation_id,
            'registered'       => self::getRegistrationByActiveSession($this->registrations, $this->current_session->id)
          ];
    }
}
