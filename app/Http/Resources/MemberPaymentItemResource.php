<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberPaymentItemResource extends JsonResource
{

    public function __construct($resource)
    {
        parent::__construct($resource);
    }

    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'amount'        => $this->amount,
            'type'          => !is_null($this->complusory) ? "CONTRIBUTIONS":"REGISTRATION",
            'is_compulsory' => $this->complusory,
            'has_pay'       => false
        ];
    }
}
