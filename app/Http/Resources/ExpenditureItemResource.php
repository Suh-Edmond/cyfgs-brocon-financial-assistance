<?php

namespace App\Http\Resources;

use App\Traits\ResponseTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenditureItemResource extends JsonResource
{
    use ResponseTrait;


    public function toArray($request)
    {
        return [
            'id'                            => $this->id,
            'name'                          => $this->name,
            'amount'                        => $this->amount,
            'comment'                       => $this->comment,
            'venue'                         => $this->venue,
            'date'                          => $this->date,
            'approve'                       => ResponseTrait::convertBooleanValue($this->approve),
            'expenditure_category_id'       => $this->expenditureCategory->id,
            'created_at'                    => $this->created_at,
            'updated_at'                    => $this->updated_at,
            'updated_by'                    => $this->updated_by,
            'expendiure_details'            => $this->expenditureDetail
        ];
    }
}
