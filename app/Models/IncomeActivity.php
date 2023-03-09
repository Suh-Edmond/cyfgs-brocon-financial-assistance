<?php

namespace App\Models;

use App\Traits\GenerateUuid;
use Illuminate\Database\Eloquent\Model;

class IncomeActivity extends Model
{
    use GenerateUuid;

    protected $primaryKey = 'id';
    public $incrementing  = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'name',
        'description',
        'date',
        'amount',
        'venue',
        'organisation_id',
        'scan_picture',
        'updated_by',
        'payment_item_id'
    ];

    public function organisation() {
        return $this->belongsTo(Organisation::class);
    }

    public function paymentItem() {
        return $this->belongsTo(PaymentItem::class);
    }

}
