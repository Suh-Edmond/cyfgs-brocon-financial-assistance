<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserContributionDTO extends JsonResource
{
    private $payment_item_name;
    private $payment_item_total_amount;
    private $payment_category_name;

    public function __construct($payment_item_name = null, $payment_item_total_amount = null, $payment_category_name = null)
    {
        $this->payment_item_name         = $payment_item_name;
        $this->payment_item_total_amount = $payment_item_total_amount;
        $this->payment_category_name     = $payment_category_name;
    }


    public function toArray($request)
    {
        return [
            'payment_item_name'             => $this->payment_item_name,
            'payment_item_total_amount'     => $this->payment_item_total_amount,
            'payment_category_name'         => $this->payment_category_name
        ];
    }
}
