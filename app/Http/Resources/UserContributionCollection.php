<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserContributionCollection extends ResourceCollection
{
    private $total_contribution;
    private $lastPage;
    private $perPage;
    private $currentPage;
    private $total;//the total number of records;

    public function __construct($collection, $total_contribution, $total, $lastPage, $perPage, $currentPage)
    {
        parent::__construct($collection);
        $this->total_contribution = $total_contribution;
        $this->total   = $total;
        $this->lastPage = $lastPage;
        $this->perPage = $perPage;
        $this->currentPage = $currentPage;
    }


    public function toArray($request)
    {
        return [
            "data"         => $this->collection,
            'total_amount' => $this->total_contribution,
            'total'          => $this->total,
            'last_page'      => $this->lastPage,
            'per_page'       => $this->perPage,
            'current_page'   => $this->currentPage
        ];
    }
}
