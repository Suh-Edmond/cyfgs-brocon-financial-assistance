<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ExpenditureItemCollection extends ResourceCollection
{
    private $total;

    public function __construct($collection, $total, $balance)
    {
      parent::__construct($collection);
      $this->total   = $total;
      $this->balance = $balance;
    }

    public function toArray($request)
    {
        return [
            'data'          => $this->collection,
            'total_amount'  => $this->total,
            'balance'       => $this->balance
        ];
    }
}
