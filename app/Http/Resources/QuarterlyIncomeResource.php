<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuarterlyIncomeResource extends JsonResource
{
    private $category;
    private $items;
    private $total;

    public function __construct($category, $items, $total)
    {
        parent::__construct(null);
        $this->category = $category;
        $this->items = $items;
        $this->total = $total;
    }

    public function toArray($request)
    {
        return [
            'category'      => $this->category,
            'items'         => $this->items,
            'total'         => $this->total
        ];
    }
}
