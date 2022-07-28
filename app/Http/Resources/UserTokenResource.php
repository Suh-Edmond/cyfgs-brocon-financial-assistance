<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserTokenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'header'          => 'Authorization',
            'type'            => 'Bearer',
            'accessToken'     => '',
            'id'              => $this->id,
            'name'            => $this->name,
            'email'           => $this->email,
            'telephone'       => $this->telephone,
            'address'         => $this->address,
            'occupation'      => $this->occupation,
            'gender'          => $this->gender,
            'organisation_id' => $this->organisation->id,
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
        ];
    }
}
