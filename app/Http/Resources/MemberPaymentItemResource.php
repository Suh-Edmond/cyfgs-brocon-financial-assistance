<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberPaymentItemResource extends JsonResource
{
    private $id;
    private $name;
    private $item_amount;
    private $amount;
    private $compulsory;
    private $type;
    private $frequency;
    private $code; //to tell if its REGISTRATION or CONTRIBUTION
    private $session;
    private $month_name;
    private $quarterly_name;
    public function __construct($id, $name, $amount, $item_amount, $compulsory, $type, $frequency, $code, $session, $month_name, $quarterly_name)
    {
        parent::__construct(null);
        $this->id = $id;
        $this->name = $name;
        $this->amount = $amount;
        $this->item_amount = $item_amount;
        $this->compulsory = $compulsory;
        $this->type = $type;
        $this->frequency = $frequency;
        $this->code = $code;
        $this->session = $session;
        $this->month_name = $month_name;
        $this->quarterly_name = $quarterly_name;
    }


    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request):array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'balance'       => $this->amount,
            'item_amount'   => $this->item_amount,
            'type'          => $this->type,
            'is_compulsory' => $this->compulsory,
            'has_pay'       => false,
            'frequency'     => $this->frequency,
            'code'          => $this->code,
            'session'       => $this->session,
            'month_name'    => $this->month_name,
            'quarterly_name'=> $this->quarterly_name
        ];
    }
}
