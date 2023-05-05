<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberRegistrationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'session'      => $this->session,
            'approve'   => $this->approve,
            'amount'    => $this->amount,
            'user'           => $this->user,
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
            'updated_by'      => $this->updated_by
        ];
    }
}
