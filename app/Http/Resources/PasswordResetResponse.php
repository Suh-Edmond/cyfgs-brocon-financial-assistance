<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PasswordResetResponse extends JsonResource
{
    private $email;
    private $user_id;

    public function __construct($email, $user_id)
    {
        parent::__construct(null);
        $this->user_id = $user_id;
        $this->email = $email;
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
            'email' => $this->email,
            'user_id' => $this->user_id
        ];
    }
}
