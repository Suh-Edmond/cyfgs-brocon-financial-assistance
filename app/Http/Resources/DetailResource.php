<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DetailResource extends JsonResource
{
    private $name;
    private $amount_given;
    private $amount_spent;
    private $balance;

    public function __construct($name, $amount_given, $amount_spent, $balance)
    {
        parent::__construct(null);
        $this->name = $name;
        $this->amount_given = $amount_given;
        $this->amount_spent = $amount_spent;
        $this->balance = $balance;
    }

    public function toArray($request)
    {
        return [
            'name'      => $this->name,
            'amount_given'  => $this->amount_given,
            'amount_spent'  => $this->amount_spent,
            'balance'       => $this->balance
        ];
    }
}
