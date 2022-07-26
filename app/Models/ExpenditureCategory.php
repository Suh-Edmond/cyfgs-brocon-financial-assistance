<?php

namespace App\Models;

use App\Traits\GenerateUuid;
use Illuminate\Database\Eloquent\Model;

class ExpenditureCategory extends Model
{


    protected $fillable = [
        'name',
        'description',
        'organisation_id'
    ];



    public function expenditureItem(){
        return $this->hasMany(ExpenditureItem::class);
    }

    public function organisation() {
        return $this->belongsTo(Organisation::class);
    }
}
