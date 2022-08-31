<?php

namespace App\Http\Resources;

use App\Traits\ResponseTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use Ramsey\Uuid\Uuid;

class IncomeActivityResource extends JsonResource
{
    use ResponseTrait;
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
            'approve'               => ResponseTrait::convertBooleanValue($this->approve),
            'organisation'          => $this->organisation,
            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at,
        ];
    }
}
