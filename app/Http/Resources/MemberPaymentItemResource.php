<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberPaymentItemResource extends JsonResource
{
    private $id;
    private $type;
    private $amount;
    private $name;
    private $is_compulsory;
    private $has_pay;

    public function __construct($id, $type, $amount, $name, $has_pay, $is_compulsory)
    {
        parent::__construct(null);
        $this->id = $id;
        $this->name = $name;
        $this->amount = $amount;
        $this->type = $type;
        $this->is_compulsory = $is_compulsory;
        $this->has_pay = $has_pay;
    }

    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'amount'        => $this->amount,
            'type'          => $this->type,
            'is_compulsory' => $this->is_compulsory,
            'has_pay'       => $this->has_pay
        ];
    }
}
