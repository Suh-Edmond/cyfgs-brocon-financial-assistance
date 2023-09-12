<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection
{
    private $total;
    private $lastPage;
    private $perPage;
    private $currentPage;

    public function __construct($collection, $total,$lastPage, $perPage, $currentPage)
    {
        parent::__construct($collection);
        $this->total = $total;
        $this->lastPage = $lastPage;
        $this->perPage = $perPage;
        $this->currentPage = $currentPage;
    }



    public function toArray($request)
    {
        return [
            "data"         => $this->collection,
            'total'        => $this->total,
            'last_page'    => $this->lastPage,
            'per_page'     => $this->perPage,
            'current_page' => $this->currentPage
        ];
    }
}
