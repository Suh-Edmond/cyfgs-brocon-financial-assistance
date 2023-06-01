<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class IncomeTotalResource extends JsonResource
{
    private $total_income;
    public function __construct($total_income)
    {
        parent::__construct(null);
        $this->total_income = $total_income;
    }

    public function toArray($request)
    {
        return [
            'total_income'  => $this->total_income
        ];
    }
}
