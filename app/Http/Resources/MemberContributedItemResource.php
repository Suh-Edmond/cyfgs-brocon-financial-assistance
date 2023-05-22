<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberContributedItemResource extends JsonResource
{
    private  $id;
    private $payment_item_id;
    private $payment_item_amount;
    private $name;
    private $amount_deposited;
    private $balance;
    private $status;
    private $approve;
    private $created_at;
    private $year;
    public function __construct($id, $payment_item_id, $payment_item_amount, $name, $amount_deposited, $balance, $status, $approve, $created_at, $year)
    {
        parent::__construct(null);
        $this->id = $id;
        $this->payment_item_id = $payment_item_id;
        $this->payment_item_amount = $payment_item_amount;
        $this->name = $name;
        $this->amount_deposited = $amount_deposited;
        $this->balance = $balance;
        $this->status = $status;
        $this->approve = $approve;
        $this->year = $year;
        $this->created_at = $created_at;
    }

    public function toArray($request)
    {
        return [
            'id'              => $this->id,
            'payment_item_id' => $this->payment_item_id,
            'payment_item_amount' => $this->payment_item_amount,
            'name'            => $this->name,
            'amount'          => $this->amount_deposited,
            'balance'         => $this->balance,
            'payment_status'  => $this->status,
            'approve'         => $this->approve,
            'year'             => $this->year,
            'created_at'       => $this->created_at,
        ];
    }
}
