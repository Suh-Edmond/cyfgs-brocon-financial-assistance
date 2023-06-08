<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class QuarterlyExpenditureResourceCollection extends JsonResource
{
    public $code;
    public $name;
    public $items;
    public function __construct($code, $name, $items)
    {
        parent::__construct(null);
        $this->code = $code;
        $this->name = $name;
        $this->items = $items;
    }

    public function toArray($request)
    {
        return [
            "code"        =>   $this->code,
            'name'        =>   $this->name,
            'items'       => $this->items
        ];
    }
}
