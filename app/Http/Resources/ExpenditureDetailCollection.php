<?php

namespace App\Http\Resources;

use App\Traits\ResponseTrait;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ExpenditureDetailCollection extends ResourceCollection
{
    use ResponseTrait;

    private $expenditure_item_name;
    private $total_item_amount;
    private $total_amount_given;
    private $total_amount_spent;
    private $balance;

    public function __construct($collection,$expenditure_item_name, $total_item_amount, $total_amount_given, $total_amount_spent, $balance)
    {
      parent::__construct($collection);
      $this->total_item_amount  = $total_item_amount;
      $this->total_amount_given = $total_amount_given;
      $this->total_amount_spent = $total_amount_spent;
      $this->balance            = $balance;
      $this->expenditure_item_name = $expenditure_item_name;
    }

    public function toArray($request)
    {
        return [
            'data'                          => $this->collection,
            'expenditure_item_name'         => $this->expenditure_item_name,
            'total_item_amount'             => $this->total_item_amount,
            'total_amount_given'            => $this->total_amount_given,
            'total_amount_spent'            => $this->total_amount_spent,
            'total_balance'                 => $this->balance,
        ];
    }
}
