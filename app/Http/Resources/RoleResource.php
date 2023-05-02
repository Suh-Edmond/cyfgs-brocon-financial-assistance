<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Ramsey\Uuid\Uuid;

class RoleResource extends JsonResource
{
    private string $updated_by;

    public function __construct($resource, $updated_by)
    {
        parent::__construct($resource);
        $this->updated_by = $updated_by;
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
            'id'            => $this->id,
            'name'          => $this->name,
            'guard_name'    => $this->guard_name,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
            'updated_by'    => $this->updated_by
        ];
    }
}
