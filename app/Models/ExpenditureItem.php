<?php

namespace App\Models;

use App\Traits\GenerateUuid;
use Illuminate\Database\Eloquent\Model;

class ExpenditureItem extends Model
{

    protected $fillable = [
        'name',
        'amount',
        'comment',
        'approve',
        'venue',
        'date',
        'expenditure_category_id'
    ];



    public function expenditureDetail() {
        return $this->hasMany(ExpenditureDetail::class);
    }


    public function expenditureCategory()
    {
        return $this->belongsTo(ExpenditureCategory::class);
    }
}
