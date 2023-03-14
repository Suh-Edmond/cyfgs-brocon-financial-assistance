<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentActivityData extends JsonResource
{
    private $incomes;
    private $expenditures;

    public function __construct($incomes, $expenditures)
    {
        parent::__construct(null);
        $this->incomes = $incomes;
        $this->expenditures = $expenditures;
    }

    public function toArray($request)
    {
        return [
            'incomes'       =>  $this->incomes,
            'expenditures'  => $this->expenditures
        ];
    }
}
