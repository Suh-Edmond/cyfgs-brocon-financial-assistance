<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\GenerateUuid;

/**
 * @method static find(mixed $payment_item_id)
 */
class PaymentItem extends Model
{
    use GenerateUuid;

    protected $primaryKey = 'id';
    public $incrementing  = false;
    protected $keyType    = 'string';


    protected $fillable = [
        'name',
        'amount',
        'complusory',
        'payment_category_id',
        'description',
        'updated_by'
    ];

    public function paymentCategory(){
        return $this->belongsTo(PaymentCategory::class);
    }

    public function userPayment() {
        return $this->hasMany(UserPayment::class);
    }

}
