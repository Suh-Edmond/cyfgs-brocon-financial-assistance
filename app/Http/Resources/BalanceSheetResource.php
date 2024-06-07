<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BalanceSheetResource extends JsonResource
{
    private $session;
    private $member_yearly_payment_resource;
    private $total_yearly_contribution;
    private $total_yearly_balance;
    private $total_year_expected_amount;

    public function __construct($resource, $member_yearly_payment_resource, $total_year_expected_amount,  $total_yearly_contribution, $total_yearly_balance, $session)
    {
        parent::__construct($resource);
        $this->total_yearly_contribution = $total_yearly_contribution;
        $this->total_yearly_balance = $total_yearly_balance;
        $this->session = $session;
        $this->member_yearly_payment_resource = $member_yearly_payment_resource;
        $this->total_year_expected_amount = $total_year_expected_amount;
    }


    public function toArray($request)
    {
        return [
            'session' => $this->session,
            'total_yearly_contribution'   => $this->total_yearly_contribution,
            'total_yearly_balance' => $this->total_yearly_balance,
            'total_year_expected_amount' => $this->total_year_expected_amount,
            'members_contributions' => $this->member_yearly_payment_resource
        ];
    }
}
