<?php

namespace App\Http\Resources;

use App\Traits\ResponseTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use Ramsey\Uuid\Uuid;

class UserContributionResource extends JsonResource
{
    use ResponseTrait;

    public function toArray($request)
    {
        return [
            'id'                       => $this->id,
            'code'                     => $this->code,
            'amount_deposited'         => $this->amount_deposited,
            'comment'                  => $this->comment,
            'status'                   => $this->status,
            'approve'                  => $this->convertBooleanValue($this->approve),
            'user_id'                  =>  $this->user->id,
            'user_name'                => $this->user->name,
            'user_telephone'           => $this->user->telephone,
            'payment_item_id'          => $this->paymentItem->id,
            'payment_item_name'        => $this->paymentItem->name,
            'payment_item_amount'      => $this->paymentItem->amount,
            'payment_item_complusory'  => $this->convertBooleanValue($this->paymentItem->complusory),
            'balance'                  => ($this->paymentItem->amount - $this->amount_deposited)
        ];
    }
}
