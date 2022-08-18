<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class IncomeActivityCollection extends ResourceCollection
{
    private $total;

    public function __construct($collection, $total)
    {
      parent::__construct($collection);
      $this->total = $total;
    }



    public function toArray($request)
    {
        return [
            'data'  =>$this->collection,
            'total_amount' => $this->total
        ];
    }
}
