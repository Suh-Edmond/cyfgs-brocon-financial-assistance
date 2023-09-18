<?php

namespace App\Http\Resources;

use App\Models\PaymentItem;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class UserContributionResource extends JsonResource
{


    public function toArray($request)
    {
        $payment_item = PaymentItem::find($this->payment_item_id);
        $user = User::find($this->user_id);
        return [
            'id'                       => $this->id,
            'code'                     => $this->code,
            'amount_deposited'         => $this->amount_deposited,
            'comment'                  => $this->comment,
            'status'                   => $this->status,
            'approve'                  => $this->approve,
            'user_id'                  => $this->user_id,
            'user_name'                => $user->name,
            'user_telephone'           => $user->telephone,
            'payment_item_id'          => $payment_item->id,
            'payment_item_name'        => $payment_item->name,
            'payment_item_amount'      => $payment_item->amount,
            'payment_item_frequency'   => $payment_item->frequency,
            'payment_category'         => $payment_item->paymentCategory,
            'payment_item_compulsory'  => $payment_item->compulsory == 0? false : true,
            'balance'                  => $this->total_amount_deposited == null ? $this->balance :($payment_item->amount - $this->total_amount_deposited),
            'updated_by'               => $this->updated_by,
            'created_at'               => $this->created_at,
            'total_amount_deposited'   => $this->total_amount_deposited == null ? 0: $this->total_amount_deposited,
            'session_id'               => $this->session_id,
            'quarterly_name'           => $this->quarterly_name,
            'month_name'               => $this->month_name
        ];
    }
}
