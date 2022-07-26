<?php

namespace App\Models;

use App\Traits\GenerateUuid;
use Illuminate\Database\Eloquent\Model;

class ExpenditureItem extends Model
{

    protected $fillable = [
        'name',
        'amount',
        'scan_picture',
        'comment',
        'approve',
        'venue',
        'expenditure_category_id'
    ];



    public function expenditureDetail() {
        return $this->hasMany(ExpenditureDetail::class);
    }
}
