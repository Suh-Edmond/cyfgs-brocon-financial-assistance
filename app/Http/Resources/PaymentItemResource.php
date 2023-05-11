<?php

namespace App\Http\Resources;

use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentItemResource extends JsonResource
{
    use ResponseTrait, HelpTrait;


    public function toArray($request)
    {
        return [
            'id'                    => $this->id,
            'name'                  => $this->name,
            'amount'                => $this->amount,
            'description'           => $this->description,
            'compulsory'            => $this->compulsory,
            'payment_category_id'   => $this->paymentCategory->id,
            'payment_category_name' => $this->paymentCategory->name,
            'type'                  => $this->type,
            'frequency'             => $this->frequency,
            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at,
            'session'               => $this->session,
            'references'             => $this->getReferenceResource($this->reference),
        ];
    }


}
