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
    private $total_balance;
    private $unpaid_durations;
    private $total_amount_payable;

    public function __construct($collection, $total_contribution, $total_balance, $unpaid_durations,$total_amount_payable, $total, $lastPage, $perPage, $currentPage)
    {
        parent::__construct($collection);
        $this->total_contribution = $total_contribution;
        $this->total   = $total;
        $this->lastPage = $lastPage;
        $this->perPage = $perPage;
        $this->currentPage = $currentPage;
        $this->total_balance = $total_balance;
        $this->unpaid_durations = $unpaid_durations;
        $this->total_amount_payable = $total_amount_payable;
    }


    public function toArray($request)
    {
        return [
            "data"         => $this->collection,
            'total_amount' => $this->total_contribution,
            'total'          => $this->total,
            'last_page'      => $this->lastPage,
            'per_page'       => $this->perPage,
            'current_page'   => $this->currentPage,
            'total_balance'  => $this->total_balance,
            'unpaid_durations' => $this->unpaid_durations,
            'total_amount_payable' => $this->total_amount_payable,
        ];
    }
}
