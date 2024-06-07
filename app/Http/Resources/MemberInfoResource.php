<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberInfoResource extends JsonResource
{
    private $id;
    private $name;
    private $email;
    public function __construct($resource, $id, $name, $email)
    {
        parent::__construct($resource);
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
    }

    public function toArray($request)
    {
        return [
            'id'     => $this->id,
            'name'   => $this->name,
            'email'  => $this->email
        ];
    }
}
