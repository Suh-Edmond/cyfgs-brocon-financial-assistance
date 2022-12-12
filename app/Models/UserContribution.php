<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\GenerateUuid;

class UserContribution extends Model
{
    use GenerateUuid;

    protected $primaryKey = 'id';
    public $incrementing  = false;
    protected $keyType    = 'string';


    protected $fillable = [
        'amount_deposited',
        'comment',
        'status',
        'user_id',
        'payment_item_id',
        'code',
        'scan_picture',
        'updated_by'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paymentItem()
    {
        return $this->belongsTo(PaymentItem::class);
    }



}
