<?php

namespace App\Http\Resources;

use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use Ramsey\Uuid\Uuid;

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
            'approve'                       => $this->approve,
            'created_at'                    => $this->created_at,
            'updated_at'                    => $this->updated_at,
            'balance'                       => $this->balance,
            'updated_by'                    => $this->updated_by,
            'expenditure_item_id'           => $this->expenditure_item_id,
            'is_selected'                   => false,
            'expenditureDetail'             => $this->expenditureDetail
        ];
    }
}
