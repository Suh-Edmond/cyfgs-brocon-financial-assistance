<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PaymentItemCollection extends ResourceCollection
{
    private $total;

    public function __construct($collection, $total)
    {
      parent::__construct($collection);
      $this->total = $total;
    }
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "data"  =>$this->collection,
            'total_amount' => $this->total
        ];
    }
}
