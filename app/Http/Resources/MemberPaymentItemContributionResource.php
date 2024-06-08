<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberPaymentItemContributionResource extends JsonResource
{

    private $id;
    private $name;
    private $item_amount;
    private $amount;
    private $code;
    private $balance;
    private $compulsory;
    private $type;
    private $frequency;
    private $payment_durations;
    private $created_at;
    private $reference;
     public function __construct($resource, $id, $name, $amount, $item_amount, $balance, $code, $compulsory,
                                $type, $frequency, $payment_durations, $created_at, $reference)
     {
         parent::__construct($resource);
         $this->id = $id;
         $this->amount = $amount;
         $this->name = $name;
         $this->item_amount = $item_amount;
         $this->code = $code;
         $this->balance = $balance;
         $this->compulsory = $compulsory;
         $this->type = $type;
         $this->frequency = $frequency;
         $this->payment_durations = $payment_durations;
         $this->created_at = $created_at;
         $this->reference = $reference;
     }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'  => $this->id,
            'name' => $this->name,
            'amount_deposited' => $this->amount,
            'amount' => $this->item_amount,
            'code'           => $this->code,
            'balance'        => $this->balance,
            'compulsory'     => $this->compulsory,
            'type'           => $this->type,
            'frequency'      => $this->frequency,
            'payment_durations' => $this->payment_durations,
            'created_at'        => $this->created_at,
            'reference'         => $this->reference
        ];
    }
}
