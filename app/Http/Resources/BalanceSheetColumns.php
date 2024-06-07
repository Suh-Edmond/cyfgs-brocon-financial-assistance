<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BalanceSheetColumns extends JsonResource
{

    private $code;
    private $name;

    private $id;
    private $amount;

    public function __construct($resource, $code, $name, $id, $amount)
    {
        parent::__construct($resource);
        $this->code = $code;
        $this->name = $name;
        $this->id = $id;
        $this->amount = $amount;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'code'  => $this->code,
            'name'  => $this->name,
            'id'    => $this->id,
            'amount' => $this->amount
        ];
    }
}
