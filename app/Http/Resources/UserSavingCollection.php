<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserSavingCollection extends ResourceCollection
{

    private float $total_amount_deposited;
    private $total;
    private $lastPage;
    private $perPage;
    private $currentPage;

    public function __construct($collection, $total_amount_deposited, $total,$lastPage, $perPage, $currentPage)
    {
      parent::__construct($collection);
      $this->total_amount_deposited = $total_amount_deposited;
      $this->total = $total;
      $this->lastPage = $lastPage;
      $this->perPage = $perPage;
      $this->currentPage = $currentPage;
    }


    public function toArray($request)
    {
        return [
            "data"           => $this->collection,
            'total_savings'  => $this->total_amount_deposited,
            'total'          => $this->total,
            'last_page'      => $this->lastPage,
            'per_page'       => $this->perPage,
            'current_page'   => $this->currentPage
        ];
    }
}
