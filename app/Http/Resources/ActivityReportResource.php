<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivityReportResource extends JsonResource
{
    private $name;
    private $amount;

    public function __construct($name, $amount)
    {
        parent::__construct(null);
        $this->name = $name;
        $this->amount = $amount;
    }

    public function toArray($request)
    {
        return [
            'name'              => $this->name,
            'amount'            => $this->amount
        ];
    }
}
