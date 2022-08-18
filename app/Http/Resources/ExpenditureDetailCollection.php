<?php

namespace App\Http\Resources;

use App\Traits\ResponseTrait;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ExpenditureDetailCollection extends ResourceCollection
{
    use ResponseTrait;

    private $total_item_amount;
    private $total_amount_given;
    private $total_amount_spent;
    private $balance;

    public function __construct($collection, $total_item_amount, $total_amount_given, $total_amount_spent, $balance)
    {
      parent::__construct($collection);
      $this->total_item_amount  = $total_item_amount;
      $this->total_amount_given = $total_amount_given;
      $this->total_amount_spent = $total_amount_spent;
      $this->balance            = $balance;
    }

    public function toArray($request)
    {
        return [
            'data'                          => $this->collection,
            'total_item_amount'             => $this->total_item_amount,
            'total_amount_given'            => $this->total_amount_given,
            'total_amount_spent'            => $this->total_amount_spent,
            'total_balance'                 => $this->balance,
        ];
    }
}
