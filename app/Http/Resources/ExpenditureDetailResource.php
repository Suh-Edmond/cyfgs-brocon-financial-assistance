<?php

namespace App\Http\Resources;

use App\Traits\ResponseTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenditureDetailResource extends JsonResource
{
    use ResponseTrait;
    private $balance;

    public function __construct($resource, $balance = null)
    {
        parent::__construct($resource);
        $this->balance = $balance;
    }


    public function toArray($request)
    {
        return [
            'id'                            => $this->id,
            'name'                          => $this->name,
            'amount_given'                  => $this->amount_given,
            'amount_spent'                  => $this->amount_spent,
            'comment'                       => $this->comment,
            'approve'                       => ResponseTrait::convertBooleanValue($this->approve),
            'created_at'                    => $this->created_at,
            'updated_at'                    => $this->updated_at,
            'expenditure_item_id'           => $this->expenditureItem->id,
            'balance'                       => $this->balance
        ];
    }
}
