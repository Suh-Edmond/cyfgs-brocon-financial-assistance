<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class IncomeActivityResource extends JsonResource
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
            'id'                    => $this->id,
            'description'           => $this->description,
            'venue'                 => $this->venue,
            'date'                  => $this->date,
            'amount'                => $this->amount,
            'approve'               => $this->approve,
            'organisation_id'       => $this->organisation->id,
            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at,
            'updated_by'            => $this->updated_by,
        ];
    }
}
