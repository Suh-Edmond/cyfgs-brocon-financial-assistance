<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class IncomeResource extends JsonResource
{
    public $items;
    public $name;
    public $code;

    public function __construct($code, $items, $name)
    {
        parent::__construct(null);
        $this->code = $code;
        $this->items = $items;
        $this->name = $name;
    }

    public function toArray($request)
    {
        return [
            'code'      => $this->code,
            'name'     => $this->name,
            'items'    => $this->items,
        ];
    }
}
