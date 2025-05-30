<?php

namespace App\Http\Resources;

use App\Constants\PaymentStatus;
use App\Traits\HelpTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenditureItemResource extends JsonResource
{
    use HelpTrait;

    private $total_amount_given;
    private $total_amount_spent;
    private $balance;

    public function __construct($resource, $total_amount_given = null, $total_amount_spent = null, $balance = null)
    {
        parent::__construct($resource);
        $this->total_amount_given = $total_amount_given;
        $this->total_amount_spent = $total_amount_spent;
        $this->balance = $balance;
    }


    public function toArray($request)
    {
        return [
            'id'                            => $this->id,
            'name'                          => $this->name,
            'amount'                        => $this->amount,
            'comment'                       => $this->comment,
            'venue'                         => $this->venue,
            'date'                          => $this->date,
            'approve'                       => $this->approve,
            'expenditure_category'          => $this->expenditureCategory,
            'created_at'                    => $this->created_at,
            'updated_at'                    => $this->updated_at,
//            'expenditure_details'            => ($this->expenditureDetails),
            'total_amount_given'            => $this->total_amount_given,
            'total_amount_spent'            => $this->total_amount_spent,
            'total_balance'                 => $this->balance,
            'updated_by'                    => $this->updated_by,
            'payment_item'                  => $this->paymentItem,
            'session'                       => $this->session,
            'has_no_pending_details'        => count($this->expenditureDetails) > 0 && is_null($this->checkExpenditureItemCanBeApproveDeclined($this->expenditureDetails)),
            'expenditureItem'               => $this->expenditureItem
        ];
    }
}
