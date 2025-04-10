<?php

namespace App\Http\Resources;

use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use Ramsey\Uuid\Uuid;

class IncomeActivityResource extends JsonResource
{
    use HelpTrait;
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
            'name'                  => $this->name,
            'description'           => $this->description,
            'venue'                 => $this->venue,
            'date'                  => $this->date,
            'amount'                => $this->amount,
            'approve'               => $this->approve,
            'organisation_id'       => $this->organisation->id,
            'payment_item'          => $this->paymentItem,
            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at,
            'updated_by'            => $this->updated_by,
            'session'               => $this->session,
            'incomeActivity'        => $this->incomeActivity
        ];
    }
}
