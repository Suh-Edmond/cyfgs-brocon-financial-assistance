<?php

namespace App\Models;

use App\Traits\GenerateUuid;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static findOrFail($expenditure_category_id)
 */
class ExpenditureCategory extends Model
{
    use GenerateUuid;

    protected $primaryKey = 'id';
    public $incrementing  = false;
    protected $keyType    = 'string';


    protected $fillable = [
        'name',
        'description',
        'organisation_id',
        'updated_by'
    ];


    public function expenditureItem(){
        return $this->hasMany(ExpenditureItem::class);
    }

    public function organisation() {
        return $this->belongsTo(Organisation::class);
    }



}
