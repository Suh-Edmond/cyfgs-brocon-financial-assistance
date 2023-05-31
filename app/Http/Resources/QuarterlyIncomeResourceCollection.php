<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class QuarterlyIncomeResourceCollection extends ResourceCollection
{
    private $code;
    public function __construct($collection, $code)
    {
        parent::__construct($collection);
        $this->code = $code;
    }

    public function toArray($request)
    {
        return [
            "data"       =>   $this->collection,
            'code_name'   => $this->code
        ];
    }
}
