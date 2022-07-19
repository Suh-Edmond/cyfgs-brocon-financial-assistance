<?php

namespace App\Models;

use App\Traits\GenerateUuid;
use Illuminate\Database\Eloquent\Model;

class ExpenditureCategory extends Model
{
    use GenerateUuid;

    protected $fillable = [
        'name',
        'description',
        'organisation_id'
    ];

    public $incrementing = false;
    public $keyType = 'string';
    public $primaryKey = 'uuid';

    public function expenditureItem(){
        return $this->hasMany(ExpenditureItem::class);
    }

    public function organisation() {
        return $this->belongsTo(Organisation::class);
    }
}
