<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\GenerateUuid;

class PaymentItem extends Model
{

    protected $fillable = [
        'name',
        'amount',
        'complusory',
        'payment_category_id'
    ];


    public function paymentCategory(){
        return $this->belongsTo(PaymentCategory::class);
    }

    public function userPayment() {
        return $this->hasMany(UserPayment::class);
    }
}
