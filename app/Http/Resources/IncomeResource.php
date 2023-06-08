<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class IncomeResource extends JsonResource
{
    public $items;
    public $name;
    public $code;
    public $total;

    public function __construct($code, $items, $name, $total)
    {
        parent::__construct(null);
        $this->code = $code;
        $this->items = $items;
        $this->name = $name;
        $this->total = $total;
    }

    public function toArray($request)
    {
        return [
            'code'      => $this->code,
            'name'     => $this->name,
            'items'    => $this->items,
            'total'    => $this->total
        ];
    }
}
