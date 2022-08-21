<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserContributionCollection extends ResourceCollection
{
    private $total;
    private $balance;

    public function __construct($resource, $total = null, $balance = null)
    {
        parent::__construct($resource);
        $this->total   = $total;
        $this->balance = $balance;
    }


    public function toArray($request)
    {
        return [
            "data"         => $this->collection,
            'total_amount' => $this->total,
            'balance'      => $this->balance
        ];
    }
}
