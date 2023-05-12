<?php

namespace App\Http\Resources;

use App\Models\User;
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
            'user_name'              => User::find($this->user_id)->name,
            'email'                  => User::find($this->user_id)->email,
            'telephone'              => User::find($this->user_id)->telephone,
            'created_at'             => $this->created_at,
            'updated_at'             => $this->updated_at,
            'updated_by'             => $this->updated_by,
//            'session'                => $this->session
        ];
    }
}
