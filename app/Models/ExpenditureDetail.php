<?php

namespace App\Models;

use App\Traits\GenerateUuid;
use Illuminate\Database\Eloquent\Model;

class ExpenditureDetail extends Model
{
    use GenerateUuid;

    protected $primaryKey = 'id';
    public $incrementing  = false;
    protected $keyType    = 'string';


    protected $fillable = [
        'amount_given',
        'amount_spent',
        'name',
        'comment',
        'expenditure_item_id',
        'scan_picture',
        'updated_by'
    ];

    public function expenditureItem() {
        return $this->belongsTo(ExpenditureItem::class);
    }

    public function transactionHistory()
    {
        return $this->hasOne(TransactionHistory::class, "reference_data");
    }
}
