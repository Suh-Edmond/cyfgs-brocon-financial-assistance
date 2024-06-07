<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BalanceSheetColumns extends JsonResource
{

    private $code;
    private $name;

    public function __construct($resource, $code, $name)
    {
        parent::__construct($resource);
        $this->code = $code;
        $this->name = $name;
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
            'name'  => $this->name
        ];
    }
}
