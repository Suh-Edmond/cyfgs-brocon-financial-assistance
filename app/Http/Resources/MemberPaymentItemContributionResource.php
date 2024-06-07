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
     public function __construct($resource, $id, $name, $amount, $item_amount, $balance, $code)
     {
         parent::__construct($resource);
         $this->id = $id;
         $this->amount = $amount;
         $this->name = $name;
         $this->item_amount = $item_amount;
         $this->code = $code;
         $this->balance = $balance;
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
            'amount_payable' => $this->item_amount,
            'code'           => $this->code,
            'balance'        => $this->balance
        ];
    }
}
