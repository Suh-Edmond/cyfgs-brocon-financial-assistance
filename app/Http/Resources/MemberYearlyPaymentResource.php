<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberYearlyPaymentResource extends JsonResource
{
   private $memberPaymentItemResource;
   private $total_year_contribution;
   private $total_yearly_balance;
   private $member_info_resource;

   private $expected_amount;

   public function __construct($resource, $memberPaymentItemResource, $total_year_contribution, $total_yearly_balance, $member_info_resource, $expected_amount)
   {
       parent::__construct($resource);
       $this->memberPaymentItemResource = $memberPaymentItemResource;
       $this->total_year_contribution = $total_year_contribution;
       $this->total_yearly_balance = $total_yearly_balance;
       $this->member_info_resource = $member_info_resource;
       $this->expected_amount = $expected_amount;
   }

    public function toArray($request)
    {
        return [
            'contributions' => $this->memberPaymentItemResource,
            'total_contribution' => $this->total_year_contribution,
            'total_balance'      => $this->total_yearly_balance,
            'member_info'        => $this->member_info_resource,
            'expected_amount'    => $this->expected_amount
        ];
    }
}
