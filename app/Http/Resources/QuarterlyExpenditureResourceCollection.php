<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class QuarterlyExpenditureResourceCollection extends ResourceCollection
{
    private $code;
    private $total_expenditure;
    public function __construct($collection, $code, $total_expenditure)
    {
        parent::__construct($collection);
        $this->code = $code;
        $this->total_expenditure = $total_expenditure;
    }

    public function toArray($request)
    {
        return [
            "data"        =>   $this->collection,
            'code_name'   =>   $this->code,
            'total_expenditure' => $this->total_expenditure
        ];
    }
}
