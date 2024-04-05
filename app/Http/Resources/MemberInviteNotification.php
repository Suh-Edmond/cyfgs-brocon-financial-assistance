<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberInviteNotification extends JsonResource
{
    private $id;
    private $name;
    private $role;
    private $date;

    public function __construct($id, $name, $role, $date)
    {
        $this->id   = $id;
        $this->role = $role;
        $this->date = $date;
        $this->name = $name;
    }

    public function toArray($request)
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'role'  => $this->role,
            'date'  => $this->date
        ];
    }
}
