<?php

namespace App\Models;

use App\Traits\GenerateUuid;
use Illuminate\Database\Eloquent\Model;

class ExpenditureDetail extends Model
{

    protected $fillable = [
        'amount_given',
        'amount_spent',
        'name',
        'comment',
        'aprove',
        'expenditure_item_id'
    ];




    public function expenditureItem() {
        return $this->belongsTo(ExpenditureItem::class);
    }
}
