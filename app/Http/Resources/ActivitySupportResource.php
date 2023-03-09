<?php

namespace App\Http\Resources;

use App\Traits\HelpTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivitySupportResource extends JsonResource
{

    use HelpTrait;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'                       => $this->id,
            'code'                     => $this->code,
            'amount_deposited'         => $this->amount_deposited,
            'comment'                  => $this->comment,
            'approve'                  => $this->approve,
            'payment_item'             => $this->paymentItem,
            'supporter'                => $this->supporter,
            'payment_category'         => $this->paymentItem->paymentCategory,
            'updated_by'               => $this->updated_by,
            'created_at'               => $this->created_at,
            'updated_at'               => $this->updated_at,
        ];
    }
}
