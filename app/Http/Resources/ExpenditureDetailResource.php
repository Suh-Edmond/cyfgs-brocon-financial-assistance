<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpenditureDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                            => $this->id,
            'amount_given'                  => $this->amount_given,
            'amount_spent'                  => $this->amount_spent,
            'comment'                       => $this->comment,
            'approve'                       => $this->approve,
            'created_at'                    => $this->created_at,
            'updated_at'                    => $this->updated_at,
            'updated_by'                    => $this->updated_by,
            'expenditure_item_id'           => $this->expenditureItem->id,
        ];
    }
}
