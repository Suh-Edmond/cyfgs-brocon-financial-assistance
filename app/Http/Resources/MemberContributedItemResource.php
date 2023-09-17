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
    private $frequency;
    private $month_name;
    private $quarterly_name;
    private $created_by;
    private $code;
    private $comment;
    private $is_compulsory;
    public function __construct($id, $payment_item_id, $payment_item_amount, $name, $amount_deposited,
                                $balance, $status, $approve, $created_at, $year, $frequency, $month_name, $quarterly_name,
                                $created_by, $code, $comment, $is_compulsory)
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
        $this->frequency = $frequency;
        $this->month_name = $month_name;
        $this->quarterly_name = $quarterly_name;
        $this->created_by = $created_by;
        $this->code = $code;
        $this->comment = $comment;
        $this->is_compulsory = $is_compulsory;
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
            'frequency'        => $this->frequency,
            'month_name'       => $this->month_name,
            'quarterly_name'   => $this->quarterly_name,
            'created_by'       => $this->created_by,
            'code'             => $this->code,
            'comment'          => $this->comment,
            'is_compulsory'    => $this->is_compulsory
        ];
    }
}
