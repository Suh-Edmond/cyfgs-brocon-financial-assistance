<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuarterlyExpenditureResource extends JsonResource
{
    private $code;
    private $name;
    private $items;
    private $total;

    public function __construct($code, $name, $items, $total)
    {
        parent::__construct(null);
        $this->code = $code;
        $this->name = $name;
        $this->items = $items;
        $this->total = $total;
    }

    public function toArray($request)
    {
        return [
            'code'          => $this->code,
            'name'          => $this->name,
            'items'         => $this->items,
            'total'         => $this->total
        ];
    }
}
