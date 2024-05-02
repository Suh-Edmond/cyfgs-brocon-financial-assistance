<?php

namespace App\Http\Resources;

use Illuminate\Database\Eloquent\Model;

class ReferenceResource extends Model
{
    private $id;
    private $name;
    private $telephone;

    public function __construct($id, $name, $telephone)
    {
        $this->id = $id;
        $this->name = $name;
        $this->telephone = $telephone;
    }

}
