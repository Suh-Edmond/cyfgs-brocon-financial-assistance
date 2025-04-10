<?php

namespace App\Models;

use App\Traits\GenerateUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivitySupport extends Model
{
    use HasFactory;
    use GenerateUuid;

    protected $primaryKey = 'id';
    public $incrementing  = false;
    protected $keyType    = 'string';


    protected $fillable = [
        'amount_deposited',
        'comment',
        'supporter',
        'payment_item_id',
        'code',
        'scan_picture',
        'updated_by',
        'session_id'
    ];

    protected $with = [
        'session'
    ];

    public function paymentItem()
    {
        return $this->belongsTo(PaymentItem::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function transactionHistory()
    {
        return $this->hasOne(TransactionHistory::class, 'reference_data');
    }
}
