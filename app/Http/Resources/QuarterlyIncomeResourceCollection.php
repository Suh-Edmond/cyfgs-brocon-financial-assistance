<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuarterlyIncomeResourceCollection extends JsonResource
{
    private $code;
    private $name;
    private $payment_items;
     public function __construct($payment_items, $code, $name)
    {
        parent::__construct(null);
        $this->code = $code;
        $this->name = $name;
        $this->payment_items = $payment_items;
    }

    public function toArray($request)
    {
        return [
            "payment_items"            =>   $this->payment_items,
            'code'                     =>   $this->code,
            'name'                     =>   $this->name,
        ];
    }
}
