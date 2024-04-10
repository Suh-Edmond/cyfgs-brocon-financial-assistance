<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberInviteNotification extends JsonResource
{
    private $id;
    private $name;
    private $role;
    private $date;

    private $has_seen_notification;

    public function __construct($id, $name, $role, $date, $has_seen_notification)
    {
        $this->id   = $id;
        $this->role = $role;
        $this->date = $date;
        $this->name = $name;
        $this->has_seen_notification = $has_seen_notification;
    }

    public function toArray($request)
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'role'  => $this->role,
            'date'  => $this->date,
            'has_seen_notification' => $this->has_seen_notification
        ];
    }
}
