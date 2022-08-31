<?php

namespace App\Models;

use App\Traits\GenerateUuid;
use Illuminate\Database\Eloquent\Model;

class ExpenditureItem extends Model
{
    use GenerateUuid;

    protected $primaryKey = 'id';
    public $incrementing  = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'name',
        'amount',
        'comment',
        'venue',
        'date',
        'expenditure_category_id',
        'scan_picture'
    ];

    public function expendiureDetails() {
        return $this->hasMany(ExpenditureDetail::class);
    }

    public function expenditureCategory()
    {
        return $this->belongsTo(ExpenditureCategory::class);
    }
}
