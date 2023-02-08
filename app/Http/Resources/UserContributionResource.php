<?php

namespace App\Http\Resources;

use App\Models\PaymentItem;
use App\Models\User;
use App\Traits\HelpTrait;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'status'                   => ((PaymentItem::find($this->payment_item_id)->amount) - ($this->total_amount_deposited)) == 0? 'COMPLETE' : 'INCOMPLETE',
            'approve'                  => $this->approve,
            'user_id'                  => $this->user_id,
            'user_name'                => User::find($this->user_id)->name,
            'user_telephone'           => User::find($this->user_id)->telephone,
            'payment_item_id'          => $this->payment_item_id,
            'payment_item_name'        => PaymentItem::find($this->payment_item_id)->name,
            'payment_item_amount'      => PaymentItem::find($this->payment_item_id)->amount,
            'payment_item_complusory'  => $this->convertBooleanValue(PaymentItem::find($this->payment_item_id)->complusory),
            'balance'                  => (PaymentItem::find($this->payment_item_id)->amount - $this->total_amount_deposited),
            'updated_by'               => $this->updated_by,
            'created_at'               => $this->created_at,
            'total_amount_deposited'   => $this->total_amount_deposited
        ];
    }
}
