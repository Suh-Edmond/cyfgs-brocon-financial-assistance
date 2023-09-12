<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class IncomeActivityCollection extends ResourceCollection
{

    private $total_income;
    private $total;
    private $lastPage;
    private $perPage;
    private $currentPage;

    public function __construct($collection, $total_income, $total,$lastPage, $perPage, $currentPage)
    {
      parent::__construct($collection);
        $this->total_income = $total_income;
        $this->total = $total;
        $this->lastPage = $lastPage;
        $this->perPage = $perPage;
        $this->currentPage = $currentPage;
    }



    public function toArray($request)
    {
        return [
            'data'           =>$this->collection,
            'total_income'   => $this->total_income,
            'total'          => $this->total,
            'last_page'      => $this->lastPage,
            'per_page'       => $this->perPage,
            'current_page'   => $this->currentPage
        ];
    }
}
