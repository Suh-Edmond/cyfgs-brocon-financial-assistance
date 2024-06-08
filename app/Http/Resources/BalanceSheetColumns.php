<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BalanceSheetColumns extends JsonResource
{

    private $code;
    private $name;
    private $id;
    private $amount;
    private $compulsory;
    private $type;
    private $frequency;
    private $payment_durations;
    private $total_expected_amount;
    private $member_size;
    private $total_amount_deposited;
    public function __construct($resource, $code, $name, $id, $amount, $compulsory, $type, $frequency,
                                $payment_durations, $total_expected_amount, $member_size,$total_amount_deposited)
    {
        parent::__construct($resource);
        $this->code = $code;
        $this->name = $name;
        $this->id = $id;
        $this->amount = $amount;
        $this->compulsory = $compulsory;
        $this->type = $type;
        $this->frequency = $frequency;
        $this->payment_durations = $payment_durations;
        $this->total_expected_amount = $total_expected_amount;
        $this->member_size = $member_size;
        $this->total_amount_deposited = $total_amount_deposited;
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
            'code'  => $this->code,
            'name'  => $this->name,
            'id'    => $this->id,
            'amount' => $this->amount,
            'compulsory' => $this->compulsory,
            'type'      => $this->type,
            'frequency' => $this->frequency,
            'total_expect_amount' => $this->total_expected_amount,
            'member_size' => $this->member_size,
            'payment_durations' => $this->payment_durations,
            'total_amount_deposited' => $this->total_amount_deposited
        ];
    }
}
