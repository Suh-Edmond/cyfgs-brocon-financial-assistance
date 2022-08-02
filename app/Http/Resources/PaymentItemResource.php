<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentItemResource extends JsonResource
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
            'name'                  => $this->name,
            'amount'                => $this->amount,
            'complusory'            => $this->complusory,
            'payment_category_id'   => $this->paymentCategory->id,
            'payment_category_name' => $this->paymentCategory->name,
            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at
        ];
    }
}
