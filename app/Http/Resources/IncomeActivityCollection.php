<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class IncomeActivityCollection extends ResourceCollection
{

    public function __construct($collection)
    {
      parent::__construct($collection);
    }



    public function toArray($request)
    {
        return [
            'data'  =>$this->collection,
        ];
    }
}
