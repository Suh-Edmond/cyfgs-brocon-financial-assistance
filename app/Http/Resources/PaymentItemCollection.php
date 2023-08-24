<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PaymentItemCollection extends ResourceCollection
{
    private $total;
    private $currentPage;

    public function __construct($collection, $total,$currentPage)
    {
      parent::__construct($collection);
      $this->total = $total;
      $this->currentPage = $currentPage;
    }



    public function toArray($request)
    {
        return [
            "data"  =>$this->collection,
            'total_pages' => $this->total,
            'current_page'=>$this->currentPage
        ];
    }
}
