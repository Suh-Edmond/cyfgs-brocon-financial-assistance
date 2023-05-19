<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberPaymentItemResource extends JsonResource
{
    private $id;
    private $name;
    private $amount;
    private $compulsory;
    private $type;
    private $frequency;
    private $code; //to tell if its REGISTRATION or CONTRIBUTION
    private $session;
    private $month_name;
    public function __construct($id, $name, $amount, $compulsory, $type, $frequency, $code = 'CONTRIBUTION', $session, $month_name)
    {
        parent::__construct(null);
        $this->id = $id;
        $this->name = $name;
        $this->amount = $amount;
        $this->compulsory = $compulsory;
        $this->type = $type;
        $this->frequency = $frequency;
        $this->code = $code;
        $this->session = $session;
        $this->month_name = $month_name;
    }

    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'amount'        => $this->amount,
            'type'          => $this->type,
            'is_compulsory' => $this->compulsory,
            'has_pay'       => false,
            'frequency'     => $this->frequency,
            'code'          => $this->code,
            'session'       => $this->session,
            'month_name'    => $this->month_name
        ];
    }
}
