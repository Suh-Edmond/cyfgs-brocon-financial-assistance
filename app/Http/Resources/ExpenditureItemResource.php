<?php

namespace App\Http\Resources;

use App\Traits\ResponseTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use Ramsey\Uuid\Uuid;

class ExpenditureItemResource extends JsonResource
{
    use ResponseTrait;

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
            'approve'                       => $this->convertBooleanValue($this->approve),
            'expenditure_category_id'       => $this->expenditureCategory->id,
            'created_at'                    => $this->created_at,
            'updated_at'                    => $this->updated_at,
            'expendiure_details'            => $this->generateResponseForExpenditureDetails($this->expendiureDetails),
            'total_amount_given'            => $this->total_amount_given,
            'total_amount_spent'            => $this->total_amount_spent,
            'total_balance'                 => $this->balance
        ];
    }
}
