<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\GenerateUuid;

class UserContribution extends Model
{
    use GenerateUuid;
    protected $fillable = [
        'amount_deposited',
        'comment',
        'scan_picture',
        'status',
        'approve',
        'user_id',
        'payment_item_id'
    ];

    public $incrementing = false;
   public $keyType = 'string';
   public $primaryKey = 'uuid';

   public function user() {
        return $this->belongsTo(User::class);
   }


   public function paymentItem() {
    return $this->belongsTo(PaymentItem::class);
   }
}
