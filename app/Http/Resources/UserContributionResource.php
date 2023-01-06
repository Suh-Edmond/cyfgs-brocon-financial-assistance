<?php

namespace App\Http\Resources;

use App\Models\PaymentItem;
use App\Models\User;
use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use Ramsey\Uuid\Uuid;

class UserContributionResource extends JsonResource
{
    use HelpTrait;

    public function toArray($request)
    {
        return [
            'id'                       => $this->id,
            'code'                     => $this->code,
            'amount_deposited'         => $this->amount_deposited,
            'comment'                  => $this->comment,
            'status'                   => $this->status,
            'approve'                  => $this->convertBooleanValue($this->approve),
            'user_id'                  => $this->user_id,
            'user_name'                => User::find($this->user_id)->name,
            'user_telephone'           => User::find($this->user_id)->telephone,
            'payment_item_id'          => $this->payment_item_id,
            'payment_item_name'        => PaymentItem::find($this->payment_item_id)->name,
            'payment_item_amount'      => PaymentItem::find($this->payment_item_id)->amount,
            'payment_item_complusory'  => $this->convertBooleanValue(PaymentItem::find($this->payment_item_id)->complusory),
            'balance'                  => (PaymentItem::find($this->payment_item_id)->amount - $this->amount_deposited),
            'updated_by'               => $this->updated_by,
            'created_at'               => $this->created_at
        ];
    }
}
