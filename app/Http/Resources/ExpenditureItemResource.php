<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpenditureItemResource extends JsonResource
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
            'name'                          => $this->name,
            'amount'                        => $this->amount,
            'comment'                       => $this->comment,
            'venue'                         => $this->venue,
            'date'                          => $this->date,
            'approve'                       => $this->approve,
            'expenditure_category_id'       => $this->expenditureCategory->id,
            'expenditure_category_name'     => $this->expenditureCategory->name,
            'created_at'                    => $this->created_at,
            'updated_at'                    => $this->updated_at,
            'updated_by'                    => $this->updated_by,
        ];
    }
}
