<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Ramsey\Uuid\Uuid;

class PaymentCategoryResource extends JsonResource
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
            'id'                => $this->id,
            'name'              => $this->name,
            'code'              => $this->code,
            'description'       => $this->description,
            'organisation_id'   => $this->organisation->id,
            'organisation_name' => $this->organisation->name,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
        ];
    }
}
