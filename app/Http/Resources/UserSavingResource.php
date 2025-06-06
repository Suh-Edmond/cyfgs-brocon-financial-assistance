<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserSavingResource extends JsonResource
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
            'id'                     => $this->id,
            'amount_deposited'       => $this->amount_deposited,
            'comment'                => $this->comment,
            'approve'                => $this->approve,
            'user_id'                => $this->user_id,
            'user_name'              => $this->user->name,
            'email'                  => $this->user->email,
            'telephone'              => $this->user->telephone,
            'created_at'             => $this->created_at,
            'updated_at'             => $this->updated_at,
            'updated_by'             => $this->updated_by,
            'session_id'             => $this->session_id,
            'amount_used'            => $this->amount_used,
            'total_amount'           => isset($this->total_amount) ? $this->total_amount: 0,
            'userSaving'             => $this->userSaving
//            'session'                => $this->session,
//            'session_status'         => $this->session_status
        ];
    }
}
