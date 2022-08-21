<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\GenerateUuid;

class UserContribution extends Model
{

    protected $fillable = [
        'amount_deposited',
        'comment',
        'status',
        'user_id',
        'payment_item_id',
        'code'
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
