<?php

namespace App\Models;

use App\Traits\GenerateUuid;
use Illuminate\Database\Eloquent\Model;

class ExpenditureDetail extends Model
{
    use GenerateUuid;
    protected $fillable = [
        'amount_given',
        'amount_spent',
        'scan_picture',
        'name',
        'comment',
        'expenditure_item_id'
    ];

    public $incrementing = false;
    public $keyType = 'string';
    public $primaryKey = 'uuid';


    public function expenditureItem() {
        return $this->belongsTo(ExpenditureItem::class);
    }
}
