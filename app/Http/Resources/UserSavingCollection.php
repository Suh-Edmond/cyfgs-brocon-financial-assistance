<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserSavingCollection extends ResourceCollection
{

    private float $total_amount_deposited;

    public function __construct($collection, $total_amount_deposited)
    {
      parent::__construct($collection);
      $this->total_amount_deposited = $total_amount_deposited;
    }


    public function toArray($request)
    {
        return [
            "data"          => $this->collection,
            'total_savings'  => $this->total_amount_deposited
        ];
    }
}
