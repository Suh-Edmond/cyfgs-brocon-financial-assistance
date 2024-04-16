<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberContributionResource extends JsonResource
{
   private $id;
   private $amount;
   private $year;
   private $name;
   public function __construct($id, $name = "Member's Contributions", $amount, $year)
   {
       parent::__construct(null);
       $this->id = $id;
       $this->name = $name;
       $this->amount = $amount;
       $this->year = $year;
   }

    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'amount'    => !is_null($this->amount) ? $this->amount : 0,
            'year'      => $this->year
        ];
    }
}
