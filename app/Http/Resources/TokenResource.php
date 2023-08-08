<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TokenResource extends JsonResource
{
    private $user_data;
    private $current_session;

    public function __construct($user_data, $current_session)
    {
        parent::__construct(null);
        $this->user_data = $user_data;
        $this->current_session = $current_session;
    }

    public function toArray($request)
    {
        return [
            'user_data' => $this->user_data,
            'current_session' => $this->current_session
        ];
    }
}
