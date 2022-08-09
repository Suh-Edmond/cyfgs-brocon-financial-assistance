<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserContributionResource extends JsonResource
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
            'id'                  => $this->id,
            'code'                => $this->code,
            'amount_deposited'    => $this->amount_deposited,
            'comment'             => $this->comment,
            'status'              => $this->status,
            'approve'             => $this->approve,
            'user_id'             => $this->user->id,
            'user_name'           => $this->user->name,
            'payment_item_id'     => $this->paymentItem->id,
            'payment_item_name'   => $this->paymentItem->name,
            'payment_item_amount' => $this->paymentItem->amount,
            'payment_item_complusory' => $this->paymentItem->complusory,
        ];
    }
}
