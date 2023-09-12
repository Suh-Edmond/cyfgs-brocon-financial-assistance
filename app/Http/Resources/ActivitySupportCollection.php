<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ActivitySupportCollection extends ResourceCollection
{
    private $total_amount;
    private $total;
    private $lastPage;
    private $perPage;
    private $currentPage;

    public function __construct($collection, $total_amount, $total,$lastPage, $perPage, $currentPage)
    {
        parent::__construct($collection);
        $this->total_amount = $total_amount;
        $this->total = $total;
        $this->lastPage = $lastPage;
        $this->perPage = $perPage;
        $this->currentPage = $currentPage;
    }



    public function toArray($request)
    {
        return [
            'data'           =>$this->collection,
            'total_amount'   => $this->total_amount,
            'total'          => $this->total,
            'last_page'      => $this->lastPage,
            'per_page'       => $this->perPage,
            'current_page'   => $this->currentPage
        ];
    }
}
