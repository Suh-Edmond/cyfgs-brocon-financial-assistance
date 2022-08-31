<?php

namespace App\Http\Resources;

use App\Traits\ResponseTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use Ramsey\Uuid\Uuid;

class UserSavingResource extends JsonResource
{
    use ResponseTrait;

    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'amount_deposited'  => $this->amount_deposited,
            'comment'           => $this->comment,
            'approve'           => ResponseTrait::convertBooleanValue($this->approve),
            'user_id'           => $this->user->id,
            'user_name'         => $this->user->name,
            'telephone'         => $this->user->telephone,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
        ];
    }
}
