<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Crypt;
use Ramsey\Uuid\Uuid;

class UserResource extends JsonResource
{
    private $token;
    private $hasLoginBefore;

    public function __construct($resource, $token = null, $hasLoginBefore = null)
    {
        parent::__construct($resource);
        $this->token = $token;
        $this->hasLoginBefore = $hasLoginBefore;
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'email'          => $this->email,
            'telephone'      => $this->telephone,
            'address'        => $this->address,
            'occupation'     => $this->occupation,
            'gender'         => $this->gender,
            'organsation_id' => optional($this->organisation->id),
            'organsation_name' => optional($this->organisation->name),
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
            'roles'          => RoleResource::collection($this->roles),
            'token'          => $this->token,
            'hasLoginBefore' => $this->hasLoginBefore
        ];
    }
}
